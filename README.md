#Laravel ACL#
**Please, keep in mind that it is development version and should not be used on production.**

With this package, you will be able detailed control access to any resource on your Laravel site.
This mechanism is based on the amazing [OrmAuth](http://fuelphp.com/docs/packages/auth/ormauth/intro.html#/acl) solution used in the framework FuelPHP.

You gain powerful mechanism for managing access levels, which include:
* Login and authenticate user, using native Laravel [Auth](http://laravel.com/docs/4.2/security#authenticating-users) mechanism,
* Every User may have one Group,
* Every User may have many Roles,
* Every User may have many Permissions,
* Every Group may have many Roles
* Every Group may have many Permissions
* Every Role may have many Permissions
* Every Role may have special permission filter, which grant global access to everything, deny access to everything or deny to previous granted access,
* Every permission may consists of `zone`, `permission` and many `actions`

**Required:**
* Laravel >= 4.2
* PHP >= 5.4
* Using database compatible with [Eloquent](http://laravel.com/docs/4.2/eloquent) and [Migration](http://laravel.com/docs/4.2/migrations) mechanism.


###Database schema###
~[alt Laravel ACL Schema](https://cloud.githubusercontent.com/assets/5002331/4994391/dbe4772e-69b8-11e4-9562-514bccc90f22.png "Laravel ACL Schema")

##Installation##
* Add this package as required in composer:
```php
soon
```


* Add **Service Provider** and **Facade**. To do this, edit your `app.php` file and add:
```php
'providers' => array(
	...
	'Signes\Acl\AclServiceProvider',
	...
);
```
```php
'aliases' => array(
	...
	'Acl' => 'Signes\Acl\Facades\Acl',
	...
);
```

* Migrate database schemas:
```
php artisan --package="signes/acl"
```

##Usage##

