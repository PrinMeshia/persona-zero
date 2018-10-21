<?php
namespace Core\Middleware;

use Core\Interfaces\SessionInterface;
use Core\Interfaces\RequestInterface;
use Core\Exceptions\CsrfException;

class CsrfMiddleware 
{
    private $formKey;
    private $sessionKey;
    private $session ;
    private $limit;

    public function __construct(SessionInterface &$session, int $limit = 50, string $formKey = "_personacsrf",string $sessionKey = "personacsrf")
    {
        $this->validSession($session);
        $this->session = &$session;
        $this->limit = $limit;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
    }
    public function __invoke(RequestInterface $request, callable $next)
    {
        if(in_array($request->getMethod(),['POST','PUT','DELETE'])){
            $params = $request->getRequest();
           
            if(!array_key_exists($this->formKey,$params)){
                $this->reject();
            }else{
               
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if(in_array($params[$this->formKey],$csrfList)){
                    $this->useToken($params[$this->formKey]);
                    return $next($request);
                }else{
                    $this->reject();
                }
            }
        }else{
            return $next($request);
        }
    }
    public function generateToken() :string{
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->limitToken();
        return $token;
    }
    private function reject(){
        throw new CsrfException();
    }
    private function useToken($token) : void{
        $tokens = array_filter($this->session[$this->sessionKey], function($t) use ($token){
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }
    private function limitToken(){
        $tokens = $this->session[$this->sessionKey] ?? [];
        if(count($tokens) > $this->limit){
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }
    private function validSession($session){
        if(!is_array($session) && !$session instanceof \ArrayAccess){
            throw new \TypeError("the session is not treatable like a table");
            
        }
    }

    /**
     * Get the value of formKey
     */ 
    public function getFormKey():string
    {
        return $this->formKey;
    }
}
