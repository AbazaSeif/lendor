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
     * @param $args - Required - See below
     *
     * $args["redirect"] Required - Route to redirect to upon authentication fail
     * $args["username"] Optional - Username needed for the user to pass authentication
     * $args["role"]     Optional - Role needed for the user to pass authentication
     * $args["id"]       Optional - User ID to use. Defaults to $_SESSION["user_id"]

     * @param &$user - Optional - Any obtained user will set into this variable
     *
     * @return function (function returns User or null)
     *
     */ 
    public static function auth ($args, &$user=null) {
        // For some dumb reason this is needed
        return function () use ($args, &$user) {
            // Obtain the slim application
            $app = \Slim\Slim::getInstance();
            // Encapsulate any errors
            try {
                // Set the user id, either given to use or obtained from the session
                $id = array_key_exists("id", $args) ? $args["id"] : $_SESSION["user_id"];
                // Obtain the current user or fail
                $user = User::findOrFail($id);
                // Perform check if role checking is specified
                if (array_key_exists("role", $args)) {
                    // Throw exception upon mismatching roles
                    if ($user->role != $args["role"]) { throw new Exception(); }
                }
                // Perform check if username is specified
                if (array_key_exists("username", $args)) {
                    // Throw exception upon mismatching usernames
                    if ($user->username != $args["username"]) { throw new Exception(); }
                }
            }
            // Catch any thrown errors
            catch (Exception $e) {
                // Redirect to specified route
                $app->redirect($args["redirect"]);
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
     * @param $args - Required - See below
     *
     * $args["redirect"] Required - Route to redirect to upon authentication fail
     * $args["username"] Optional - Username needed for the user to pass authentication
     * $args["role"]     Optional - Role needed for the user to pass authentication
     * $args["id"]       Optional - User ID to use. Defaults to $_SESSION["user_id"]
     *
     * @param &$user - Optional - Any obtained user will set into this variable
     *
     * @return User, or null
     *
     */ 
    public static function auth_call ($args, &$user=null) {
        // Obtain the returned function
        $auth = self::auth($args, $user);
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
