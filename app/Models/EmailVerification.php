<?php

namespace App\Models;

use Model;

class EmailVerification extends Model{
	
	protected $table =  'email_verification';

	protected $fillable = ['user_id','verification_token'];

	protected $primaryKey = 'user_id';
}