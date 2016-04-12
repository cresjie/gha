<?php
namespace App\Http\Controllers\Api\Geolocation;


use App\Http\Controllers\Controller;

use Input;
use Validator;
use Response;
use Auth;
use App;

use App\Models\Geolocation\Relationship;
use App\Models\GigEvent;
use App\Models\GigEventOrganizer;


class EventController extends Controller
{

	public function index()
	{
		return App::abort(404);
	}

	/**
	 * @http @param string trackable_id => refers to gig_event.id
	 *				string location
	 *						locality
	 *						administrative_level_1
	 *						country
	 *						coordinates
	 */
	public function store()
	{
		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => Input::get('trackable_id') ])->first();
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);

		$gigEvent = GigEvent::find( Input::get('trackable_id') );
		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		$location = new Relationship;
		$location->fill( Input::all() );
		$location->trackable_name = GigEvent::class;

		$rules = $gigEvent->publish ? Relationship::createRules() : Relationship::draftRules();
		$validator = Validator::make($location, $rules);
		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		if( $location->save() )
			return Response::json(['success' => true, 'data' => $location]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to save location.', 'error_code' => 1]);

	}

	public function show($id)
	{
		$location = Relationship::find($id);
		if( !$location )
			Response::json(['success' => false, 'error_msg' => 'Location doesn\'t exists.', 'error_code' => 404]);

		$gigEvent = GigEvent::find( $location->trackable_id );
		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		if( $gigEvent->privacy == 'private' ){
			return 'private';
		}

		return Response::json(['success' => true, 'data' => $location]);
	}

	public function update($id)
	{
		$location = Relationship::find($id);
		if( !$location )
			Response::json(['success' => false, 'error_msg' => 'Location doesn\'t exists.', 'error_code' => 404]);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $location->trackable_id ])->first();
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);

		$gigEvent = GigEvent::find( $location->trackable_id );
		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		$location->fill( Input::all() );
		$rules = $gigEvent->publish ? Relationship::createRules() : Relationship::draftRules();
		$validator = Validator::make($location, $rules);
		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		if( $location->save() )
			return Response::json(['success' => true, 'data' => $location]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to save location.', 'error_code' => 1]);

	}

	public function destroy($id)
	{
		$location = Relationship::find($id);
		if( !$location )
			Response::json(['success' => false, 'error_msg' => 'Location doesn\'t exists.', 'error_code' => 404]);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $location->trackable_id ])->first();
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);

		$gigEvent = GigEvent::find( $location->trackable_id );
		if( !$gigEvent ){
			$location->delete();
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);
		}

		if( $location->delete() )
			return Response::json(['success' => true, 'data' => $location]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to save location.', 'error_code' => 1]);
	}
}