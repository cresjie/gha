<?php

namespace App\Http\Controllers;

use View;

class SettingsController extends Controller
{
	
	public function index()
	{
		return View::make('settings.index');
	}
}