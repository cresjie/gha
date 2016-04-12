<?php

namespace App\Http\Controllers;

use App\Models\Classification\EventCategory;
use App\User;

use View;
use Auth;

class GigEventController extends Controller
{

	public function index()
	{

	}

	public function create()
	{
		$event_categories = EventCategory::all();
		$user = User::with('usertype.account')->find( Auth::id() );

		return View::make('events.create', compact('event_categories','user'));
	}

	public function show($id)
	{

	}

}