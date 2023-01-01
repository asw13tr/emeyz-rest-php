<?php 

namespace Atabasch\Helpers;

class Hash extends \Atabasch\BaseHelper{

    private $options = [
        'memory_cost' => 1024,
        'time_cost' => 2,
        'threads' => 2  
    ];


    public function encrypt($value=null){
        $password = md5( sha1( base64_encode(base64_encode($value).'.'.base64_encode($_ENV['HASH_KEY'] ?? '')) ) );
    }


    public function create($value=null){
        return password_hash($value, PASSWORD_DEFAULT, $this->options);
    }

    public function verify($value=null, $hash=null){
        return password_verify($value, $hash);
    }


}

?>