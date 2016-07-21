<?php

// Check if the user is logged in
// Returns a user mode if they are
// Returns false if they are not
// Returns false on any error
function logged_in () {
    try {
        return User::findOrFail($_SESSION["user_id"]);
    } catch (Exception $e) {}
    return false;
}
