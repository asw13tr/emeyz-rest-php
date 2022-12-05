<?php

namespace Atabasch;

class BaseController{

    public function __construct(){

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
            return $_POST;
        }
        return isset($_POST[$key])? $_POST[$key] : $default;
    }

}
