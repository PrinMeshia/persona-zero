<?php
declare (strict_types = 1);
namespace Core\Http;

use Core\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    protected $body;
    protected $headers;
    protected $statusCode;
    public function __construct(int $statusCode = 200, array $headers = [], ? string $body = null)
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }
    public function setStatusCode(int $statusCode) 
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    public function setHeader(string $header, string $value) 
    {
        $this->headers[$header] = $value;
        return $this;
    }
    public function setBody(string $body) 
    {
        $this->body = $body;
        return $this;
    }
    public function send()
    {
        header('HTTP/1.0 ' . $this->statusCode);
        foreach ($this->headers as $header => $value) {
            header(strtoupper($header) . ': ' . $value);
        }
        echo $this->body;
    }
}