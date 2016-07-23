<?php

// Debugging - Remove in production
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Dependencies
require "vendor/autoload.php";
require "functions.php";
require "config.php";
require "setup.php";
require "routes.php";
require "shutdown.php";

