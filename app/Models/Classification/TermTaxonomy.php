<?php

namespace App\Models\Classification;

use Model;

class TermTaxonomy extends Model
{
	protected $table = 'classification_term_taxonomy';

	protected $fillable = ['title', 'description','taxonomy_code','parent'];

	static public function createRules()
	{
		return [
			'title' => 'required',
			'taxonomy_code' => 'required'
		];
	}
}