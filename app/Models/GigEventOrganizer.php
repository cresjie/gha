<?php

namespace App\Models;

use App\Helpers\Eloquent\Model;

class GigEventOrganizer extends Model
{

	protected $table = 'gig_event_organizer';
	protected $fillable = ['is_admin'];

	static public function createRules()
	{
		return [
			'event_id' => 'required|numeric',
			'user_id' => 'required|numeric'
		];
	}

	public function usertype()
	{
		return $this->belongsTo('App\Models\Usertype\Relationship','user_id', 'user_id');
	}
}