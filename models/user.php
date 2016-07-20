<?php

// Usage aliases
use \Illuminate\Database\Eloquent\Model as Model;
use \Illuminate\Database\Capsule\Manager as Capsule;

// User model class
class User extends Model {

    // Table for this model
    protected $table = "user";

    // Database migration
    public static function migrate () {
        // If the user table does not exist
        if (Capsule::schema()->hasTable("user") == false) {
            // Create the user table
            Capsule::schema()->create("user", function ($table) {
                // Model ID
                $table->increments("id");
                $table->string("username");
                $table->string("password");
                $table->string("firstname");
                $table->string("lastname");
                $table->string("email");
                $table->integer("role");
                $table->boolean("remote");
                $table->timestamps();
            });
        }
    }
}
