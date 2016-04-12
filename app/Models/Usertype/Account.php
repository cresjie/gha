<?php

namespace App\Models\Usertype;

use App\Helpers\Eloquent\Model;

class Account extends Model
{
	protected $table = 'usertype_account';

	protected $fillable = ['name', 'code', 'description', 'currency', 'price'];

	static public function createRules()
	{
		return [
			'name' => 'required',
			'name' => 'required|alpha_numeric',
			'description' => '',
			'currency' => 'required',
			'price' => 'required|numeric'
		];
	}
}