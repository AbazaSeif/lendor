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
		"title" => "Lendor - Home",
		"username" => $user->username,
		"role" => $user->role
	]);
});

// Login route
// GET, POST
$app->map("/login", function () use ($app, $twig) {
	// Request is POST
	if ($app->request()->isPost()) {
		// Check and store a user model on successful login
		if ($user = Helper::valid_login($app->request->post())) {
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
				"error" => "Invalid username and password combination"
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

$app->get("/profile/:username", function ($username) use ($app, $twig) {
	$user = Helper::auth_call([
		// Redirect to index page
		"redirect" => "/",
		// Require matching name
		"username" => $username
	]);
});

// Logout route
// GET
$app->get("/logout", function () use ($app) {
    // Unset the session
    session_unset();
    // Redirect to login
    $app->redirect("/login");
});

