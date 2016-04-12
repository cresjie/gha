<?php

namespace App\Models;

use Model;

use App\Helpers\Generator;

class GroupDiscussion extends Model
{

	protected $table = 'group_discussion';

	protected $fillable = ['group_id', 'subject', 'message'];

	public $incrementing = false;

	static public function createRules()
	{
		return [
			'group_id' => 'required|numeric',
			'subject' => 'required',
			'message' => 'required'
		];
	}

	static public function updateRules($additionalRules = [])
	{
		return array_merge(static::createRules(),[
				'user_id' => 'required|numeric'
			],
			$additionalRules);
	}


	protected static function boot()
	{
		parent::boot();

		static::registerModelEvent('creating',function($groupDiscussion){
			$groupDiscussion->id = Generator::id();
		});

		static::registerModelEvent('saving', function($groupDiscussion){

		});
	}

	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id');
	}

	public function discussion_replies()
	{
		return $this->hasMany('App\Models\GroupDiscussionReply', 'gd_id');
	}
}