<?php 
namespace Atabasch\Controllers;

use Atabasch\Helpers\Session;
use Atabasch\Helpers\Hash;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AccountController extends \Atabasch\BaseController{


    public function index(){
        return $this->json([
            'ses'   => $_SESSION,
            's' => session_id()
        ]); 
    }

    public function login(){
        if($this->hasRequestMethod("POST")){
            $username   = $this->post('username', null);
            $password   = $this->post('password', null);
            $level      = 3;
            $status     = 'active';
            if($username && $password){

                $sql = "SELECT id,name,email,password,fullname,level,status,slug FROM users WHERE name=? AND level=? AND status=? LIMIT 1";
                $user = $this->db()->queryOne($sql,[$username, $level, $status]);

                if($user){
                    $hash = new Hash();
                    if($hash->verify($password, $user->password)){
                        unset($user->password);
                        
                        $payload = [
                            'uid'       => $user->name,
                            'name'      => $user->name,
                            'fullname'  => $user->fullname,
                            'iat'       => time(),
                            'level'     => $user->level,
                        ];
                        $jwt = JWT::encode($payload, $ENV['API_KEY'] ?? 'emeyz', 'HS512');

                        $_SESSION['token'] = $jwt;
                        

                        return $this->json([
                            'status' => true,
                            'user' => $user,
                            'jwt'   => $jwt,
                            'ses'   => $_SESSION,
                            's' => session_id()
                        ]); 

                    }
                    
                }
            }
        }
        return $this->json([
            'status' => false,
            'user' => false
        ]);
    }


}

?>