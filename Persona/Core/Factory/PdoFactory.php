<?php
namespace Core\Factory;

use Core\Database\PdoDB;
use Psr\Container\ContainerInterface;

class PdoFactory {
    public function __invoke(ContainerInterface $container):PdoDB
    {
        return new PdoDB($container->get('database'));
    }
}