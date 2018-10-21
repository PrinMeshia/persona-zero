<?php
    use function DI\factory;
    use function DI\autowire;
    return [
        "twig.extension" =>[
            \DI\get(\Core\Render\Extensions\TwigExtensions::class),
            \DI\get(\Core\Render\Extensions\TwigTextExtensions::class),
            \DI\get(\Core\Render\Extensions\TwigCsrfExtension::class)
        ],
        "view.path" => PUBLIC_PATH."/views",
        "cache.path" => dirname(PUBLIC_PATH)."/stockage/tmp//",
        Core\Interfaces\SessionInterface::class =>autowire(Core\Http\Session::class),
        Core\Middleware\CsrfMiddleware::class => autowire()->constructor(\DI\get(Core\Interfaces\SessionInterface::class)),
        Core\Router\Router::class => autowire(),
        Core\Interfaces\PdoDBInterface::class => factory(Core\Factory\PdoFactory::class),
        Core\Interfaces\RendererInterface::class => factory(Core\Factory\TwigRendererFactory::class),
       
    ];