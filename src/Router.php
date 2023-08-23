<?php

namespace App;

class Router {

  private $pathUri;
  private $routes = [];
  private $matches = [];
  private $paramsRegex = [];

  public function __construct(string $pathUri){
    $this->pathUri = trim($pathUri, "/");
  }

  public function get(string $path, \Closure $callable): self
  {
    $this->routes["GET"][] = [$path, $callable];
    return $this;
  }

  public function with(array $params)
  {
    foreach($params as $key => $regex){
      $this->paramsRegex[$key] = $regex;
    }

    return $this;
  }

  public function run()
  {
    if(!isset($this->routes[$_SERVER["REQUEST_METHOD"]])){
      throw new \Exception("Aucune routes existante !");
    }

    foreach($this->routes[$_SERVER["REQUEST_METHOD"]] as $route){
     if($this->url($route[0])) {
      return call_user_func_array($route[1], $this->matches);
     }

    $this->url($route[0]);
    }
  }

  private function paramsMatch($match){
    if(isset($this->paramsRegex[$match[1]])){
      return "(" . $this->paramsRegex[$match[1]] . ")";  
    }

    return "([^/]+)";
  }

  private function url(string $url): bool
  {
    $url = trim($url, "/");
    $path = preg_replace_callback("#:([\w]+)#", [$this, "paramsMatch"], $url);


    if(!preg_match("#^$path$#", $this->pathUri, $matches)){
      return false;
    }


    array_shift($matches);
    $this->matches = $matches;

    return true;

  }
}