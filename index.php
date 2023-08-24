<?php

use App\Router;


require "./vendor/autoload.php";

$route = new Router($_SERVER['REQUEST_URI']);

$route->get("/", function(){ echo "bienvenue sur la page d'accueil";});
$route->get("/article/:id-:slug", function($slug, $id){ echo "voici l'article numéro $id et le slug $slug ";})
      ->with(["id" => "[0-9]+", "slug" => "[\w]+"]);
$route->get("/article", function(){ echo "voici tous les articles";});

$route->get("/contact", "Contact#welcome");

$route->run();
