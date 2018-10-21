<?php

namespace Core\Render;

use Core\Interfaces\RendererInterface;


class TwigRenderer implements RendererInterface{

    const EXTENSION = '.html.twig';
    private $loader;
    private $twig;
    
    public function __construct(\Twig_Loader_Filesystem $loader,\Twig_Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }
     /**
     * Add a path to load a view
     *
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, $path = null) : void{
        $this->loader->addPath($path,$namespace);
    }
    /**
     * Generate view
     * $path must be add with namespace add with function addpath()
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []) : string{
       return $this->twig->render($view.self::EXTENSION,$params);
    }
    /**
     * add global var for all view
     *
     * @param string $key
     * @param [type] $value
     * @return void
     */
    public function addGlobal(string $key,  $value) : void{
        $this->twig->addGlobal($key,  $value);
    }

    public function getTwig(){
        return $this->twig;
    }
}