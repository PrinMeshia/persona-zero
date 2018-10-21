<?php
declare (strict_types = 1);
namespace Core\Http;

use Core\Interfaces\RequestInterface;

class Request implements RequestInterface
{
    public $params = [];
    protected $server = [];
    private $method;
    public function __construct(array $getParams = [], array $postParams = [], array $serverParams = [])
    {
        $params = [];
        $this->params = array_merge($params, $getParams, $postParams);
        $this->server = $serverParams;
        $this->method = isset($this->server['REQUEST_METHOD']) ? $this->server['REQUEST_METHOD'] : 'GET';
    }
    public static function createFromGlobals() : Request
    {
        return new Request($_GET, $_POST, $_SERVER);
    }
    public function getPathInfo() : ? string
    {
        return ($this->server['REQUEST_URI'] ?? '');
    }
    public function mergeParams($params = [])
    {
        $this->params = array_merge($this->params, $params);
    }
    public function getRequest() : array
    {
        return $this->params;
    }
    public function getMethod() : string
    {
        return $this->method;
    }
    public function getAttribute($name)
    {
        return $this->params[$name] ?? null;
    }
    public function setMethod(string $method)
    {
        $this->method = $method;
    }
    public function getUploadedFile() : array
    {
        return $_FILES;
    }
}
