<?php

$config = [
    // Slim v2 settings
    // See - http://docs.slimframework.com/configuration/settings/
    "slim" => [
        "mode" => "development",
	"debug" => true
    ],
    // Eloquent settings
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
        // LDAP version to use
        "ldap_version" => 3,
        // LDAP admin bind dn
        "bind_dn" => "cn=read-only-admin,dc=example,dc=com",
        // LDAP admin bind password
        "bind_password" => "password",
        // Search base for users
        "user_search_base" => "dc=example,dc=com",
        // User filter, %s is username
        "user_filter" => "(uid=%s)",
        // User dn attribute name (usually just 'dn')
        "user_dn_attr" => "dn",
        // Whether to use ldap over tls
        "tls_encryption" => false,
    ],
    // Miscellaneous settings
    "misc" => [
	// Timezone, required
        "timezone" => "America/New_York"
    ]
];
