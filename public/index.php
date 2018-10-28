<?php 
declare(strict_types = 1); 
use Persona\Bootstrap;
use Core\Http\Request;
use Core\Middleware\TraillingSlashMiddleware;
use Core\Middleware\MethodMiddleware;
use Core\Middleware\CsrfMiddleware;
use Core\Middleware\RouterMiddleware;
use Core\Middleware\NotFoundMiddleware;
use Core\Middleware\DispatcherMiddleware;


// error_reporting(E_ALL);
// ini_set('display_errors', '1');

chdir(dirname(__dir__));;
define("PUBLIC_PATH", __DIR__);

include dirname(__DIR__).'/vendor/autoload.php';
/**
 * 
 * Without Composer, use thse line
 * 
 * loader = new PackageLoader\PackageLoader();
 * $loader->load(dirname(__DIR__));
 */


$app = (new Bootstrap())
        ->pipe(TraillingSlashMiddleware::class)
        ->pipe(MethodMiddleware::class)
        ->pipe(CsrfMiddleware::class)
        ->pipe(RouterMiddleware::class)
        ->pipe(DispatcherMiddleware::class)
        ->pipe(NotFoundMiddleware::class)
        ->loadParameter(dirname(__DIR__).'/Config/System/')
        ->LoadRouteFile(dirname(__DIR__).'/Config/route.yml');

if (php_sapi_name() !== "cli") {
    $response = $app->listen(Request::createFromGlobals());
    $response->send();
}
