<?php

namespace Core\Middleware;

use Core\Interfaces\RequestInterface;
use Core\Http\Response;

class NotFoundMiddleware{
    public function __invoke(RequestInterface $request,callable $next){
        return new Response(404, [], 'Error 404');
    }
}