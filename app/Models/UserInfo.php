<?php

namespace App\Models;

use Model;

class UserInfo extends Model{
	
	protected $table = 'user_info';

	protected $primaryKey = 'user_id';

	protected $fillable = ['user_id','phone','address','company'];
}