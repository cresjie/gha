<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use Input;
use Validator;
use Response;
use Auth;

use App\Models\EventTicket;
use App\Models\GigEventOrganizer;
use App\Models\GigEvent;

use App\Helpers\Generator;

class EventTicketController extends Controller
{

	public function index()
	{

	}

	
	public function store()
	{
		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => Input::get('event_id') ])->first();

		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);
		if( !$organizer->is_admin || !$organizer->is_publisher)
			return Response::json(['success' => false, 'error_msg' => "You don't have enough privilege for this event", 'error_code' => 400, 'msg' => 'Unauthorized']);

		$gigEvent = GigEvent::find( Input::get('event_id') );
		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		if( Input::get('tickets') ){
			return Response::json(['success' => false]);
			$tickets = Input::get('tickets');
			$i = 0;
			foreach( $tickets as &$ticket ){
				$ticket['id'] = Generator::id();
				$ticket['event_id'] =  Input::get('event_id');
				$ticket['currency']= Input::get('currency');
				
				$rules = $gigEvent->publish ? EventTicket::createRules() : EventTicket::draftRules();
				$validator = Validator::make($ticket, $rules);
				if( $validator->fails() )
					return Response::json(['success' => false, 'error_msg' => $validator->messages(), 'index' => $i ]);

				$i++;
			}

			if( EventTicket::insert($tickets) )
				return Response::json(['success' => true, 'data' => $tickets]);

		}else{
			$ticket = new EventTicket;
			$ticket->fill( Input::all() );
			$ticket->event_id = Input::get('event_id');

			$rules = $gigEvent->publish ? EventTicket::createRules() : EventTicket::draftRules();
			return $ticket;
			$validator = Validator::make($ticket->toArray(), $rules );

			if( $validator->fails() )
				return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

			if( $ticket->sort_number ) { //if the sort_number is set
				//increment other tickets inorder to insert a new one
				EventTicket::where(['event_id' => $ticket->event_id])->where('sort_number','>', $ticket->sort_number)->increment('sort_number');
			}else{
				$max = EventTicket::where(['event_id' => $ticket->event_id])->max('sort_number');
				$ticket->sort_number = $max+1;
			}

			if( $ticket->save() )
				return Response::json(['success' => true, 'data' => $ticket]);

		}

		return Response::json(['success' => false, 'error_msg' => 'Unable to save tickets','error_code' => 1]);
	}

	public function update($id)
	{
		
		$ticket = EventTicket::find($id);

		if( !$ticket )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' =>$ticket->event_id ])->first();

		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);
		if( !$organizer->is_admin || !$organizer->is_publisher)
			return Response::json(['success' => false, 'error_msg' => "You don't have enough privilege for this event", 'error_code' => 400, 'msg' => 'Unauthorized']);

		$gigEvent = GigEvent::find( $ticket->event_id );
		if( !$gigEvent )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);


		$ticket->fill( Input::all() );

		$rules = $gigEvent->publish ? EventTicket::createRules() : EventTicket::draftRules();
		$validator = Validator::make($ticket->toArray(), $rules );

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		//if sort_number was modified
		if( isset($ticket->getDirty()['sort_number']) ){
			//if the new sort_number is greater than the old one
			//eg: 5 tickets, change from 2 become 4
			if( $ticket->getDirty()['sort_number'] > $ticket->getOriginal()['sort_number'] ){
				EventTicket::where(['event_id' => $ticket->event_id])
							->where('id', '!=', $ticket->id)
							->where('sort_number', '<', $ticket->getOriginal()['sort_number'])
							->where('sort_number', '>=', $ticket->getDirty()['sort_number'])
							->decrement();
			}else if( $ticket->getDirty()['sort_number'] < $ticket->getOriginal()['sort_number'] ) {
				//eg: from 4 -> 2
				EventTicket::where(['event_id' => $ticket->event_id])
							->where('id', '!=', $ticket->id)
							->where('sort_number','<=', $ticket->getDirty()['sort_number'])
							->where('sort_number', '>', $ticket->getOriginal()['sort_number'])
							->increment();
			}
		}

		if( $ticket->save() )
			return Response::json(['success' => true, 'data' => $ticket]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to update ticket.', 'error_msg' => 1]);
	}

	public function destroy($id)
	{
		$ticket = EventTicket::find($id);

		if( !$ticket )
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' =>$ticket->event_id ])->first();

		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);
		if( !$organizer->is_admin || !$organizer->is_publisher)
			return Response::json(['success' => false, 'error_msg' => "You don't have enough privilege for this event", 'error_code' => 400, 'msg' => 'Unauthorized']);

		$gigEvent = GigEvent::find( $ticket->event_id );
		if( !$gigEvent ){
			$ticket->forceDelete();
			return Response::json(['success' => false, 'error_msg' => 'Event doesn\'t exists.']);
		}

		$deleteResult = $gigEvent->publish ? $ticket->delete() : $ticket->forceDelete();

		if( $deleteResult )
			return Response::json(['success' => true, 'data' => $ticket]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to delete ticket', 'error_code' => 1]);
	}
}