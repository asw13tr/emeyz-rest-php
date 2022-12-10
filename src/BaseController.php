<?php

namespace Atabasch;

class BaseController{

    private $postDatas = [];
    public function __construct(){

        $this->postDatas = count($_POST) > 0? $_POST : json_decode(file_get_contents('php://input'), true) ;

    }

    protected function json($data = []){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    protected function notFound($message=null){
        return $this->json([
            'status'    => false,
            'message'   => !$message? 'İçerik bulunamadı' : $message
        ]);
    }

    protected function db(){
        return new Database();
    }

    protected function get(){

    }

    protected function post($key=null, $default=null){
        if(!$key){
            return $this->postDatas;
        }
        return isset($this->postDatas[$key])? $this->postDatas[$key] : $default;
    }

    protected function hasRequestMethod($methods=["get"]){
        if(!is_array($methods)){
            $methods = explode(',', $methods);
        }
        $methods = array_map(function($i){
                                return strtoupper(trim($i));
                            }, $methods);
        return in_array(strtoupper($_SERVER['REQUEST_METHOD']), $methods);
    }

}
