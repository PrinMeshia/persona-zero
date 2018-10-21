<?php
namespace Core\Middleware;

use Core\Interfaces\RequestInterface;

class MethodMiddleware
{
    public function __invoke(RequestInterface $request, callable $next)
    {
        $bodyRequest = $request->getRequest();
        if (array_key_exists('_method', $bodyRequest) && in_array($bodyRequest['_method'], ['PUT', 'DELETE'])) {
            $request->setMethod($bodyRequest['_method']);
        }
        return $next($request);
    }
}
