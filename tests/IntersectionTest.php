<?php

use Mockery as m;
use Feijs\MTDataSeparation\ModelWithTenant;
use Feijs\MTDataSeparation\ModelWithoutTenant;
use Feijs\MTDataSeparation\ModelWithTenantAndSoftDelete;

/**
 * Multi Tenant Data Separation Test Case
 *
 * @package Feijs/MTDataSeparation
 * @author  Mike Feijs <mike@feijs.nl>
 */
class IntersectionTest extends DSTestCase
{
	protected $non_intersect_queries = 3;
	protected $intersect_queries = 2;
	protected $num_tenants = 5;

	protected $tenants;

	/**
     * Setup the test environment.
     */
    public function setUp()
    {
    	parent::setUp();

        $this->tenants = range(1, $this->num_tenants);
    }

	public function testNonIntersectingResults()
	{
		for($i = 1; $i <= $this->non_intersect_queries; $i++)
		{
			$intersection = null;

			foreach($this->tenants as $tenant)
			{
				$this->setTenant($tenant);
			
				$result_builder = $this->{"nonIntersectQuery{$i}"}();		
				$result_set = $result_builder->lists('id');

				$this->assertNotEmpty($result_set, "Query {$i} returned an empty result. Please check table is seeded");

				if(is_null($intersection)) {		//Store first set
					$intersection = $result_set;
				}
				else {								//Compare other sets
					$intersection = array_intersect($intersection, $result_set);
				}
			}

			$this->assertEmpty($intersection, "Overlapping results for query {$i}");			
		}
	}

	public function NonIntersectQuery1()
	{
		$model = new ModelWithTenant;
		$builder = $model->newQuery();

		return $builder->where('foo', '=', "foo")->orWhere('bar', '=', "bar");
	}

	public function NonIntersectQuery2()
	{
		$model = new ModelWithTenant;
		$builder = $model->newQuery();

		return $builder->where('foo', '=', 'foo')->orWhereNotNull('tenant_id');
	}

	public function NonIntersectQuery3()
	{
		$model = new ModelWithTenantAndSoftDelete;
		$builder = $model->newQuery();
		
		return $builder->withTrashed();
	}

	public function testIntersectingResults()
	{
		for($j = 1; $j <= $this->intersect_queries; $j++)
		{
			$total = $this->{"intersectQuery{$j}"}();	

			foreach($this->tenants as $tenant)
			{
				$this->setTenant($tenant);
			
				$result_set = $this->{"intersectQuery{$j}"}();		

				/* Assert array equality (but not order) */
				$this->assertNotEmpty($result_set, "Query {$j} returned an empty result. Please check table is seeded");
				$this->assertEquals($total, $result_set, "\$canonicalize = true", 0.0, 10, true);
			}
		}
	}

	public function IntersectQuery1()
	{
		$model = new ModelWithoutTenant;
		$builder = $model->newQuery();

		return $builder->where('foo', '=', 'foo')->orWhere('bar', '<>', 'foo');
	}

	public function IntersectQuery2()
	{
		$model = new ModelWithTenant;
		$builder = $model->allTenants();

		return $builder->where('foo', '=', 'foo')->orWhere('bar', '<>', 'foo');
	}

}