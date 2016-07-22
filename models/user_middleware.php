<?php

class UserMiddleware {

    protected $container;

    public function __construct ($container) {
        $this->container = $container;
    }
   
    public function authenticated ($req, $res, $args) {
        try {

        } catch (Exception $e) {
            
        }
    }
 
    public function administrator ($req, $res, $args) {
        try {
            $user = User::findOrFail($_SESSION["user_id"]); 
            if ($user->role != "administrator") {
                return $res->withStatus(403);  
            }
        } catch (Exception $e) {
            return $res->withStatus(403);  
        }
    }
}
