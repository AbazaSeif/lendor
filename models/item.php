<?php

// Usage aliases
use \Illuminate\Database\Eloquent\Model as Model;
use \Illuminate\Database\Capsule\Manager as Capsule;

// User model class
class Item extends Model {

    // Table for this model
    protected $table = "item";

    // Allowed fields
    protected $fillable = [
        "name",
        "location",
    ];

    // Database migration
    public static function migrate () {
        // If the user table does not exist
        if (Capsule::schema()->hasTable("item") == false) {
            // Create the user table
            Capsule::schema()->create("item", function ($table) {
                // Model attributes
                $table->increments("id");
                $table->string("name");
                $table->string("location");
                $table->timestamps();
            });
        }
    }
}
