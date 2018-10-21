<?php
namespace Core\Router;

use Core\Interfaces\RequestInterface;

class RouteCollection
{
    /**
     *
     * @var array
     */
    private $routes = [];
    private $regexStartLine = '^';
    private $regexEndLine = '$';
    private $_METHOD = ["GET","POST","PUT","DELETE","UPDATE"];
    public function __construct()
    {
    }
    /**
     * save route object in array
     *
     * @param Route $route
     * @param string $method
     * @param string $path
     * @return void
     */
    public function addRoutes(Route $route, $method, string $path)
    {
        if(is_array($method))
            foreach ($method as $type) {
                $this->routes[strtoupper($type)][$path] = $route;
            }
        else
            $this->routes[strtoupper($method)][$path] = $route;
    }
    /**
     * Search route from current url
     *
     * @param RequestInterface $request
     * @return Route|null
     */
    public function match(RequestInterface $request)
    {
        $slugs = [];
        $method = $request->getMethod();
        if (isset($this->routes[$method])) {
            
            foreach ($this->routes[$method] as $path => $route) {
                if ($this->processUri($path, $slugs, $request)) {
                    $route->addParams($slugs);
                    return $route;
                }
            }
        }
        return null;
    }
    private function processUri($path, array &$slugs, RequestInterface $request) : bool
    {

        $url = $request->getPathInfo();

        $uri = parse_url($url, PHP_URL_PATH);
        $func = $this->matchUriWithRoute($uri, $path, $slugs);
        
        return $func ? $func : false;
    }
    private function matchUriWithRoute(string $uri, string $path, array &$slugs) : bool
    {
        $uriSeg = preg_split('/[\/]+/', $uri, null, PREG_SPLIT_NO_EMPTY);
        $pathSeg = preg_split('/[\/]+/', $path, null, PREG_SPLIT_NO_EMPTY);
        if (self::compareSegments($uriSeg, $pathSeg, $slugs)) {
            return true;
        }
        return false;
    }
    private function CompareSegments(array $uriSeg, array $pathSeg, array &$slugs) : bool
    {
        if (count($uriSeg) != count($pathSeg)) return false;
        foreach ($uriSeg as $segIndex => $segment) {
            $segPath = $pathSeg[$segIndex];

            $is_slug = preg_match('/^{[^\/]*}$/', $segPath) || preg_match('/^:[^\/]*/', $segPath, $matches);

            if ($is_slug) {
                if (strlen(trim($segment)) === 0) {
                    return false;
                }
                preg_match_all('/{[^{}]+}/m', $segPath, $matches, PREG_SET_ORDER, 0);
                $endline = '';
                $startline = $this->regexStartLine;
                foreach ($matches as $key => $match) {
                    if($key+1 == sizeof($matches))
                        $endline = $this->regexEndLine;
                    if($key != 0)
                        $startline = '';
                    $part = explode(":", str_ireplace(['{', '}'], '', $match[0]));
                    $regex = $startline.$part[1] .$endline;
                    preg_match('/' . $regex. '/', $segment, $element);
                    if (sizeof($element)> 0) {
                        foreach ($slugs as $id =>$item) {
                            $slugs[$id] = str_replace( $element[0],'',$item);
                        }
                        $slugs[$part[0]] = $element[0];
                    }
                }
                if (sizeof($matches) != sizeof($slugs))
                    return false;

            } else if ($segPath !== $segment && $is_slug !== 1)
                return false;
        }
        return true;
    }
    private function findByMethod(string $name)
    {
        foreach ($this->_METHOD as $method) {
            if (array_key_exists($method, $this->routes)) {
                foreach ($this->routes[$method] as $key => $value) {
                    if ($value->getName() == $name) {
                        return $key;
                    }
                }
            }
        }
        return null;
    }
    public function find(string $name, array $params = [])
    {
        $path = $this->findByMethod($name);
        if (is_null($path)) {
            return null;
        }
        foreach ($params as $key => $value) {
            $path = preg_replace('/{'.$key.':(.*?)}/m', $value, $path);
        }
        return $path;
    }

}
