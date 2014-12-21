<?php
namespace Signes\Acl;

use Illuminate\Support\ServiceProvider;
use Signes\Acl\Repository\SignesAclRepository;

class AclServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('signes/acl', 'signes-acl');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind('acl', function () {
            return new Acl(new SignesAclRepository());
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
