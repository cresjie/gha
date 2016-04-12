<?php

namespace App\Models;

use Model;

use App\Helpers\Generator;

class GroupDiscussionReply extends Model
{

	protected $table = 'group_discussion_reply';

	protected $fillable = ['gd_id', 'user_id', 'message'];

	public $incrementing = false;

	static public function createRules($additionalRules = [])
	{
		return array_merge([
			'gd_id' => 'required|numeric',
			//'user_id' => 'required|numeric',
			'message' => 'required'
		], $additionalRules);
	}

	static public function updateRules($additionalRules = [])
	{
		return array_merge(static::createRules(),[
			'user_id' => 'required|numeric'],
			$additionalRules);
	}


	protected static function boot()
	{
		parent::boot();

		static::registerModelEvent('creating',function($reply){
			$reply->id = Generator::id();
		});

	}

}