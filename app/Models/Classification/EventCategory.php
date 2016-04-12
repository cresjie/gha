<?php

namespace App\Models\Classification;

use Model;

class EventCategory extends Terms
{

	protected $fillable = ['name','description','term_code','order','parent'];

	const TAXONOMY_ID = 1;


	public function newQuery()
	{
		$builder = $this->newQueryWithoutScopes();

		return $this->applyGlobalScopes($builder)
					->where('taxonomy_id','=', static::TAXONOMY_ID);
	}
	
}