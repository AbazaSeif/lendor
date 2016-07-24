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
     * @return function (function returns User or false)
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
				// Stop the annoying notices of undefined index for user_id
				if (!isset($_SESSION["user_id"])) { throw new Exception(); }
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
					// Stop the request from going further
					//$app->stop();
				}
				// If the redirect route was not given
				else {
					// Set the status
					$app->response->setStatus($args["status"]);
					// Stop the request from going further
					$app->stop();
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
	 * @return User, or false
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

	public static function update_user ($id, $role, $args) {
		// Encapsulate any errors
		try {
			// Grab the specified user or fail
			$user = User::findOrFail($id);
			// Default ungiven arguments to current arguments
			// Don't set the password
			$args = array_merge([
				"username" => $user->username,
				"firstname" => $user->firstname,
				"lastname" => $user->lastname,
				"email" => $user->email,
				"role" => $user->role,
				"type" => $user->type
			], $args);
			// If the updating user is not an administrator
			if ($role != "administrator") {
				// Reset given arguments back to originals
				$args = array_merge($args, [
					"username" => $user->username,
					"role" => $user->role,
					"type" => $user->type,
				]);
			}
			try {
				// Check to make sure the username is not already taken
				// Does not check if the username is the same as before
				if ($user->username != $args["username"] && 
					User::where("username", "=", $args["username"])->first()) {
					// Return an error
					return ["error" => "Username already exists"];
				}
			} catch (Exception $e) {}
			// Set the username
			$user->username = $args["username"];
			// Password was specified
			if (!empty($args["password"])) {
				// Check to make sure the passwords match
				if ($args["password"] != $args["confirm_password"]) {
					// Return an error
					return ["error" => "Password fields do not match"];
				}
				// Set the password as a hash
				$user->password = password_hash($args["password"], PASSWORD_DEFAULT);
			}
			// Set the first name
			$user->firstname = $args["firstname"];
			// Set the last name
			$user->lastname = $args["lastname"];
			// Set the email address
			$user->email = $args["email"];
			// Set the user role
			$user->role = $args["role"];
			// Check to make sure the user role is valid
			if ($user->role != "administrator" && $user->role != "authenticated") {
				// Return an error
				return ["error" => "Invalid user role"];
			}
			// Set the user type
			$user->type = $args["type"];
			// Check to make sure the user type is valid
			if ($user->type != "local" && $user->type != "remote") {
				// Return an error
				return ["error" => "Invalid user type"];
			}
			// Save the user
			$user->save();
			// Return true
			return $user;
		}
		// Catch any exceptions
		catch (Exception $e) {
			// Return an error
			return ["error" => $e];
		}
	}
}
