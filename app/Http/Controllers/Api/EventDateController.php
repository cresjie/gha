<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use Input;
use Validator;
use Response;
use Auth;

use App\Models\EventDate;
use App\Models\GigEventOrganizer;
use App\Models\GigEvent;

use App\Helpers\Generator;
use App\Helpers\Directory\DateTimeZone;

class EventDateController extends Controller
{

	public function index()
	{

	}


	/**
	 * @http @param string event_id
	 * 				date start
	 *				date end
	 *				string timezone
	 *
	 */
	public function store()
	{
		$organizer = GigEventOrganizer::where(['event_id' => Input::get('event_id'), 'user_id' => Auth::id() ])->first();
		
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => 'Unauthorized']);

		$gigEvent = GigEvent::find( Input::get('event_id') );

		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		if( Input::get('dates') ){ //multiple
			$dates = Input::get('dates');

			$i = 0;
			foreach($dates as &$date){
				$date['id'] = Generator::id();
				$date['event_id'] = Input::get('event_id');
				$date['timezone'] = Input::get('timezone') ?: DateTimeZone::byCountry(Input::get('country'));

				$rules = $gigEvent->publish ? EventDate::createRules() : EventDate::draftRules();
				$validator = Validator::make($date, $rules);
				if( $validator->fails() )
					return Response::json(['success' => false, 'error_msg' => $validator->messages(), 'index' => $i ]);

				$i++;
			}

			if( EventDate::insert($dates) ) 
				return Response::json(['success' => true, 'data' => $dates]);
		}else{
			$date = new EventDate;
			$date->event_id = Input::get('event_id');
			$date->timezone = Input::get('timezone') ?: DateTimeZone::byCountry(Input::get('country'));
			$date->fill( Input::all() );

			$rules = $gigEvent->publish ? EventDate::createRules() : EventDate::draftRules();
			$validator = Validator::make($date->toArray(), $rules );
			if($validator->fails() )
				return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

			if( $date->save() )
				return Response::json(['success' => true, 'data' => $date]);
 		}

		return Response::json(['success' => false, 'error_msg' => 'Unable to save date.', 'error_code' => 1]);
	}

	public function update($id)
	{

		$date = EventDate::find($id);
		if( !$date )
			return Response::json(['success' => false ,'error_msg' => 'Event date doesn\'t exists.']);

		$organizer = GigEventOrganizer::where(['event_id' => $date->event_id, 'user_id' => Auth::id() ])->first();
		
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => 'Unauthorized']);

		$gigEvent = GigEvent::find( $date->event_id );
		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);


		$date->fill( Input::all() );
		$date->timezone = Input::get('timezone') ?: DateTimeZone::byCountry(Input::get('country'));

		$rules = $gigEvent->publish ? EventDate::createRules() : EventDate::draftRules();
		$validator = Validator::make($date->toArray(), $rules );
		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		if( $date->save() )
			return Response::json(['success' => true, 'data' => $date]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to update date.', 'error_code' => 1]);
	}

	public function destroy($id)
	{
		$organizer = GigEventOrganizer::where(['event_id' => Input::get('event_id'), 'user_id' => Auth::id() ])->first();
		
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => 'Unauthorized']);

		$date = EventDate::find($id);

		if( !$date )
			return Response::json(['succ' => false ,'error_msg' => 'Event date doesn\'t exists.']);

		if( $date->delete() )
			return Response::json(['success' => true, 'data' => $date]);

		return Response::json(['success' => false ,'error_msg' => 'Unable to delete event date.']);
	}
}