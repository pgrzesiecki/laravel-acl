#Laravel ACL#

**Please, keep in mind that it is development version and should not be used on production.**

##Installation##

1. Add this package as required in composer:

```php
soon
```

and update yout codebase:
```
composer update
```

2. Add **Service Provider** and **Facade**. To do this, edit your `app.php` file and add:

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

##Usage##

**todo**