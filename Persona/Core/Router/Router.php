<?php
namespace Core\Router;

use Core\Interfaces\RequestInterface;
use Helpers\Yaml;

/**
 * Class register and get routes
 */
class Router
{
    private $router;
    public function __construct()
    {
        $this->router = new RouteCollection();
    }
    /**
     * @param RequestInterface $request
     * @return Route|null
     */
    public function match(RequestInterface $request)
    {

        $result = $this->router->match($request);
        
        return $result ?? null;
    }
    /**
     * Retour path of the Route
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    public function generateUri(string $name, array $params = [])
    {
        return $this->router->find($name, $params);
    }

    /**
     * @param string $file
     */
    public function loadYaml($file, string $root)
    {
        $routes = (new Yaml())->load($file);

        foreach ($routes as $name => $route) {
            $methods = explode(",",$route['method']);
            foreach ($methods as $method) {
                $this->router->addRoutes(new Route($name, [$route["controller"], $route["action"]]), $method, $root . $route["path"]);
            }
        }
        
    }
}
