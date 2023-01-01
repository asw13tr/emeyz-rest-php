<?php
namespace Atabasch\Controllers;

class AdminController extends \Atabasch\BaseController{

   

    public function __construct(){
        parent::__construct();
        if(!$this->checkApiKey() || !$this->checkAccess()){
            $this->json([
                'message' => 'Geçersiz Erişim'
            ], false);
            return die();
        }
    }

    private function checkApiKey(){
        $headerKey = $_SERVER['HTTP_X_EMEYZ_KEY'] ?? 'notApiKeyHeader';
        $envKey = $_ENV['API_KEY'] ?? 'notApiKey';
        return $headerKey===$envKey;
    }

    private function checkAccess(){
        if($_ENV['DEV'] ?? false){
            $headerKey = $_SERVER['HTTP_X_DEV_KEY'] ?? 'notDevKeyHeader';
            $envKey = $_ENV['DEV_KEY'] ?? 'notDevKey';
            return ($headerKey===$envKey);
        }
        return true;
        
    }



    public function json($data = [], $status=true){
        $data = [
            'status' => $status,
            'data' => $data
        ];
        return parent::json($data);
    }

    protected function renderError(){
        return $this->json([
            'message' => 'Hatalı İstek'
        ], false);
    }
    
    
    
    
}

