<?php

// Usage aliases
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Slim\Views\TwigExtension as TwigExtension;
use \Slim\Views\Twig as Twig;

// Start a session
session_start();

// Initialize slim with configuration
$app = new \Slim\App($config);
// Slim app container
$container = $app->getContainer();

$container["view"] = function ($container) {
    return new Twig("templates");
};

// Eqloquent database capusle manager
$capsule = new Capsule;
// Add connection to the capsule
$capsule->addConnection($config["db"]);
// Set capsule as global
$capsule->setAsGlobal();
// Boot eloquent database
$capsule->bootEloquent();

// User migrate setup
User::migrate();
