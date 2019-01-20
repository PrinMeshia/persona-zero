<?php
declare (strict_types = 1);
namespace Controller\Admin;

use Core\Module\Controller;
use Core\Http\Request;
use Model\Category;

class CategoryController extends Controller{
    public function __construct(\Psr\Container\ContainerInterface $container, \Core\Interfaces\RendererInterface $renderer)
    {
        parent::__construct($container,$renderer);
        $this->category = $this->database->getManager(Category::class);
    }
    public function listAction(Request $request) : string
    {
 
        $items = $this->category->FindPaginated(1);
        $flash = $this->flash;
        return $this->renderer->render('@Admin/category/list', compact('items', 'flash'));
    }
    public function deleteAction(Request $request) 
    {
        $item = $this->category->findOneBy(["id" =>$request->getAttribute("id")]);
        $result = $this->category->remove($item);
        if ($result)
                $this->flash->success('The category has been successfully removed');
            else
                $this->flash->error('An error occured');
        return $this->redirect('admin.category');
    }
    public function createAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $this->getCategoryParams($request);
            $validator = $this->validate($request);
            if($validator->isValid()){
                $newCategory = (new Category())->hydrate($params);
                $this->category->persist($newCategory);
                $this->flash->success('The article has been successfully created');
                return $this->redirect('admin.category');
            }
            $errors = $validator->getErrors(); 
        }
        return $this->renderer->render('@Admin/category/create',compact('errors'));
    }
    public function editAction(Request $request)
    {
        $item = $this->category->findOneBy(["id" => intval($request->getAttribute("id"))]);
        if ($request->getMethod() === 'POST') {
            $params = $this->getCategoryParams($request);
            $validator = $this->validate($request);
            if($validator->isValid()){ 
                $item->setTitle($request->getAttribute("title"));
                $item->setSlug($request->getAttribute("slug"));
                $result = $this->category->persist($item);
                $this->flash->success('The category has been successfully edited');
                return $this->redirect('admin.category');
            }
            $errors = $validator->getErrors();
        }
        return $this->renderer->render('@Admin/category/edit', compact('item','errors'));
    }



    private function getCategoryParams(Request $request)
    {
        return array_filter($request->getRequest(), function ($key) {
            return in_array($key, ['title', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }
   
    private function validate(Request $request){
        return $this->getValidator($request)
            ->require('title','slug')
            ->length('title',5,255)
            ->length('slug',5,255)
            ->unique('slug',$this->category,$request->getAttribute('id'))
            ->slug('slug');
    }
    
}