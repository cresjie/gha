<?php

namespace App\Http\Controllers;

use App\Models\Page;

use App;
use View;

class PageController extends Controller
{

	public function index()
	{

	}

	public function show($slug)
	{
		$page = Page::where(['slug' => $slug])->whereNotNull('published_at')->first();

		if( !$page )
			return App::abort(404);

		return View::make('page.show', compact('page'));
	}

}