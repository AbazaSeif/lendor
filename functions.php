<?php

// Check if user is authenticated
// Return the user if they are
// Redirect toward a given route if they are not
$authenticated = function ($app, $route) {
    try {
        return User::findOrFail($_SESSION["user_id"]);
    } catch (Exception $e) {
        $app->redirect($route);
    }
};
