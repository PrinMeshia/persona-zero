<?php
declare(strict_types = 1); 
namespace Core\Interfaces;
interface RequestInterface
{
    public function __construct(array $getParams = [], array $postParams = [], array $serverParams = []);
    public static function createFromGlobals();
    public function getPathInfo();
    public function mergeParams($params = []);
    public function getRequest();
    public function getAttribute($name);
    public function setMethod(string $method);
    public function getUploadedFile();
}