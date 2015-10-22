<?php

namespace {

    /**
     * Class TestCase
     */
    class TestCase extends \Orchestra\Testbench\TestCase
    {
        /**
         * Set up repository to tests
         */
        public function setUp()
        {
            parent::setUp();

            $this->artisan('migrate', [
                '--database' => 'sqlite',
                '--realpath' => realpath(__DIR__ . '/../src/Publish/Migrations'),
            ]);
        }

        /**
         * Tear down environment after tests
         */
        public function tearDown()
        {
            $this->artisan('migrate:rollback', [
                '--database' => 'sqlite'
            ]);

            parent::tearDown();
        }

        /**
         * @param \Illuminate\Foundation\Application $app
         * @return array
         */
        protected function getPackageProviders($app)
        {
            return ['Signes\Acl\AclServiceProvider'];
        }

        /**
         * @param \Illuminate\Foundation\Application $app
         * @return array
         */
        protected function getPackageAliases($app)
        {
            return [
                'Acl' => 'Signes\Acl\Facades\Acl'
            ];
        }

        /**
         * Define environment setup.
         *
         * @param  Illuminate\Foundation\Application $app
         * @return void
         */
        protected function getEnvironmentSetUp($app)
        {
            // reset base path to point to our package's src directory
            //$app['path.base'] = __DIR__ . '/../src';
            $config = require 'config/database.php';
            $app['config']->set('path.storage', '/');
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite', $config['connections']['sqlite']);
            $app['config']->set('cache.driver', 'array');
        }
    }
}
