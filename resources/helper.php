<?php

// Helper function class
class Helper {

    /*
     *
     * All in one authentication verification function
     * Best used as middleware within a route function
     * First and foremost checks if a given user is logged in
     * Afterwards, filters based on optional arguments provided
     * Will return a User model upon validation, otherwise null
     *
     * @param $args - Optional - See below
     *
     * $args["redirect"] Optional - Route to redirect to upon authentication fail, defaults to false
     * $args["status"]   Optional - Status to set the page to when redirecting, defaults to 200 OK status
     * $args["combine"]  Optional - True filters based on &&, false filters based on ||, defaults to true
     * $args["username"] Optional - Username needed for the user to pass authentication, defaults to none
     * $args["role"]     Optional - Role needed for the user to pass authentication, defaults to none
     * $args["id"]       Optional - User ID to use, defaults to $_SESSION["user_id"]
     *
     * @return function (function returns User or null)
     *
     */ 
    public static function auth ($args=[]) {
        // For some dumb reason this is needed
        return function () use ($args) {
            // Obtain the slim application
            $app = \Slim\Slim::getInstance();
            // Encapsulate any errors
            try {
				// Default the given arguments
				$args = array_merge([
					// Default redirect to current route
					"redirect" => false,
					// Default to 200 OK status
					"status" => 200,
					// Default combine to true
					"combine" => true,
				], $args);
				// This might throw an error
				// We need the above args to be set
				$args = array_merge([
					// Default user id to session user id
					"id" => $_SESSION["user_id"]
				], $args);
                // Obtain the current user or fail
                $user = User::findOrFail($args["id"]);
				// Default username check to true
				$username_check = true;
				// Default role check to true
				$role_check = true;
                // Perform check if role checking is specified
                if (array_key_exists("role", $args)) {
                    // Reset role check to false if given invalid role
                    if ($user->role != $args["role"]) { $role_check = false; }
                }
                // Perform check if username is specified
                if (array_key_exists("username", $args)) {
                    // Reset username check to false if given invalid username
                    if ($user->username != $args["username"]) { $username_check = false; }
                }
				// Check based on &&
				if ($args["combine"]) {
					// Check based on &&, throw an exception on fail
					if (!($username_check && $role_check)) { throw new Exception(); }
				}
				// Check based on ||
				else {
					// Check based on ||, throw an exception on fail
					if (!($username_check || $role_check)) { throw new Exception(); }
				}
            }
            // Catch any thrown errors
            catch (Exception $e) {
				// If the redirect route was given
				if ($args["redirect"]) {
					// Redirect to specified route
					$app->redirect($args["redirect"], $args["status"]);
				}
				// If the redirect route was not given
				else {
					// Set the status
					$app->response->setStatus($args["status"]);
				}
				// Return false
				return false;
            }
            // Return the obtained user, or null
            return $user;
        };
    }

    /*
     *
     * All in one authentication verification function
     * Same as self::auth, but it allows direct calling
     * First and foremost checks if a given user is logged in
     * Afterwards, filters based on optional arguments provided
     * Will return a User model upon validation, otherwise null
     *
     * @param $args - Optional - See below
	 *
     * $args["redirect"] Optional - Route to redirect to upon authentication fail, defaults to false
     * $args["status"]   Optional - Status to set the page to when redirecting, defaults to 200 OK status
     * $args["combine"]  Optional - True filters based on &&, false filters based on ||, defaults to true
     * $args["username"] Optional - Username needed for the user to pass authentication, defaults to none
     * $args["role"]     Optional - Role needed for the user to pass authentication, defaults to none
     * $args["id"]       Optional - User ID to use, defaults to $_SESSION["user_id"]
     *
	 * @return User, or null
     *
     */ 
    public static function auth_call ($args=[]) {
        // Obtain the returned function
        $auth = self::auth($args);
        // Call and return the function
        return $auth();
    }

	/*
     *
     * Check whether a username password combination is valid
     * Returns a user model on a correct combination
	 * Returns false on an incorrect combination
     *
     * @param $args - Required - See below
     *
     * $args["username"] Required - The username to verify
     * $args["password"] Optional - The password to verify
     *
     * @return User, or false
     *
     */ 
	public static function valid_login ($args) {
		// Make sure the username and password are provided to use
		if (array_key_exists("username", $args) && array_key_exists("password", $args)) {
			// Ecapsulate any thrown exceptions
			try {
				// Grab the user with the specified username or throw an exception
				$user = User::where("username", "=", $args["username"])->firstOrFail();
				// Return whether or not the login was valid, if true return the user
				return password_verify($args["password"], $user->password) ? $user : false;
			}
			// Do nothing on a thrown error
			catch (Exception $e) {}
		}
		// Default to false
		return false;
	}
}
