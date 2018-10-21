<?php
namespace Helpers\Traits;

use Core\Interfaces\ResponseInterface;
use Core\Http\Response;


trait RouterTrait {
    public function redirect(string $name, array $params = []):ResponseInterface
    {
        $redirectUrl = $this->router->generateUri($name,$params);
        return (new Response())
            ->setStatusCode(301)
            ->setHeader('Location',$redirectUrl);
            
    }
}