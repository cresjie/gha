<?php

namespace App\Helpers;

use App\User;
use App\Models\GigGroup;


class Slug
{
	static public function inRestrictedSlug($q)
	{
		return in_array($q, ['create','api','home','events','gig_group','users','edit','show','settings']);
	}

	static public function filterSlug($q)
	{
		return static::inRestrictedSlug($q) ? $q . '-1' : $q;
	}
	
	static public function gigGroup($q, $except = '')
	{
		$q = static::filterSlug($q);

		return  Generator::slug( (new GigGroup)->getTable(), 'slug', $q, [], 0, Generator::MIN_SLUG_LENGTH, function($q) use ($except) {
			$q->where('id', '!=', $except);
		});	
	}
	/**
	 * @param string $q
	 * @param string|int|array $except
	 */
	static public function user($q, $except = '')
	{
		$q = static::filterSlug($q);

		return Generator::slug( (new User)->getTable(),'slug', $q, [],0, Generator::MIN_SLUG_LENGTH, function($q) use ($except) {
			if( is_array($except) )
				$q->whereNotIn('id', $except);
			else
				$q->where('id', '!=', $except);
		});	
	}
}