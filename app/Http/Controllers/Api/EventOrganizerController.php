<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use Auth;
use Input;
use Validator;
use Response;
use App;
use Config;

use App\Models\GigEvent;
use App\Models\GigEventOrganizer;


class EventOrganizerController extends Controller
{
	public function index()
	{	
		//list all organizer of an event
		return App::abort(404);
	}

	/**
	 * DB query reaches 5
	 */
	public function store()
	{
		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => Input::get('event_id') ])->first();
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);

		if( !$organizer->is_admin )
			return Response::json(['success' => false, 'error_msg' => "You're not allowed to add an organizer.", 'error_code' => 400]);

		$newOrganizer = GigEventOrganizer::where(['event_id' => Input::get('event_id'), 'user_id' => Input::get('user_id') ])->first();

		if( $newOrganizer ){ //if the organizer already exists.
			return Response::json(['success' => true, 'data' => $newOrganizer, 'msg' => 'Organizer already exists.']);
		}

		$newOrganizer = new GigEventOrganizer;
		$newOrganizer->fill( Input::all() );
		$newOrganizer->setAttributes([
			'event_id' => Input::get('event_id'),
			'user_id' => Input::get('user_id'),
			'added_by' => Auth::id()
		]);

		$validator = Validator::make($newOrganizer->toArray(), GigEventOrganizer::createRules());
		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]);

		$limit = Config::get('gha.usertype.gig_organizer.' . $organizer->usertype->account->code) ?: Config::get('gha.usertype.gig_organizer.standard');
		if( GigEventOrganizer::where(['event_id' => Input::get('event_id') ])->count() >= $limit)
			return Response::json(['success' => false, 'error_msg' => 'Number of organizer has reach the allowable limit for your account.']);

		if( $newOrganizer->save() )
			return Response::json(['success' => true, 'data' => $newOrganizer]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to add organizer', 'error_code' => 1]);
		

	}

	public function show($id)
	{

	}

	public function update($id)
	{
		$modifyOrganizer = GigEventOrganizer::find($id);
		if( !$modifyOrganizer )
			return Response::json(['success' => false, 'error_msg' => "Organizer doesn't exists.",'error_code' => 404]);

		if( $modifyOrganizer->is_publisher )
			return Response::json(['success' => false, 'error_msg' => 'Publisher cannot be modified.']);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $modifyOrganizer->event_id ])->first();
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);

		if( !$organizer->is_admin )
			return Response::json(['success' => false, 'error_msg' => "You're not allowed to update an organizer.", 'error_code' => 400]);

		
		$modifyOrganizer->fill( Input::all() );

		$validator = Validator::make($modifyOrganizer->toArray(), GigEventOrganizer::createRules());
		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]);


		if( $modifyOrganizer->save() )
			return Response::json(['success' => true, 'data' => $modifyOrganizer]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to update organizer', 'error_code' => 1]);
	}

	public function destroy($id)
	{
		$deleteOrganizer = GigEventOrganizer::find($id);
		if( !$deleteOrganizer )
			return Response::json(['success' => false, 'error_msg' => 'Organizer doesn\'t exists.', 'error_code' => 404]);

		if( $deleteOrganizer->is_publisher )
			return Response::json(['success' => false, 'error_msg' => 'Publisher cannot be leave or remove.']);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $deleteOrganizer->event_id ])->first();
		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);

		if( !$organizer->is_admin || $organizer->user_id != Auth::id() ) //if not admin or not a voluntary leave
			return Response::json(['success' => false, 'error_msg' => "You're not allowed to remove an organizer.", 'error_code' => 400]);

		$count = GigEventOrganizer::where(['event_id' => $deleteOrganizer->event_id])->count();

		if( $count <= 1 )
			return Response::json(['success' => false, 'error_msg' => 'Event should always have atleast one organizer.']);

		if( $deleteOrganizer->delete() )
			return Response::json(['success' => true, 'data' => $deleteOrganizer]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to delete organizer.']);

	}

}