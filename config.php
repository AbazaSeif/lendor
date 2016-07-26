<?php

$config = [
    // Slim v2 settings
    // See - http://docs.slimframework.com/configuration/settings/
    "slim" => [
        "mode" => "development",
	"debug" => true
    ],
    // Eloquent settings
    // See - TODO
    "db" => [
        "driver" => "mysql",
        "host" => "localhost",
        "database" => "lendor",
        "username" => "root",
        "password" => "",
        "charset" => "utf8",
        "collation" => "utf8_unicode_ci",
        "prefix" => "",
	"date.timezone" => "America/New_York"
    ],
    // Miscellaneous settings
    "misc" => [
	// Timezone, required
        "timezone" => "America/New_York"
    ]
];
