<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use Input;
use Validator;
use Response;
use Auth;

use App\Models\EventRsvp;
use App\Models\GigEvent;
use App\Models\GigEventOrganizer;

class EventRsvpController extends Controller
{

	public function index()
	{

	}

	public function store()
	{
		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => Input::get('event_id') ])->first();

		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);
		
		$gigEvent = GigEvent::find( Input::get('event_id') );
		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		$rsvp = new EventRsvp;
		$rsvp->fill( Input::all() );
		$rsvp->event_id = Input::get('event_id');

		$rules = $gigEvent->publish ? EventRsvp::createRules() : EventRsvp::draftRules();
		$validator = Validator::make($rsvp->toArray(), $rules);
		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		if( $rsvp->save() )
			return Response::json(['success' => true, 'data' => $rsvp]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to save event rsvp.', 'error_code' => 1]);
	}

	public function update($eventId)
	{
		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $eventId ])->first();

		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);

		$rsvp = EventRsvp::find($eventId);
		if( !$rsvp )
			return Response::json(['success' => false, 'error_msg' => 'Event rsvp doesn\'t exists.', 'error_code' => 404]);

		$gigEvent = GigEvent::find( $eventId );
		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		$rsvp->fill( Input::all() );
		
		$rules = $gigEvent->publish ? EventRsvp::createRules() : EventRsvp::draftRules();
		$validator = Validator::make($rsvp->toArray(), $rules );
		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		if( $rsvp->save() )
			return Response::json(['success' => true, 'data' => $rsvp]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to update event rsvp.', 'error_code' => 1]);
	}

	public function destroy($eventId)
	{
		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $eventId ])->first();

		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);

		$rsvp = EventRsvp::find($eventId);
		if( !$rsvp )
			return Response::json(['success' => false, 'error_msg' => 'Event rsvp doesn\'t exists.', 'error_code' => 404]);

		if( $rsvp->delete() )
			return Response::json(['success' => true, 'data' => $rsvp]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to delete event rsvp.', 'error_code' => 1]);
	}
}