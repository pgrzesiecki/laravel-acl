#Laravel ACL#
**Please, keep in mind that it is development version and should not be used on production.**

With this package, you will be able detailed control access to any resource on your Laravel site.
This mechanism is inspired by the amazing [OrmAuth](http://fuelphp.com/docs/packages/auth/ormauth/intro.html#/acl) solution used in the framework FuelPHP.

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
![alt Laravel ACL Schema](https://cloud.githubusercontent.com/assets/5002331/4994391/dbe4772e-69b8-11e4-9562-514bccc90f22.png "Laravel ACL Schema")

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
You can check whether the user has permissions to the resource. This is done with the `Acl::isAllow($resource)`. For the currently logged in user we check his access to the resource defined in the variable `$resource`.
We can describe resource on few ways:
```php
$resource_A = "zoneA.permissionA|actionA1.actionA2"
$resource_A = "zoneB.permissionB|actionB1"
$resource_B = "zoneC.permissionC"
```
As a result we got `true` or `false`.

If we requested few actions, we get `true` result only when User has access to every of this actions.

***Special filters***
Roles may contain special filters like:
* `A` - allow access to everything
* `D` - deny access to everything
* `R` - revoke access to resource

####Available methods####
**Acl::isAllow($resource)** 
Check if current user have access to `$resource`.

**Acl::createPermission($area, $permission, array $actions = null, $description = '')** 
Create new permission. `$actions` may contain string or array of accesses.

**Acl::deletePermission($area, $permission = null, $actions = null)**
Delete existing permission. You can delete whole `zone`, `zone.permission`, or single actions in `zone.permission` set 

... more soon

##Create own providers##
By default `Signes\Acl` give you ready models and required methods to work. But sometimes you may want to integrate ACL with your own, existing `User` objects.
You can design your own providers, remember only to implement required interfaces(UserInterface, GroupInterface, PermissionInterface, RoleInterface).

`Signes\Acl` used own Provider to get data. You can check code in `Signes/Acl/Repository/SignesAclRepository.php` file. 

##Credits##
* Pawel Grzesiecki - Developer ([http://signes.pl/](http://signes.pl/))
[MIT](http://opensource.org/licenses/MIT) License