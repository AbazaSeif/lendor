<?php

// Debugging - Remove in production
//ini_set("display_errors", 1);
//ini_set("display_startup_errors", 1);
//error_reporting(E_ALL);

// Dependencies
require "vendor/autoload.php";
require "config.php";
require "functions.php";
require "setup.php";

// Usage aliases
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\Views\TwigExtension as TwigExtension;
use \Slim\Views\Twig as Twig;

// Index route
$app->get("/", function ($req, $res, $args) {
    // Only allow users that are logged in
    if (!($user = logged_in())) {
        // Redirect to the login page
        return $res->withHeader("Location", "/login");
    }
    // Draw the index page 
    return $this->view->render($res, "index.html", ["username" => $user->username]);
});

// Login route
// GET
$app->get("/login", function ($req, $res, $args) {
    // Simple render the page, no logic needed 
    return $this->view->render($res, "login.html", []);
});

// Login route
// POST
$app->post("/login", function ($req, $res, $args) {
    // Store POST parameters
    $params = $req->getParsedBody();
    // If user is logged in
    if (logged_in()) {
        // No need to log in twice, redirect to index
        return $res->withHeader("Location", "/");
    }
    // Store user or false on valid login check
    if ($user = valid_login($params)) {
        // On valid login, set user id token
        $_SESSION["user_id"] = $user->id;
        // Redirect to index
        return $res->withHeader("Location", "/");
    }
    // User not authenticated, redraw the login page
    return $this->view->render($res, "login.html", [
        "error" => "Invalid username and password combination"
    ]);
});

// Logout rout
$app->get("/logout", function ($req, $res, $args) {
    // Unset the user id token
    unset($_SESSION["user_id"]);
    // Redirect to the login page
    return $res->withHeader("Location", "/login");
});

// Run slim
$app->run();
