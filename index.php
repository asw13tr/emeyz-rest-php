<?php
require_once(__DIR__.'/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$namespace = "Atabasch";

$url        = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
$path       = trim(preg_replace('/^\//i', '', $_SERVER['REQUEST_URI']));



$pathParts  = explode("?", $path);
$pathParts  = explode('/', $pathParts[0]);

if(count($pathParts) < 2){
    $pathParts[1] = "index";
}

$className = ucfirst(!$pathParts[0]? 'main' : $pathParts[0]).'Controller';
$controllerName = "\\". $namespace ."\\Controllers\\" . $className ;
$methodName     = !$pathParts[1]? 'index' : $pathParts[1];

array_shift($pathParts);
array_shift($pathParts);

$controller = new $controllerName;
if(method_exists($controller, $methodName)){
    $runMethod  = [$controller, $methodName];
    call_user_func_array($runMethod, $pathParts);
}else{
    $runMethod  = [$controller, "index"];
    array_unshift($pathParts, $methodName);
    call_user_func_array($runMethod, $pathParts);
}

?>
