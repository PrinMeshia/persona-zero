<?php
namespace Core\Middleware;

use Core\Http\Response;
use Core\Interfaces\RequestInterface;


class TraillingSlashMiddleware {
    public function __invoke(RequestInterface $request,callable $next){
        
        $uri = $request->getPathInfo();
        if (!empty($uri) && $uri[-1] === "//") {
            return (new Response())->redirect(substr($uri, 0, -1));
        }
        return $next($request);
    }
}