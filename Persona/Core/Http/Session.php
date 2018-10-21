<?php
namespace Core\Http;

use Core\Interfaces\SessionInterface;

class Session implements SessionInterface,\ArrayAccess{

    private function EnsureStarted() {
        if(session_status() === PHP_SESSION_NONE)
            session_start();
    }
     /**
     *
     * @param string $key
     * @param mixed $default
     * @return void
     */
    public function get(string $key,$default = null){
        $this->EnsureStarted();
        if(array_key_exists($key,$_SESSION)){
            return $_SESSION[$key];
        }
        return $default;
    }
    /**
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key,$value):void{
        $this->EnsureStarted();
        $_SESSION[$key]=$value;
    }
    /**
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key):void{
        $this->EnsureStarted();
        unset($_SESSION[$key]);
    }

    public function offsetExists($offset){
        $this->EnsureStarted();
        return array_key_exists($offset,$_SESSION);
    }
    public function offsetGet($offset){
        return $this->get($offset);
    }
    public function offsetSet($offset, $value){
        $this->set($offset, $value);
    }
    public function offsetUnset($offset){
        $this->delete($offset);
    }

}