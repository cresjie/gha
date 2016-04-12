<?php

namespace App\Models;

use App\Helpers\Eloquent\Model;

class Page extends Model
{
	protected $table = 'page';

	protected $fillable = ['title','slug','content'];

	static public function createRules()
	{
		return [
			'title' => 'required',
			'slug' => 'required|alpha_dash',
			'content' => 'required'
		];
	}

	static public function draftRules()
	{
		return [
			'title' => 'required',
			'slug' => 'required|alpha_dash'
		];
	}

	public function author()
	{
		return $this->belongsTo('App\User','user_id','id');
	}

	public function user()
	{
		return $this->author();
	}
}
