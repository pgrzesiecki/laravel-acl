<?php
namespace Signes\Acl;

use Illuminate\Support\ServiceProvider;
use Signes\Acl\Contract\AclRepository;
use Signes\Acl\Repository\EloquentAclRepository;

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
        $this->app->singleton(
            'acl',
            function () {
                return new Acl($this->getRepository());
            }
        );
    }

    /**
     * Return repository which will be used to persist data.
     * If you have own implementation, just replace this class.
     *
     * @return AclRepository
     */
    protected function getRepository()
    {
        return new EloquentAclRepository();
    }

    /**
     * Boot Signes ACL package
     * Run:
     *  php artisan vendor:publish --provider="Signes\Acl\AclServiceProvider"
     */
    public function boot()
    {
        // Publish database migrations
        $this->publishes([__DIR__ . '/../Publish/Migrations/' => base_path('/database/migrations')], 'migrations');

        // Publish database models
        $this->publishes([__DIR__ . '/../Publish/Models/' => base_path('/app/Models')], 'models');
    }
}
