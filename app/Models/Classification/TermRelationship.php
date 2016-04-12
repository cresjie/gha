<?php

namespace App\Models\Classification;

use Model;

class TermRelationship extends Model
{

	protected $table = 'classification_term_relationship';

	public $timestamps =  false;

	protected $fillable = [];

	static public function createRules()
	{
		return [
			'term_id' => 'required',
			'termable_id' => 'required',
			'termable_type' => 'required'
		];
	}
}