<?php

// Check if the user is logged in
// Returns a user model if they are
// Returns false if they are not
// Returns false on any error
function logged_in () {
    try {
        return User::findOrFail($_SESSION["user_id"]);
    } catch (Exception $e) {}
    return false;
}

// Check if login credentials are valid
// Requires a parameters array
// Returns user model on valid authentication
// Returns false on invalid authentication
function valid_login ($params) {
    try {
        $username = $params["username"];
        $password = $params["password"];
        $user = User::where("username", "=", $username)->firstOrFail();
        if (password_verify($password, $user->password)) {
            return $user;
        }
    } catch (Exception $e) {}
    return false;
}
