<?php

/*
 * mostly used by the controllers
 * also use in models:
 * 		App\User 
 * 		App\Models\GigGroup
 *		App\models\GigGroupMembers
 * 		App\Http\Controllers\Controller\EventCategoryController
 */
namespace App\Helpers;


use DB;
use Str;
use Closure;



class Generator{
	
	const MIN_SLUG_LENGTH = 5;


	static public function id()
	{

		//return 17 - 19 digits;
		//tested: out of 10,000 - 100% unique
		//date() return 14 digits + 3 to 5 digits of rand()
		return date('YmdHis').rand(); 
	}

	static public function slug($table, $field, $title, $where = [], $index = 0, $min = self::MIN_SLUG_LENGTH, Closure $callback = null)
	{
		$slug = Str::slug( $title );

		//check the length of the title 
		
		if(strlen($slug) < $min )
		{
			$diff = $min - strlen($slug);
			for($i=0; $i < $diff; $i++)
			{
				$slug .= '0';
			}
		}
		
		$slug = $index ? "{$slug}-{$index}" : $slug; 

		$db = is_array($where) ? DB::table( $table )->where( $where ) : DB::table( $table )->whereRaw( $where );

		if( $callback )
			call_user_func( $callback, $db);

		$exists = $db->where([ $field => $slug ])->count( $field );

		if( $exists )
			return self::slug( $table, $field, $title, $where, $index+1, $min, $callback);
		else
			return $slug;
	}
}