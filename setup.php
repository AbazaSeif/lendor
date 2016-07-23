<?php

// Start a session
session_start();

// Initialize the twig renderer
$loader = new Twig_Loader_Filesystem("templates");
$twig = new Twig_Environment($loader);

// Initialize slim with configuration
$app = new \Slim\Slim(array_merge($config["slim"], []));

// Eqloquent database capusle manager
$capsule = new \Illuminate\Database\Capsule\Manager;
// Add connection to the capsule
$capsule->addConnection($config["db"]);
// Set capsule as global
$capsule->setAsGlobal();
// Boot eloquent database
$capsule->bootEloquent();

// User migrate setup
User::migrate();
