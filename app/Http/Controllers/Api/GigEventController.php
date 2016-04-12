<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use Input;
use Validator;
use Response;
use Auth;
use Str;
use URL;

use App\User;
use App\Models\GigEvent;
use App\Models\GigEventOrganizer;
use App\Models\EventTicket;
use App\Models\EventDate;
use App\Models\Classification\TermRelationship;
use App\Models\EventRsvp;

class GigEventController extends Controller
{

	public function index()
	{
		$r =Response::json(['success' => true]);
		return var_dump($r->getData()->success);
		return 123;
	}

	public function store()
	{
		$gigEvent = new GigEvent;
		$gigEvent->fill( Input::all() );
		$gigEvent->slug = $gigEvent->slug?: Str::slug($gigEvent->title);
		$gigEvent->meta_id = Auth::id();
		$gigEvent->meta_type = User::class;

		if( $gigEvent->publish )
			$validator = Validator::make( $gigEvent->toArray(), GigEvent::createRules() );	
		else
			$validator = Validator::make( $gigEvent->toArray(), GigEvent::draftRules() );	
		

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);
		
		

		if( $gigEvent->save() ){

			$publisher = new GigEventOrganizer;
			$publisher->setAttributes([
				'event_id' => $gigEvent->id,
				'user_id' => Auth::id(),
				'is_admin' => true,
				'is_publisher' => true
			]);

			if( $publisher->save() ){
				$gigEvent->organizer = $publisher;
			}
			return Response::json(['success' => true, 'data' => $gigEvent, 'redirect' => 'home']);
		}


		return Response::json(['success' => false, 'error_msg' => 'Unable to save event.','error_code' => 1]);
	}

	public function update($id)
	{
		$gigEvent = GigEvent::find($id);

		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesnt exists.', 'error_code' => 404]);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $gigEvent->id])->first();
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => 'You\'re not an organizer for this event','error_code' => 400,'msg' => 'Unauthorized']);

		if( !$organizer->is_admin && !$organizer->is_publisher )
			return Response::json(['success' => false, 'error_msg' => 'You don\'t have enough privilege for this event', 'error_code' => 400, 'msg' => 'Unauthorized']);

		$gigEvent->fill( Input::all() );

		if( $gigEvent->publish )
			$validator = Validator::make( $gigEvent->toArray(), GigEvent::createRules() );	
		else
			$validator = Validator::make( $gigEvent->toArray(), GigEvent::draftRules() );	


		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		if( $gigEvent->save() )
			return Response::json(['success' => true, 'data' => $gigEvent]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to save event.','error_code' => 1]);

	}

	/**
	 * @danger: slow speed in deleting draft event, too many quiries to delete because of its relation
	 */
	public function destroy($id)
	{
		$gigEvent = GigEvent::find($id);

		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesnt exists.', 'error_code' => 404]);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $gigEvent->id])->first();
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => 'You\'re not an organizer for this event','error_code' => 400,'msg' => 'Unauthorized']);

		if(  !$organizer->is_publisher )
			return Response::json(['success' => false, 'error_msg' => 'Only the publisher can delete an event.', 'error_code' => 400, 'msg' => 'Unauthorized']);

		if( !$gigEvent->publish ){
			
			//delete other relationship table
			EventDate::where(['event_id' => $gigEvent->id])->delete();
			EventTicket::where(['event_id' => $gigEvent->id])->delete();
			EventRsvp::where(['event_id' => $gigEvent->id])->delete();
			TermRelationship::where(['termable_id' => $gigEvent->id, 'termable_type' => GigEvent::class])->delete();
			GigEventOrganizer::where(['event_id' => $gigEvent->id])->delete();

			$gigEvent->forceDelete();
		}else{
			$gigEvent->delete();
		}
		return Response::json(['success' => false, 'error_msg' => 'Unable to delete event.','error_code' => 1]);
	}
}