<?php
use Orchestra\Testbench\TestCase as TestBenchTestCase;

/**
 * Multi Tenant Data Separation Test Case
 *
 * @package Feijs/MTDataSeparation
 * @author  Mike Feijs <mike@feijs.nl>
 */
abstract class DSTestCase extends TestBenchTestCase
{
	protected $artisan;

    protected function getPackageProviders()
    {
        return array(
        	'AuraIsHere\LaravelMultiTenant\LaravelMultiTenantServiceProvider'
        );
    }

    protected function getPackagePath()
    {
        return realpath(implode(DIRECTORY_SEPARATOR, array(
            __DIR__,
            '..',
            'src',
            'Feijs',
            'MTDataSeparation'
        )));
    }

    protected function getPackageAliases()
	{
    	return [
        	'TenantScope' => 'AuraIsHere\LaravelMultiTenant\TenantScope'
    	];
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
		$app['path.base'] = __DIR__ . '/../src';

	    // Setup default database to use sqlite :memory:
	    $app['config']->set('database.default', 'testbench');
	    $app['config']->set('database.connections.testbench', [
	        'driver'   => 'sqlite',
	        'database' => ':memory:',
	        'prefix'   => '',
	    ]);
	    $app['config']->set('laravel-multi-tenant::default_tenant_columns', 'tenant_id');
	}

	protected function runMigrations()
	{
		$this->artisan->call('migrate', [
		    '--database' => 'testbench',
		    '--path' => '/../migrations',
		]);
	}

	/**
     * Setup the test environment.
     */
    public function setUp()
    {
    	parent::setUp();

        $this->artisan = $this->app->make('artisan');
        $this->runMigrations();
    }

    public function setTenant($id)
    {
    	$scope = $this->app->make('AuraIsHere\LaravelMultiTenant\TenantScope');
    	$scope->addTenant('tenant_id', $id);
    }
}