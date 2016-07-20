<?php

// Debugging - Remove in production
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Require composer dependencies
require "vendor/autoload.php";
// Require the configuration
require "config.php";
// Require the setup
require "setup.php";

// Usage aliases
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Initialize slim with configuration
$app = new \Slim\App(["settings" => $config]);

// Index route
$app->get("/", function (Request $request, Response $response) {
    $response->getBody()->write("Index");
    return $response;
});

// Run slim
$app->run();
