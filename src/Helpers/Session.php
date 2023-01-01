<?php 
namespace Atabasch\Helpers;

class Session extends \Atabasch\BaseHelper{

    public function has($key){
        return $_SESSION[$key] ?? false;
    }

    public function set($key, $value=null){
        $_SESSION[$key] = $value;
        return $this;
    }

    public function get($key, $default=null){
        return $_SESSION[$key] ?? $default;
    }


    public function setAll($items=[]){
        foreach($items as $key => $val){
            $this->set($key, $val);
        }
    }

    public function flash($key, $default=null){
        $result = $_SESSION[$key] ?? $default;
        $this->remove($key);
        return $result;
    }

    public function remove($key){
        if($this->has($key)){
            unset($_SESSION[$key]);
        }
    }

    public function clear(){
        session_destroy();
    }

}

?>