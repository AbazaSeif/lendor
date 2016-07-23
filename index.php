<?php

// Debugging - Remove in production
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Dependencies
require_once "vendor/autoload.php";
require_once "config.php";
require_once "setup.php";
require_once "routes.php";
require_once "shutdown.php";
