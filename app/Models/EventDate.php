<?php

namespace App\Models;

use App\Helpers\Eloquent\Model;
use DateTimeZone;
use Carbon\Carbon;
use Config;

class EventDate extends Model
{

	protected $table = 'event_date';

	protected $fillable = ['start','end','timezone'];

	//protected $dates = ['start', 'end'];


	static public function createRules()
	{
		return [
			'event_id' => 'required',
			'start' => 'required|date|max_date_to:end',
			'end' => 'required|date',
			'timezone' => 'in:'. implode(',', DateTimeZone::listIdentifiers())
		];
	}

	static public function draftRules()
	{
		return [
			'event_id' => 'required',
			'start' => 'date',
			'end' => 'date',
			'timezone' => 'in:'. implode(',', DateTimeZone::listIdentifiers())
		];
	}

	public function setStartAttribute($value)
	{
		$this->attributes['start'] = new Carbon($value, $this->timezone);
		$this->attributes['start']->setTimezone( Config::get('app.timezone') );
		var_dump($this->attributes['start']);
		return $this;
	}

	public function getStartAttribute($value)
	{
		$this->attributes['start'] = new Carbon((string)$value);
		$this->attributes['start']->setTimezone( $this->timezone );
		return $this->attributes['start'];
	}

	public function getEndAttribute($value)
	{
		$this->attributes['end'] = new Carbon((string)$value);
		$this->attributes['end']->setTimezone( $this->timezone );
		return $this->attributes['end']; 
	}
	
	public function attributesToArray()
	{
		$attributes = parent::attributesToArray();
		
		if( isset($attributes['start']) )
			$attributes['start'] = (string) $attributes['start'];

		if( isset($attributes['end']) )
		$attributes['end'] = (string) $attributes['end'];

		return $attributes;
	}
	
}