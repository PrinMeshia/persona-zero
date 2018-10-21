<?php

namespace Core\Module;


use Core\Router\Router;
use Helpers\Traits\RouterTrait;
use Core\Services\FlashService;
use Core\Interfaces\{RequestInterface,PdoDBInterface,RendererInterface};
use Core\Http\{Request,Response};
use Psr\Container\ContainerInterface;

class Controller {
    use \Helpers\Traits\RouterTrait;
    /**
     *
     * @var Router
     */
    protected $router;
    /**
     *
     * @var string
     */
    protected $flash;
    protected $database ;
    protected $renderer;
    public function __construct(ContainerInterface $container,RendererInterface $renderer){
        $this->database = $container->get(PdoDBInterface::class);
        $this->router = $container->get(Router::class);
        $this->flash = $container->get(FlashService::class);
        $this->renderer = $renderer;
        $parts = explode('\\',get_class($this));
        $className = get_class($this);
        $folder = $parts[1];
        $this->renderer->addPath($parts[1], dirname(PUBLIC_PATH). "/Src/Views/".$parts[1]."/");
    }
    /**
     * prepare 
     *
     * @param [type] $data
     * @return void
     */
    protected function toJson($request,$data) : Response
    {

        $callback = $request->getAttribute("callback");
        $data = json_encode($data);
        if ($callback && $callback != ''){
            $data = $callback .'('.$data.')';
        }
        return new Response(200,["Content-type" => "application/json"],$data);
    }
    protected function getModel($model){
        return $this->database->getManager($model);
    }
    
}