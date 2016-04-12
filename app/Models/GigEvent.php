<?php

namespace App\Models;

use Purifier;
use App\Helpers\Eloquent\Model;

class GigEvent extends Model
{
	
	protected $table = 'gig_event';

	protected $fillable = ['title','slug','slogan','description','private_description','poster','location','requisite','paypal_email','privacy','publish']; //'start','end','timezone',

	protected static function boot()
	{
		parent::boot();
		static::registerModelEvent('saving',function($model){
			$model->description = Purifier::clean($model->description);
			$model->private_description = Purifier::clean($model->private_description);
		});

	}

	static public function createRules()
	{
		return [
			'meta_id' => 'required',
			'title' => 'required',
			'slug' => 'required|alpha_dash',
			'description' => 'required',
			'meta_id' => 'required',
			'requisite' => 'required|in:ticket,rsvp,none',
			//'paypal_email' => 'required_if:requisite,ticket',
			'privacy' => 'required|in:public,private'
			//'start' => 'required',
			//'end' => 'required'
		];
	}

	static public function draftRules()
	{
		return [
			'meta_id' => 'required',
			'title' => 'required',
			'slug' => 'required|alpha_dash',
			'requisite' => 'in:ticket,rsvp,none',
			'privacy' => 'in:public,private'
		];
	}
}