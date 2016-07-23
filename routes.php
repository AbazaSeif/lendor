<?php

// Index route
// GET
$app->get("/", Helper::auth(["redirect" => "/login"]), function () use ($app) {
    $app->render("index.html", [
		"title" => "Lendor - Home"
	]);
});

// Login route
// GET
$app->get("/login", function () use ($app) {
	// Render the login page
	echo $app->render("login.html", [
		// Page title
		"title" => "Lendor - Login"
	]);
});

// Login route
// POST
$app->post("/login", function () use ($app) {
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
		$app->render("login.html", [
			// Page title
			"title" => "Lendor - Login",
			// Error message
			"error" => "Invalid username and password combination"
		]);
	}
});

// Logout route
// GET
$app->get("/logout", function () use ($app) {
    // Unset the session
    session_unset();
    // Redirect to login
    $app->redirect("/login");
});

