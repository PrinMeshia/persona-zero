<?php
namespace Core\Factory;

use Core\Router\Router;
use Psr\Container\ContainerInterface;
use Core\Render\TwigRenderer;
use Core\Render\TwigExtensions;

class TwigRendererFactory {
    public function __invoke(ContainerInterface $container):TwigRenderer
    {
        $debug = $container->get('system')['env'] !== 'production';
        $viewPath = $container->get('view.path');
        $loader = new \Twig_Loader_Filesystem($viewPath);
        $twig = new \Twig_Environment($loader,[
            'debug' => $debug,
            'cache' => $debug ? false : $container->get("cache.path").'views',
            'auto_reload' => $debug
        ]);
        if($container->has('twig.extension')){
            foreach ($container->get('twig.extension') as $extension) {
                $twig->addExtension($extension);
            }
        }
       
        return new TwigRenderer($loader,$twig);
    }
}