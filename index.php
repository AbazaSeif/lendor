<?php

// Debugging - Remove in production
//ini_set("display_errors", 1);
//ini_set("display_startup_errors", 1);
//error_reporting(E_ALL);

// Dependencies
require "vendor/autoload.php";
require "config.php";
require "functions.php";
// Set up everything
require "setup.php";

// Usage aliases
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Views\TwigExtension as TwigExtension;
use \Slim\Views\Twig as Twig;

// Index route
$app->get("/", function ($req, $res, $args) {
    // Only allow users that are logged in
    if (!logged_in()) {
        // Redirect to the login page
        return $res->withHeader("Location", "/login");
    }
});

// Login route
$app->get("/login", function ($req, $res, $args) {
    return $this->view->render($res, "login.html", []);
});

// Run slim
$app->run();
