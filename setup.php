<?php

// Usage aliases
use \Illuminate\Database\Capsule\Manager as Capsule;

// Eqloquent database capusle manager
$capsule = new Capsule;
// Add connection to the capsule
$capsule->addConnection($config["db"]);
// Set capsule as global
$capsule->setAsGlobal();
// Boot eloquent database
$capsule->bootEloquent();

// User migrate setup
User::migrate();
