<?php
namespace Signes\Acl;

use Illuminate\Support\ServiceProvider;
use Signes\Acl\Repository\SignesAclRepository;

/**
 * Class AclServiceProvider
 *
 * @package Signes\Acl
 */
class AclServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register Signes ACL
     */
    public function register()
    {
        $this->app->singleton('acl', function () {
            return new Acl(new SignesAclRepository());
        });
    }

    /**
     * Boot Signes ACL package
     */
    public function boot()
    {
        // php artisan vendor:publish --provider="Signes\Acl\AclServiceProvider"

        // Publish database migrations
        $this->publishes([
            __DIR__ . '/../Publish/Migrations/' => base_path('/database/migrations')
        ], 'migrations');

        // Publish database models
        $this->publishes([
            __DIR__ . '/../Publish/Models/' => base_path('/app/Models')
        ], 'models');
    }
}
