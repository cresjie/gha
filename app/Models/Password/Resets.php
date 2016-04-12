<?php

namespace App\Models\Password;

use Model;




class Resets extends Model
{

	public $incrementing = false;

	protected $table = 'password_resets';


	protected $primaryKey = 'user_id';
	
	
}