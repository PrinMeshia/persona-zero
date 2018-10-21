<?php
namespace Core\Render;

use Core\Interfaces\RendererInterface;
use Core\Render\Template;
use Core\Router\Router;


class Renderer implements RendererInterface
{
    const DEFAULT_NAMESPACE = '__MAIN';
    const EXTENSION = '.tpl';
    private $_path = [];
    private $_globals = [];
    private $_router ;
    private $_defaultLayout = 'layout';

    /**
     * Undocumented function
     *
     * @param [type] $defaultPath
     * @param Router $router
     */
    public function __construct($defaultPath = null)
    {
        if (!is_null($defaultPath))
            $this->addPath($defaultPath);
    }
    /**
     * Add a path to load a view
     *
     * @param string $namespace
     * @param string|null $path
     * @return void
     */
    public function addPath(string $namespace, $path = null) : void
    {

        if (is_null($path)) {
            $this->_path[self::DEFAULT_NAMESPACE] = $namespace;
        } else
            $this->_path[$namespace] = $path;
    }
    /**
     * Generate view
     * $path must be add with namespace add with function addpath()
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []) : string
    {
        $path = $this->getLayout($view);
        $template = new Template();
        $template->setContent($path);
        return $template->render($this->getLayout($this->_defaultLayout),$params,$this->_globals);
    }
    /**
     * add global var for all view
     *
     * @param string $key
     * @param [type] $value
     * @return void
     */
    public function addGlobal(string $key, $value) : void
    {
        $this->_globals[$key] = $value;
    }
    /**
     * test if $view has a namespace
     *
     * @param string $view
     * @return boolean
     */
    private function hasNamespace(string $view) : bool
    {
        return $view[0] === '@';
    }
    /**
     * get namespace from the param view
     *
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view) : string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }
    /**
     * replace namespace from real namespace php
     *
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view) : string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->_path[$namespace], $view);
    }
    /**
     * get layout path
     *
     * @param string $view
     * @return string
     */
    private function getLayout(string $view) : string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . self::EXTENSION;
        } else {
            $path = $this->_path[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . self::EXTENSION;
        }
        return $path;
    }

    public function setLayout(string $layout){
        $this->_defaultLayout = $layout;
    }

}
