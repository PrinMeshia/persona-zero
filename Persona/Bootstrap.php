<?php
declare (strict_types = 1);

namespace Persona;
use DI\ContainerBuilder;
use Core\Interfaces\RequestInterface;
use Core\Interfaces\ResponseInterface;
use Core\Router\Router;
use Helpers\Yaml;
use Helpers\Server;

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
        $this->container->get(Router::class)->loadYaml($file,$this->container->get("rootfolder"));
        return $this;
    }
    /**
     * Current Environment
     *
     * @param string $path
     * @return string
     */
    private function GetEnvironmentFile(string $path): string{
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $content = json_decode($content, true);
            foreach ($content as $key => $value) {
                foreach ($value as $arrayAddress) {
                    if(array_search(Server::getUrlServer(),$arrayAddress) !== false || array_search(Server::getAddressServer(false),$arrayAddress) !== false){
                        return $key;
                    }
                }
            }
        }
    }
    /**
     * Import app parameter
     *
     * @param string $path
     * @return self
     */
    public function loadParameter(string $path) : self
    {
        
        $data = [];
        if (is_dir($path)) {
            $files = array_values( preg_grep( '/^((?!env.json).)*$/', glob("$path/*.json") ) );
            $files[] = $path."Environment/".$this->GetEnvironmentFile($path."env.json").".json";
            //$files = glob("$path/*.json", GLOB_BRACE);
            foreach ($files as $file) {
                $tempsdata = new \StdClass();
                $content = file_get_contents($file);
                $this->loadData($tempsdata,json_decode($content,true));
                $data = array_merge_recursive($data,$this->objectToArray($tempsdata));
            }
            foreach ($data as $name => $param) {
                $this->container->set($name, $param);
            }
        }

        return $this;
    }
    private function loadData(object &$data,array $array)
    {
        foreach ($array as $name => $value) {
            if (is_array($value)) {
                $this->setChildConfig($name,$value,$data);
            } else {
                $data->{$name} = $value;
            }
            if (is_object($data->{$name}))
            $this->preprocessing($data->{$name},$data);
        }
    }
    private function setChildConfig($name,array $array,object &$data)
    {
        $json = json_encode($array);
        $object = json_decode($json);
        if(isset($data->{$name})){
            foreach ($object as $key => $value) {
                $data->{$name}->{$key} = $value;
            }
        }else
        $data->{$name} = $object;
    }
    private function preprocessing($values, object &$data)
    {
        foreach ($values as $key => $value) {
            if (is_object($values->$key)) {
                $this->preprocessing($values->$key);
            } else if (!is_array($values->$key)) {
                if (is_string($values->$key) &&  preg_match('#{([\w\.]*)}#', $values->$key, $m)) {
                    $name = $m[1];
                    $val = $data;
                    foreach (explode('.', $name) as $k => $attr) {
                        if ($val->$attr)
                            $val = $val->$attr;
                    }
                    if (is_scalar($val)) {
                        $values->$key = str_replace('{' . $name . '}', $val, $values->$key);
                    } else {
                        trigger_error('Undefined var "' . $name . '" in the configuration file');
                    }
                }
            }
        }
    }
    private function objectToArray($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }
		
        if (is_array($d)) {
            return array_map([$this, __FUNCTION__], $d);
        }
        else {
            return $d;
        }
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
        $builder->addDefinitions(dirname(__DIR__)."/Config/Kernel.php");
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