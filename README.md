# lendor
Item lending and management system with a web interface

# Note
This project has not yet had it's first major version release, changes will be continuously made to this project. We cannot guarantee any safety or sanity across updates until the first major version release, at which point this project will follow a more strict standard of commits.

If you see potential with this project, please feel free to contribute. We would appreciate the help!

# Install

Clone the repository into `/var/www` or wherever your webserver document roots reside.

```
cd /var/www
git clone git@github.com:UnmercifulTurtle/lendor.git
```

Update your web server document root to point at the cloned repository. This depends on what web stack you are using, the most common ones are `apache` and `nginx`. The following is what you would need to change for an apache setup.

```
DocumentRoot "/var/www/lendor"
```

Update the permissions of the cloned repository to match those that your web server requires. This could be something such as `www-data`, `apache`, `webservd`, or any number of things. This really depends on your personal setup.

```
chown -R apache /var/www/lendor
```

Update the project dependencies with composer. This repository assumes that you either have the global `composer` installed, or that you understand how to download and use composer

```
composer update
```

Restart your web server. The following is an example with an apache setup.

```
systemctl restart httpd
```

# License
GNU General Public License v3.0

# TODO

SELinux configuration makes LDAP not work for apache at first glance. Add instructions to allow LDAP connection through Apache using selinux into the documentation.
