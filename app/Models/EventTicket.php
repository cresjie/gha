<?php

namespace App\Models;

use App\Helpers\Eloquent\Model;

class EventTicket extends Model
{
	
	protected $table = 'event_ticket';

	protected $fillable = ['type','name','description','sales_start','sales_end','price','currency','stock','minimum','maximum', 'sort_number'];

	static public function createRules()
	{
		return [
			'event_id' => 'required',
			'type' => 'required|in:free,paid,donation',
			'name' => 'required',
			'sales_start' => 'date',
			'sales_end' => 'date',
			'price' => 'required_if:type,paid|numeric',
			'currency' => 'required|alpha',
			'stock' => 'required|numeric',
			'minimum' => 'numeric|min:1',
			'maximum' => 'numeric|min:1|min_to:minimum',
			'sort_number' => 'numeric'
		];
	}

	static public function draftRules()
	{
		return [
			'event_id' => 'required',
			'type' => 'required|in:free,paid,donation',
			'sales_start' => 'date',
			'sales_end' => 'date',
			'price' => 'numeric',
			'currency' => 'alpha',
			'minimum' => 'numeric|min:1',
			'stock' => 'numeric',
			'maximum' => 'numeric|min:1|min_to:minimum',
			'sort_number' => 'numeric'
		];
	}	
}