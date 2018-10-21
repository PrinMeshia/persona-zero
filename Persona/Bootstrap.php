<?php
declare (strict_types = 1);

namespace Persona;
use DI\ContainerBuilder;
use Core\Interfaces\RequestInterface;
use Core\Interfaces\ResponseInterface;
use Core\Router\Router;
use Helpers\Yaml;

class Bootstrap
{
    /**
     * Container
     * @var ContainerInterface
     */
    private $container;
    /**
     * List of middleware
     *
     * @var array
     */
    private $middlewares = [];
    private $index = 0;
    public function __construct()
    {
        $this->setContainer();
    }
    /**
     * Undocumented function
     *
     * @param string $file
     * @return self
     */
    public function LoadRouteFile(string $file) : self
    {
        $this->container->get(Router::class)->loadYaml($file);
        return $this;
    }

    /**
     * Import app parameter
     *
     * @param string $path
     * @return self
     */
    public function loadParameter(string $path) : self
    {
        $yamlParser = new Yaml();
        $data = [];
        if (is_dir($path)) {
            $files = glob("$path/*", GLOB_BRACE);
            foreach ($files as $file) {
                $filedata = $yamlParser->load($file);
                $data = array_merge_recursive($data, $filedata);
            }
            foreach ($data as $name => $param) {
                $this->container->set($name, $param);
            }
        }
        return $this;
    }

    /**
     * Set Container
     *
     * @param ContainerInterface $container
     */
    private function setContainer()
    {
        $builder = new ContainerBuilder();
        $builder->writeProxiesToFile(true,'/stockage/tmp/proxies');
        $builder->addDefinitions(__DIR__."/Kernel.php");
        $this->container = $builder->build();
    }
    /**
     * Get Container
     *
     * @return ContainerInterface
     */
    public function getContainer() : ContainerInterface
    {
        return $this->container;
    }
    /**
     * Undocumented function
     *
     * @param string $middleware
     * @return self
     */
    public function pipe(string $middleware) : self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }
    /**
     * Undocumented function
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function listen(RequestInterface $request) : ResponseInterface
    {
        return $this->process($request);
    }
    public function process(RequestInterface $request) : ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception("no middleware intercepted this request", 1);
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'process']]);
        }
    }
    private function getMiddleware() : callable
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->index]);
            $this->index++;
            return $middleware;
        }
        return null;
    }

}