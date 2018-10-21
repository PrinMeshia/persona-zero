<?php
declare(strict_types = 1); 
namespace Core\Interfaces;

interface PdoDBInterface
{
    public function getPdo();
    public function getManager($model);
}