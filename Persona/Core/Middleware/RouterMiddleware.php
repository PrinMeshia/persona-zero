<?php

namespace Core\Middleware;

use Core\Http\Response;
use Core\Router\Router;
use Core\Interfaces\RequestInterface;

class RouterMiddleware{
    private $router;
    public function __construct(Router $Router)
    {
        $this->router = $Router;
    }
    public function __invoke(RequestInterface $request,callable $next){
        $route = $this->router->match($request);
        if (is_null($route)) {
            return $next($request);
        }
        $request->mergeParams($route->getParams());
        $request->mergeParams([get_class($route) => $route]);
        return $next($request);
    }
}