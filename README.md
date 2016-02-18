#Laravel 5 ACL#

[![Latest Stable Version](https://poser.pugx.org/signes/acl/v/stable)](https://packagist.org/packages/signes/acl)
[![Build Status](https://travis-ci.org/signes-pl/laravel-acl.svg?branch=master)](https://travis-ci.org/signes-pl/laravel-acl)
[![Total Downloads](https://poser.pugx.org/signes/acl/downloads)](https://packagist.org/packages/signes/acl)
[![License](https://poser.pugx.org/signes/acl/license)](https://packagist.org/packages/signes/acl)

###For Laravel 4, please use the [laravel4](https://github.com/signes-pl/laravel-acl/tree/laravel4) branch. Please note that this version (Laravel 4) is no longer maintained.###

* [Database schema](#database-schema)
* [Installation](#installation)
* [Usage](#usage)
* [Advanced example](#advanced-example)
* [User object](#user-object)
* [Create own providers](#create-own-providers)
* [Credits](#credits)



With this package, you will be able detailed control access to any resource on your Laravel site.
This mechanism is inspired by the amazing [OrmAuth](http://fuelphp.com/docs/packages/auth/ormauth/intro.html#/acl) solution used in the framework FuelPHP.

You gain powerful mechanism for managing access levels, which include:
* Every User may have one Group,
* Every User may have many Roles,
* Every User may have many Permissions,
* Every Group may have many Roles
* Every Group may have many Permissions
* Every Role may have many Permissions
* Every Role may have special permission filter, which grant global access to everything, deny access to everything or deny to previous granted access,
* Every permission may consists of `zone`, `permission` and many `actions`

**Required:**
* Laravel ~5.1
* PHP >= 5.5

**Recommended**
* Using database compatible with [Eloquent](http://laravel.com/docs/5.0/eloquent) and [Migration](http://laravel.com/docs/5.0/migrations) mechanism.


###Database schema###
![alt Laravel ACL Schema](https://cloud.githubusercontent.com/assets/5002331/4994391/dbe4772e-69b8-11e4-9562-514bccc90f22.png "Laravel ACL Schema")

##Installation##
* **Add this package as required in composer:**
```
"signes/acl": "^2.0.0"
```


* **Add *Service Provider* and *Facade*. To do this, edit your `app.php` file and add:**
```php
'providers' => [
    ...
	Signes\Acl\AclServiceProvider::class,
	...
];
```
```php
'aliases' => [
	...
	'Acl' => Signes\Acl\Facades\Acl::class,
	...
];
```

* **Move migrations and models to your project:**
```
php artisan vendor:publish --provider="Signes\Acl\AclServiceProvider"
```
this command will copy files: 
* `vendor/signes/acl/src/Acl/Publish/Migrations` -> `database/migrations`
* `vendor/signes/acl/src/Acl/Publish/Models` -> `app/Models`

* **Migrate database**
```
php artisan migrate
```

###Application name###
In Laravel 5 you can set your [own application name](http://laravel.com/docs/5.0/configuration#after-installation). If you did this and default `namespace` is not "**App**", you have to change namespaces in new models manually. Remember also to update `$namespace` variable in this models to get correct relations.

Also you have to get ACL know which namespace you use. You can do this by `\Acl::setSiteNamespace('MyNamespace');`.

##Usage##
You can check whether the user has permissions to the resource. This is done with the `\Acl::isAllow($resource, UserInterface $user = null)`. For the currently logged in user we check his access to the resource defined in the variable `$resource`.
We can describe resource on few ways:
```php
$resource_A = "zoneA.permissionA|actionA1.actionA2"
$resource_A = "zoneB.permissionB|actionB1"
$resource_B = "zoneC.permissionC"
```
As a result we got `true` or `false`.

If we requested few actions, we get `true` result only when User has access to every of this actions.

You can pass `$user` to every `\Acl::isAllowed()` method, or set User only once in Acl using `\Acl::setUser(UserInterface $user)`. If you won't set any user, by default it will take **Guest account**, that means: `User::find(1);`.

####Special filters####

Roles may contain special filters like:
* `A` - allow access to everything
* `D` - deny access to everything
* `R` - revoke access to resource

and you can set it using `$role->setFilter($filter)`.

####Available methods####

**Note** All interfaces (like `UserInterface`), comes from package so please complete namespaces. I skip full namespace description to make code more readable.

---

**`\Acl::setSiteNamespace($namespace)`**  
Set new namespace for models. Required when you change app namespace.

---

**`\Acl::isAllow($resource, UserInterface $user = null)`**  
Check if current user have access to `$resource`.

---

**`\Acl::createPermission($area, $permission, array $actions = null, $description = '')`**  
Create new permission. `$actions` may contain string or array of accesses.

---

**`\Acl::deletePermission($area, $permission = null, $actions = null)`**  
Delete existing permission. You can delete whole `zone`, `zone.permission`, or single actions in `zone.permission` set 

---

**`\Acl::grantUserPermission(PermissionInterface $permission, UserInterface $user, $actions = [], $overwrite = false)`**  
Grand permissions with actions to User. If `$action` is empty array, there will be no access to any of action (eg. `zone.permission|action`). Only to global permission (like `zone.permission`). If `$action` is true, all permission access will be granted.
Using `$overwrite` attribute you can decide to overwrite existing user - permission relation. By default there will be Exception if this relation exists and you trying to set it once again.

---

**`\Acl::grantGroupPermission(PermissionInterface $permission, GroupInterface $group, $actions = [])`**  
Working similar to `\Acl::grantUserPermission`.

---

**`\Acl::grantRolePermission(PermissionInterface $permission, RoleInterface $role, $actions = [])`**  
Working similar to `\Acl::grantUserPermission`.

---

**`\Acl::revokeUserPermission(PermissionInterface $permission, UserInterface $user)`**  
User will lost access to Permission. Relation will be removed. It works only with User object, not with other ways to have permission (eg. Group or Role).
If you want to revoke access for specific User, but do not want to impact Permissions for other Users, you should think about special Role with `R` filter.

---

**`\Acl::revokeGroupPermission(PermissionInterface $permission, GroupInterface $group)`**  
Group will lost access to Permission.

---

**`\Acl::revokeRolePermission(PermissionInterface $permission, RoleInterface $role)`**  
Role will lost access to Permission.

---

**`\Acl::grantUserRole(RoleInterface $role, UserInterface $user)`**  
Connect User with Role and give all permissions connected with this Role.

---

**`\Acl::grantGroupRole(RoleInterface $role, GroupInterface $group)`**  
Connect User with Group and give all permissions connected with this Group.
    
---

**`\Acl::revokeUserRole(RoleInterface $role, UserInterface $user)`**  
Disconnect User with Role and revoke all permissions connected with this Role.
    
---

**`\Acl::revokeGroupRole(RoleInterface $role, GroupInterface $group)`**  
Disconnect User with Group and revoke all permissions connected with this Group.
    
---

## Middleware##

If you want to use middleware you can register it in `app/Http/Kernel.php`.

```php
protected $routeMiddleware = [
    ...
    'acl' => \Signes\Acl\AclMiddleware::class
    ...
];
```

Example usage in [routes](https://laravel.com/docs/master/middleware#assigning-middleware-to-routes) or [controllers](https://laravel.com/docs/master/controllers#controller-middleware):

```php
// Routes
Route::get('/', ['middleware' => ['acl:zone.permission|action1.action2'], function () {

// Controller
$this->middleware('acl:zone.permission|action1.action2');
```

By default when user do not have access to resource `\Signes\Acl\Exception\AclAccessException` exception will be thrown. Of course feel free to create own middleware and register it with custom logic.

##Advanced example##
Easiest way to describe how it works, is to give you a real example ;)
Let's take a sample user **user1**, which belongs to group **myGroup**. **User** and **Group** are connected with **Roles** and with **Permissions**.

**We want to create connections like below:**

![alt Laravel ACL example](https://cloud.githubusercontent.com/assets/5002331/5563580/6af8b668-8e81-11e4-8f21-7a7ab8be0dfd.jpg "Laravel ACL example")

in this example **user1** have access to permissions like:
* `zone1.access1|act1.act2.act3`
* `zone2.access2|act1.act3`
* `zone3.access3|act1`

and here is code how to reach this:

```php
    /**
     * Create and Save example Group
     */
    $group = new \App\Models\Acl\Group();
    $group->setName('MyGroup1')->save();

    /**
     * Create and save example Roles
     */
    $role1 = new \App\Models\Acl\Role();
    $role1->setName('My Role 1')->save();
    
    $role2 = new \App\Models\Acl\Role();
    $role2->setName('My Role 2')->save();
    
    $role3 = new \App\Models\Acl\Role();
    $role3->setName('My Role 3')->setFilter('R')->save();

    /**
     * Create and save example permissions
     */
    $permission1 = \Acl::createPermission('zone1', 'access1', ['act1', 'act2', 'act3'], 'Zone 1');
    $permission2 = \Acl::createPermission('zone2', 'access2', ['act1', 'act2', 'act3'], 'Zone 2');
    $permission3 = \Acl::createPermission('zone3', 'access3', ['act1', 'act2', 'act3'], 'Zone 3');

    /**
     * When we have ready entity lets connect them
     */
    $guestUser = \App\Models\Acl\User::find(1);
    $guestUser->setGroup($group)->save()

    // Connect user with permissions
    \Acl::grantUserPermission($permission1, $guestUser, ['act1']);
    \Acl::grantUserPermission($permission2, $guestUser, ['act1', 'act2', 'act3']);

    // Connect group with permissions
    \Acl::grantGroupPermission($permission1, $group, ['act3']);

    // Connect roles with permissions
    \Acl::grantRolePermission($permission3, $role1, ['act1']);
    \Acl::grantRolePermission($permission1, $role2, ['act2']);
    \Acl::grantRolePermission($permission2, $role3, ['act2']);

    // Connect user with roles
    \Acl::grantUserRole($role2, $guestUser);
    \Acl::grantUserRole($role3, $guestUser);

    // Connect group with roles
    \Acl::grantGroupRole($role1, $group);
    \Acl::grantGroupRole($role2, $group);
```

##User object##
Most important object in ACL is User representation, which is provided by `\App\Models\Acl\User` object.
For more information's you can check `app/Models/Acl/User.php` file.

Of course you can create your own User object, just remember to extend your class by `\App\Models\Acl\User`.

Available methods on User object:
* `$user->getGroup`
* `$user->setGroup(\Signes\Acl\GroupInterface $group)`
* `$user->getRoles`
* `$user->getPermissions`

##Create own providers##
By default `\Signes\Acl` give you ready models and required methods to work. But sometimes you may want to integrate ACL with your own, existing `User` objects.
You can design your own providers, remember only to implement required interfaces (UserInterface, GroupInterface, PermissionInterface, RoleInterface).

`\Signes\Acl` used own Provider to get data. You can check code in `Signes/Acl/Repository/SignesAclRepository.php` file. 

##Credits##
* Pawel Grzesiecki - Developer ([http://signes.pl/](http://signes.pl/))
[MIT](http://opensource.org/licenses/MIT) License