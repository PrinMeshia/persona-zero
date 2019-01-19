<?php
declare (strict_types = 1);
namespace Controller\Admin;

use Core\Module\Controller;
use Core\Http\Request;
use Model\{Category,Post};
use Widget\AdminWidgetInterface;

class IndexController extends Controller{
    public function __construct(\Psr\Container\ContainerInterface $container, \Core\Interfaces\RendererInterface $renderer)
    {
        parent::__construct($container,$renderer);
        $this->post = $this->database->getManager(Post::class);
    }
    public function indexAction(Request $request){

        $count = $this->post->count("id");
        return $this->renderer->render('@Admin/dashboard',compact("count"));
    }
}