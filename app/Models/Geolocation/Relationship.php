<?php

namespace App\Models\Geolocation;

use App\Helpers\Eloquent\Model;


class Relationship extends Model
{
	protected $table = 'geolocation_relationship';

	protected $fillable = ['locality','administrative_area_level_1','country','coordinates'];

	protected $hidden = ['trackable_name'];


	static public function createRules()
	{
		return [
			'trackable_id' => 'required|numeric',
			'trackable_name' => 'required',
			'country'	=> 'required',
			'coordinates' => 'required|array'
		];
	}

	static public function draftRules()
	{
		return [
			'trackable_id' => 'required|numeric',
			'trackable_name' => 'required',
			'country'	=> '',
			'coordinates' => 'array'
		];
	}


}