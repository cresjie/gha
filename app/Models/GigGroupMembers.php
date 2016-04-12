<?php

namespace App\Models;

use App\Helpers\Eloquent\Model;

use App\Helpers\Generator;


class GigGroupMembers extends Model
{

	protected $table = 'gig_group_members';

	protected $fillable = ['group_id', 'user_id', 'is_admin']; // 'pending', 'approved_by', 'added_by'

	public $incrementing = false;

	const CREATED_AT  = 'added_at';

	public static function createRules()
	{
		return [
			'group_id' => 'required|numeric',
			'user_id' => 'required|numeric',
			'is_admin' => 'boolean',
			'approved_by' => 'numeric',
			'added_by' => 'numeric'
		];
	}

	

	public function user()
	{
		return $this->belongsTo('App\User','user_id');
	}
}