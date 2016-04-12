<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Input;
use Response;
use Auth;
use Str;

use App\Helpers\Generator;
use App\Helpers\Slug;


class SlugController extends Controller
{

	public function anyIndex()
	{
		$q = Slug::filterSlug(Input::get('q'));

		return Response::json(['slug' => Str::slug($q) ]);
	}
	
	/**
	 * @http @param q
	 * @http @param except (optional)
	 */
	public function anyGigGroup()
	{
		$q = Input::get('q');

		/*
		$slug = Generator::slug('gig_group', 'slug', $q, [], 0, Generator::MIN_SLUG_LENGTH, function($q){
			$q->where('id', '!=', Input::get('except', '') );
		});
		*/
		$slug = Slug::gigGroup( Input::get('q'), Input::get('except', '') );
		
		return Response::json(['slug' => $slug]);
	}


	/**
	 * @http @param q
	 */
	public function anyUser()
	{
		$q = Input::get('q');

		$slug = Generator::slug('user', 'slug', $q, [], 0, Generator::MIN_SLUG_LENGTH, function($q){
			if( Input::get('except') )
				$q->where('id', '!=', Input::get('except'));
			if( Input::get('except_me') ){
				$q->where('id', '!=', Auth::id());
			}
			
		});

		return Response::json(['success' => true, 'slug' => $slug]);
	}

	
}