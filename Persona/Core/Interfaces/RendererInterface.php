<?php
namespace Core\Interfaces;

interface RendererInterface
{

    /**
     * Add a path to load a view
     *
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, $path = null) : void;
    /**
     * Generate view
     * $path must be add with namespace add with function addpath()
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []) : string;
    /**
     * add global var for all view
     *
     * @param string $key
     * @param [type] $value
     * @return void
     */
    public function addGlobal(string $key,  $value) : void;
    

}
