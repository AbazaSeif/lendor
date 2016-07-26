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
    // LDAP settings
    "ldap" => [
        // LDAP host name
        "host" => "ldap.forumsys.com",
        // LDAP port
        "port" => 389,
        // LDAP admin bind dn
        "bind_dn" => "cn=read-only-admin,dc=example,dc=com",
        // LDAP admin bind password
        "bind_password" => "password",
        // Search base for users
        "user_search_base" => "dc=example,dc=com",
        // User filter, %s is username
        "user_filter" => "(uid=%s)",
        "tls_encryption" => true,
        "tls_verify" => false
    ],
    // Miscellaneous settings
    "misc" => [
	// Timezone, required
        "timezone" => "America/New_York"
    ]
];
