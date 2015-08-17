<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExampleTables extends Migration 
{
	protected $num_tenants = 5;
	protected $tuples_per_tenant = 100;

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('model_without_tenants', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('foo')->default("foo");
			$table->string('bar')->default("bar");
			$table->string('baz')->default("baz");
			$table->timestamps();
		});

		Schema::create('model_with_tenants', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('foo')->default("foo");
			$table->string('bar')->default("bar");
			$table->string('baz')->default("baz");
			$table->integer('tenant_id')->unsigned()->nullable();
			$table->timestamps();
		});		
	
		Schema::create('model_with_tenant_and_soft_deletes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('foo')->default("foo");
			$table->string('bar')->default("bar");
			$table->string('baz')->default("baz");
			$table->integer('tenant_id')->unsigned()->nullable();
			$table->timestamps();
			$table->softDeletes();
		});		

		$this->seed();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('model_with_tenant_and_soft_deletes');
		Schema::drop('model_with_tenants');
		Schema::drop('model_without_tenants');
	}

	protected function seed()
	{
		Eloquent::unguard();
		$scope = App::make('AuraIsHere\LaravelMultiTenant\TenantScope');

		$now = new DateTime;
		$keys = range(1, $this->tuples_per_tenant);
		$tenants = range(1, $this->num_tenants);

		foreach($keys as $key) {
			$data_without_tenant[$key] = 
			  	[
			  		'baz'			=> $key,
			  		'created_at'    => $now,
					'updated_at'    => $now
				];
		}
		$data_with_tenant = $data_without_tenant; //copy
		
		DB::table('model_without_tenants')->insert($data_without_tenant);

		foreach($tenants as $tenant)
		{	
    		$scope->addTenant('tenant_id', $tenant);
			foreach($data_with_tenant as &$tuple) {
				$tuple['tenant_id'] = $tenant;
			}
			
			DB::table('model_with_tenants')->insert($data_with_tenant);
			DB::table('model_with_tenant_and_soft_deletes')->insert($data_with_tenant);
		}

		
	}

}
