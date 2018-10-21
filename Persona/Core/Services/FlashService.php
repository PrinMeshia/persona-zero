<?php

namespace Core\Services;

use Core\Interfaces\SessionInterface;


class FlashService{
    private $session;
    private $sessionKey = 'flash';
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    public function success(string $message){
        $this->setMessage('success',$message);
    }
    public function error(string $message){
        $this->setMessage('error',$message);
    }

    public function has(string $type){
        $flash = $this->session->get($this->sessionKey,[]);
        return array_key_exists($type,$flash);
    }
    public function get(string $type){
        $flash = $this->session->get($this->sessionKey,[]);
        $this->session->delete($this->sessionKey);
        if(array_key_exists($type,$flash)){
            return $flash[$type];
        }
        return null;
    }
    private function setMessage(string $action,string $message){
        $flash = $this->session->get($this->sessionKey,[]);
        $flash[$action] = $message;
        $this->session->set($this->sessionKey,$flash);
    }
}
