<?php

// Usage aliases
use \Illuminate\Database\Eloquent\Model as Model;
use \Illuminate\Database\Capsule\Manager as Capsule;

// User model class
class User extends Model {

    // Table for this model
    protected $table = "user";

    // Allowed fields
    protected $fillable = [
        "username",
        "password",
        "firstname",
        "lastname",
        "email",
        "role",
        "type"
    ];

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
                $table->string("firstname")->nullable();
                $table->string("lastname")->nullable();
                $table->string("email")->nullable();
                $table->string("role");
                $table->string("type");
                $table->timestamps();
            });
            // Create initial admin user
            User::create([
                "username" => "admin",
                "password" => password_hash("admin", PASSWORD_DEFAULT),
                "role" => "administrator",
                "type" => "local"
            ]);
        }
    }
}
