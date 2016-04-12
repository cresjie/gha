<?php

namespace App\Models;

use App\Helpers\Eloquent\Model;

class Contact extends Model
{
	protected $table = 'contact';

	protected $fillable = ['requestor','user_id','is_confirmed'];


	static public function createRules()
	{
		return [
			'user_id' => 'required|numeric',
			'is_confirmed' => 'boolean'
		];
	}
}