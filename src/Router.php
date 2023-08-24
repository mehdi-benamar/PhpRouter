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

  public function get(string $path, \Closure|string $callable): self
  {
    $this->method($path, $callable, "GET");
    return $this;
  }

  public function post(string $path, \Closure|string $callable): self
  {

    $this->method($path, $callable, "POST");
    return $this;
  }

  private function method(string $path, \Closure|string $callable, string $method)
  {
    if(is_string($callable)){
      $controllerMethod = explode("#", $callable);
      dump($controllerMethod);
      die();
    }

    $this->routes[$method][] = [$path, $callable];
  }

  public function with(array $params): self
  {
    foreach($params as $param => $regex){
      $this->paramsRegex[$param] = $regex;
    }

    return $this;
  }

  public function run()
  {
    if(!isset($this->routes[$_SERVER["REQUEST_METHOD"]])){
      throw new \Exception("Aucune méthode existante !");
    }

    foreach($this->routes[$_SERVER["REQUEST_METHOD"]] as $route){
     if($this->url($route[0])) {
      return call_user_func_array($route[1], $this->matches);
      }
    }
  }

  private function paramsMatch(array $match): string
  {
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