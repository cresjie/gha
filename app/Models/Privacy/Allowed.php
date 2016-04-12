<?php

namespace App\Models\Privacy;

use App\Helpers\Eloquent\Model;

class Allowed extends Model
{
	
	protected $table = 'privacy_allowed';

	protected $fillable = [];

	protected $hidden = ['allowable_name'];

	static public function createRules()
	{
		return [
			'allowable_id' => 'required',
			'allowable_name' => 'required',
			'allowed_user' => 'required'
		];
	}
}