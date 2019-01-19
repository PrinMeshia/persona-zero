<?php
declare (strict_types = 1);
namespace Controller\Admin;

use Core\Module\Controller;
use Core\Http\Request;
use Model\Post;
use Model\Category;

class PostController extends Controller{
    public function __construct(\Psr\Container\ContainerInterface $container, \Core\Interfaces\RendererInterface $renderer)
    {
        parent::__construct($container,$renderer);
        $this->post = $this->database->getManager(Post::class);
        $this->category = $this->database->getManager(Category::class);
    }
    public function listAction(Request $request) : string
    {
        $items = $this->post->FindPaginated(1);
        foreach ($items as $key => $item) {
           dd($item->getCategory());
        }
        $flash = $this->flash;
        return $this->renderer->render('@Admin/post/list', compact('items', 'flash'));
    }
    public function deleteAction(Request $request) 
    {
        $item = $this->post->findOneBy(["id" =>$request->getAttribute("id")]);
        $result = $this->post->remove($item);
        if ($result)
                $this->flash->success('The post has been successfully removed');
            else
                $this->flash->error('An error occured');
        return $this->redirect('admin.post');
    }
    public function createAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
           
            $params = $this->getPostParams($request);
           
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $validator = $this->validate($request);
            if ($validator->isValid()) {
                $newPost = (new Post())->hydrate($params);
                $this->post->persist($newPost);
                $this->flash->success('The article has been successfully created');
                return $this->redirect('admin.post');
            }

            $errors = $validator->getErrors();
        }
        $categoriesList = $this->category->findList(['id', 'title']);
        return $this->renderer->render('@Admin/post/create', compact('errors', 'categoriesList'));
    }
    public function editAction(Request $request)
    {
        $item = $this->post->findOneBy(["id" =>$request->getAttribute("id")]);
        if ($request->getMethod() === 'POST') {
            $validator = $this->validate($request);
            if ($validator->isValid()) {
                $item->hydrate($this->getPostParams($request));
                $this->post->persist($item);
                $this->flash->success('The article has been successfully edited');
                return $this->redirect('admin.posts');
            }
            $errors = $validator->getErrors();
        }
        $categoriesList = $this->category->findList(['id', 'title']);
        return $this->renderer->render('@Admin/post/edit', compact('item', 'errors', 'categoriesList'));
    }



    private function getPostParams(Request $request)
    {
        return array_filter($request->getRequest(), function ($key) {
            return in_array($key, ['title', 'slug','content', 'category']);
        }, ARRAY_FILTER_USE_KEY);
    }
   
    private function validate(Request $request){
        return $this->getValidator($request)
        ->require('title', 'slug', 'content', 'category')
        ->length('content', 25)
        ->length('title', 5, 255)
        ->length('slug', 5, 255)
        ->unique('slug',$this->post,$request->getAttribute('id'))
        ->exists('category', $this->category)
        ->slug('slug');
    }
    
}