<?php
declare(strict_types = 1); 
namespace Core\Interfaces;
interface ResponseInterface
{
    public function __construct(int $statusCode = 200, array $headers = [], ?string $body = null);
    public function setStatusCode(int $statusCode) ;
    public function setHeader(string $header, string $value) ;
    public function setBody(string $body) ;
    public function send();
}