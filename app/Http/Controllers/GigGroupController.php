<?php

namespace App\Http\Controllers;

use View;
use App;
use Auth;

use App\Models\GigGroup;
use App\Models\GigGroupMembers;
class GigGroupController extends Controller
{
	public function index()
	{
		
	}

	public function create()
	{
		return View::make('gig_group.create');
	}

	public function show($slug)
	{
		$gig_group = is_numeric($slug) ? GigGroup::find($slug) : GigGroup::where(['slug' => $slug])->first();

		if( !$gig_group )
			return App::abort(404);


		$the_member = Auth::check() ? GigGroupMembers::where(['user_id' => Auth::id(), 'group_id' => $gig_group->id])->first() : null;

		return View::make('gig_group.show',compact('gig_group', 'the_member'));
		
	}
}