<?php

namespace App\Models;

use Model;

class EventRsvp extends Model
{

	protected $table = 'event_rsvp';

	protected $primaryKey = 'event_id';

	public $incrementing = false;

	protected $fillable = ['limit', 'maximum_guest', 'display_remaining', 'message', 'due_date'];

	static public function createRules()
	{
		return [
			'event_id' => 'required',
			'limit' => 'required|numeric',
			'maximum_guest' => 'numeric|min:0',
			'display_remaining' => 'boolean',
			'message' => 'required',
			'due_date' => 'required|date'
		];
	}

	static public function draftRules()
	{
		return [
			'event_id' => 'required',
			'limit' => 'numeric',
			'maximum_guest' => 'numeric|min:0',
			'display_remaining' => 'boolean',
			//'message' => 'required',
			'due_date' => 'date'
		];
	}
}