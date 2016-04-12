<?php

namespace App\Helpers\Directory;

use DateTimeZone as BaseDTZ;
use Config;

class DateTimeZone extends BaseDTZ
{
	static function byCountry($name, $failSafe = true){
		$abbr = Country::nameToAbbr($name);
		return self::byCountryAbbr($abbr, $failSafe);
	}

	static function byCountryAbbr($abbr, $failSafe = true){
		$abbr = strtoupper($abbr);

		if($abbr)
			$tz = self::listIdentifiers(DateTimeZone::PER_COUNTRY, $abbr );

		if( !empty($tz) )
			return $tz[0];

		if( empty($tz) && $failSafe)
			return Config::get('app.timezone');
		
		return null;
	}

	static public function timezoneByCountry()
	{
		$timezones = [];
		foreach(COuntry::codes() as $code) {
			$tz = self::byCountryAbbr($code);
			if($tz)
				array_push($timezones, $tz);
		}

		return $timezones;
	}
}