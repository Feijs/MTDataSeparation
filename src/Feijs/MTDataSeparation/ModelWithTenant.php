<?php namespace Feijs\MTDataSeparation;

use Illuminate\Database\Eloquent\Model;
use AuraIsHere\LaravelMultiTenant\Traits\TenantScopedModelTrait;

/**
 * Model with tenant constraints
 *
 * @package    Feijs\MTDataSeparation
 * @author     Mike Feijs <mfeijs@gmail.com>
 */
class ModelWithTenant extends Model
{
	use TenantScopedModelTrait;
}
 