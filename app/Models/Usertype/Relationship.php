<?php

namespace App\Models\Usertype;

use App\Helpers\Eloquent\Model;

class Relationship extends Model
{
	protected $table = 'usertype_relationship';

	protected $fillable = ['user_id','account_type'];

	protected $primaryKey = 'user_id';

	static public function createRules()
	{
		return [
			'user_id' => 'required|numeric',
			'account_type' => 'required|numeric'
		];
	}


	/**
	 * Relationships
	 */

	public function account()
	{
		return $this->hasOne('App\Models\Usertype\Account','id','account_type');
	}
}