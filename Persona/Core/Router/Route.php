<?php
namespace Core\Router;

/**
 * Route
 */
class Route 
{
    /**
     *
     * @var string
     */
    private $name;
    /**
     *
     * @var callable
     */
    private $callback;
    /**
     *
     * @var array
     */
    private $parameters = [];
    /**
     * Undocumented function
     *
     * @param string $name
     * @param string/callable $callback
     * @param array $parameters
     */
    public function __construct(string $name,  $callback)
    {
        $this->name =$name;
        $this->callback = $callback;
    }
    /**
     * get route name
     *
     * @return string
     */
    public function getName():string{
        return $this->name;
    }
    /**
     * get callback
     *
     * @return string/callable
     */
    public function getCallback(){
        return $this->callback;
    }
    /**
     * Return url parameter
     *
     * @return array
     */
    public function getParams():array{
        return $this->parameters;
    }

    public function addParams(array $parameters){
        $this->parameters = $parameters;
    }

}

