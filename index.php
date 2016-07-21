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
    // Render the index page
    return $this->view->render($res, "index.html", [
        "role" => $user->role,
        "username" => $user->username
    ]);
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

$app->get("/profile/{username}", function ($req, $res, $args) {
    // Get the url given username
    $username = $req->getAttribute("username");
    // Only allow users that are logged in
    if (!($user = logged_in())) {
        // Redirect to the login page
        return $res->withHeader("Location", "/login");
    }
    try {
        // Obtain the user profile
        $profile = User::where("username", "=", $username)->firstOrFail();
        // Check to make sure that the current user matches or is an administrator
        if ($user->username == $profile->username || $user->role == "administrator") {
            // Display the profile page
            return $this->view->render($res, "profile.html", [
                "role" => $user->role,
                "username" => $username,
                "profile" => $profile
            ]);
        }
        // Forbidden user, return 403 status
        return $res->withStatus(403);
    } catch (Exception $e) {
        // User profile does not exist, return error 404
        return $res->withStatus(404);
    }
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
