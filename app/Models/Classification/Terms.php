<?php

namespace App\Models\Classification;

use Model;

class Terms extends Model
{

	protected $table = 'classification_terms';

	public $timestamps =  false;

	protected $fillable = ['taxonomy_id','name','description','term_code','order','parent'];

	static public function createRules()
	{
		return [
			'taxonomy_id' => 'required|numeric',
			'name' => 'required'
		];
	}
}