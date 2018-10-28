<?php
namespace Core\Middleware;

use Core\Router\Route;
use Core\Http\Response;
use Core\Interfaces\RequestInterface;
use Core\Interfaces\ResponseInterface;
use Psr\Container\ContainerInterface;

class DispatcherMiddleware
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function __invoke(RequestInterface $request, callable $next)
    {
        $route = $request->getAttribute(Route::class);
        if ($route) {
            $callback = $route->getCallback();

            if (is_string($callback))
                $callback = $this->container->get($callback);
            if (is_array($callback) && is_string($callback[0])) {
                $callback[0] = $this->container->get($callback[0]);
            }
            $response = call_user_func_array($callback, [$request]);
            if (is_string($response)) {
                return new Response(200, [], $response);
            } elseif ($response instanceof ResponseInterface) {
                return $response;
            } else {
                if ($this->container->get("debug") != "production")
                    throw new \Exception('the response is unavailable');
                else
                    return false;
            }
        } else {
            if ($this->container->get("debug") )
                throw new \Exception('the response is unavailable');
            else
                return false;
        }
    }
}