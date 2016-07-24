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
		// Attempt to update the user and store the status
		$status = Helper::update_user($profile->id, $user->role, $post);
		// Status says there was an error
		if (is_array($status)) {
			// Redner the profile page
			echo $twig->render("profile.html", [
				// Page title
				"title" => "Lendor - Profile",
				// Current user
				"user" => $user,
				// Viewed profile
				"profile" => $profile,
				// Error message
				"error" => $status["error"]
			]);
		}
		// User update went off without a hitch
		else {
			// Obtain the profile again as it has updated
			$profile = User::where("username", "=", $username)->firstOrFail();
			// Render the profile page
			echo $twig->render("profile.html", [
				// Page title
				"title" => "Lendor - Profile",
				// Current user
				"user" => $user,
				// Viewed profile
				"profile" => $profile,
				// Success message
				"success" => "User has been updated"
			]);
		}
	}
	// The request is GET
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
})->via("GET", "POST");

// Logout route
// GET
$app->get("/logout", function () use ($app) {
    // Unset the session
    session_unset();
    // Redirect to login
    $app->redirect("/login");
});

