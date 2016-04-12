<?php

namespace App\Models;

use App\Helpers\Eloquent\Model;

use App\Helpers\Generator;

use Purifier;
class GigGroup extends Model
{
	protected $table = 'gig_group';

	protected $fillable = ['name','cover_img','slug','slogan','description','privacy','created_by'];

	public $incrementing = false;

	protected static function boot()
	{
		parent::boot();

		static::registerModelEvent('saving',function($model){

			$model->description = Purifier::clean($model->description);
			
			
		});

	}
 	static public function createRules()
	{
		return [
			'name' => 'required|not_numeric',
			'slug' => 'not_numeric',
			'privacy' => 'required|in:public,private'
		];
	}

	static public function updateRules($additionalRules = [])
	{
		return array_merge( static::createRules(), [
			'slug' => 'required|not_numeric',
			'id' => 'required|numeric'
		], $additionalRules);
	}


	

	public function gig_members()
	{
		return $this->hasMany('App\Models\GigGroupMembers','group_id');
	}
}