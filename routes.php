<?php

// Index route
// GET
$app->get("/", function () use ($app, $twig) {
    // Require authentication
    $user = Helper::auth_call([
        // Redirect to login page
        "redirect" => "/login"
    ]);
    // Render the index page
    echo $twig->render("index.html", [
        // Page title
        "title" => "Lendor - Home",
        // Current user
        "user" => $user
    ]);
});

// Login route
// GET, POST
$app->map("/login", function () use ($app, $twig, $config) {
    // Request is POST
    if ($app->request()->isPost()) {
        // Check and store a user model on successful login
        if ($user = Helper::valid_login($app->request->post(), $config["ldap"])) {
            // Set the session variables
            $_SESSION["user_id"] = $user->id;
            // Redirect to the index route
            $app->redirect("/");
        }
        // On invalid username and password combination
        else {
            // Render the login page with error
            echo $twig->render("login.html", [
                // Page title
                "title" => "Lendor - Log In",
                // Error message
                "error" => "Invalid username or password"
            ]);
        }
    }
    // Request is GET
    else {
        // Render the login page
        echo $twig->render("login.html", [
            // Page title
            "title" => "Lendor - Log In"
        ]);
    }
})->via("GET", "POST");

// Profile route
// GET, POST
$app->map("/profile/:username", function ($username) use ($app, $twig) {
    // Encapsulate any errors
    try {
        // Obtain the profile to view or fail
        $profile = User::where("username", "=", $username)->firstOrFail();
    }
    // Profile could not be found
    catch (Exception $e) {
        // Set status to 404
        $app->response->setStatus(404);
        // Stop the request from going further
        $app->stop();
    }
    // Require authentication
    $user = Helper::auth_call([
        // 403 forbidden status
        "status" => 403,
        // Only require one filter
        "combine" => false,
        // Require matching name
        "username" => $username,
        // Require administrator role
        "role" => "administrator"
    ]);
    // The request is POST
    if ($app->request->isPost()) {
        // Get the post parameters
        $post = $app->request->post();
        // Delete request was set
        if (isset($post["delete"])) {
            // User must be administrator
            if ($user->role == "administrator") {
                // Delete the profile
                $profile->delete();
                // Redirect to the users page
                $app->redirect("/admin/users");
            }
            // User is not administrator
            else {
                // Set the status
                $app->response->setStatus(403);
                // Stop the application
                $app->stop();
            }
        }
        // Attempt to update the user and store the status
        $updated_profile = Helper::update_user($profile->id, $user->role, $post);
        // Status says there was an error
        if (is_array($updated_profile)) {
            // Redner the profile page
            echo $twig->render("profile.html", [
                // Page title
                "title" => "Lendor - Profile",
                // Current user
                "user" => $user,
                // Viewed profile
                "profile" => $profile,
                // Error message
                "error" => $updated_profile["error"]
            ]);
        }
        // User update went off without a hitch
        else {
            // Username has changed
            if ($username != $updated_profile->username) {
                // Flash a message for the new route
                $app->flash("success", "User has been updated");
                // Redirect to the new user page
                $app->redirect("/profile/" . $updated_profile->username);
            }
            // Username has stayed the same
            else {
                try {
                    // We need to fetch the user again as their role may have changed
                    $user = User::findOrFail($user->id);
                } catch (Exception $e) {}
                // Render the profile page
                echo $twig->render("profile.html", [
                    // Page title
                    "title" => "Lendor - Profile",
                    // Current user
                    "user" => $user,
                    // Viewed profile
                    "profile" => $updated_profile,
                    // Success message
                    "success" => "User has been updated"
                ]);
            }
        }
    }
    // The request is GET
    else {
        // Username just changed
        if (isset($_SESSION["slim.flash"]["success"])) {
            // Render the profile page
            echo $twig->render("profile.html", [
                // Page title
                "title" => "Lendor - Profile",
                // Current user
                "user" => $user,
                // Viewed profile
                "profile" => $profile,
                // Success message
                "success" => $_SESSION["slim.flash"]["success"]
            ]);
        }
        // Username is the same
        else {
            // Render the profile page
            echo $twig->render("profile.html", [
                // Page title
                "title" => "Lendor - Profile",
                // Current user
                "user" => $user,
                // Viewed profile
                "profile" => $profile
            ]);
        }
    }
})->via("GET", "POST");

// Administrator group
$app->group("/admin", function () use ($app, $twig) {
    // Users route
    $app->get("/users", function () use ($app, $twig) {
        // Obtain authenticated user
        $user = Helper::auth_call([
            // Forbidden status
            "status" => 403,
            // Require administrator role
            "role" => "administrator",
        ]);
        // Render the users page
        echo $twig->render("users.html", [
            // Current user
            "user" => $user,
            // Page title
            "title" => "Lendor - Users",
            // Obtained users
            "users" => User::all()
        ]);
    });
    // Create user
    $app->get("/createuser", function () use ($app, $twig) {
        // Obtain authenticated user
        $user = Helper::auth_call([
            // Forbidden status
            "status" => 403,
            // Require administrator role
            "role" => "administrator",
        ]);
        // Render the users page
        echo $twig->render("createuser.html", [
            // Current user
            "user" => $user,
            // Page title
            "title" => "Lendor - Create User",
        ]);
    });
    // Create user
    $app->post("/createuser", function () use ($app, $twig) {
            // Obtain authenticated user
        $user = Helper::auth_call([
            // Forbidden status
            "status" => 403,
            // Require administrator role
            "role" => "administrator",
        ]);
        // Obtain the post parameters
        $post = $app->request->post();
        // Attempt to create the new uset
        $profile = Helper::create_user($post);
        // Creation returned with an error
        if (is_array($profile)) {
            // Render the users page
            echo $twig->render("createuser.html", [
                // Page title
                "title" => "Lendor - Create User",
                // Current user
                "user" => $user,
                // Error message
                "error" => $profile["error"]
            ]);
        }
        // Creation went off without a hitch
        else {
            // Render the users page
            echo $twig->render("createuser.html", [
                // Page title
                "title" => "Lendor - Create User",
                // Current user
                "user" => $user,
                // Error message
                "success" => "User has been created"
            ]);
        }
    });
});

// Logout route
// GET
$app->get("/logout", function () use ($app) {
    // Unset the session
    session_unset();
    // Redirect to login
    $app->redirect("/login");
});

