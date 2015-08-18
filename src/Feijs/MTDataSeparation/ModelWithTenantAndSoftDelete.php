<?php namespace Feijs\MTDataSeparation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use AuraIsHere\LaravelMultiTenant\Traits\TenantScopedModelTrait;

/**
 * Model with tenant constraints
 *  and soft deletes
 *
 * @package    Feijs\MTDataSeparation
 * @author     Mike Feijs <mfeijs@gmail.com>
 */
class ModelWithTenantAndSoftDelete extends Model
{
	use SoftDeletes;
	use TenantScopedModelTrait;
}
 