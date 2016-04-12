<?php
namespace App\Http\Controllers\Api\Classification;


use App\Http\Controllers\Controller;

use Input;
use Validator;
use Response;
use Auth;

use App\Models\GigEvent;
use App\Models\GigEventOrganizer;
use App\Models\Classification\Terms;
use App\Models\Classification\TermRelationship;


use App\Helpers\Generator;


class EventCategoryController extends Controller
{

	public function index()
	{

	}

	/**
	 * @http @param event_id
	 * @http @param array categories [id's] 
	 */
	public function store()
	{
		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => Input::get('event_id') ])->first();

		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);
		//if( !$organizer->is_admin || !$organizer->is_publisher)
		//	return Response::json(['success' => false, 'error_msg' => "You don't have enough privilege for this event", 'error_code' => 400, 'msg' => 'Unauthorized']);
		
		//delete all categories of the previous event 
		TermRelationship::where(['termable_id' => Input::get('event_id'), 'termable_type' => GigEvent::class ])->delete();

		$categoryIds = array_intersect( Input::get('categories'), Terms::where(['taxonomy_id' => 1])->lists('id')->toArray() );
		$query = [];

		foreach($categoryIds as $catId){
			array_push($query,[
				'id' => Generator::id(),
				'term_id' => $catId,
				'termable_id' => Input::get('event_id'),
				'termable_type' => GigEvent::class
			]);
		}

		if( count($query) && TermRelationship::insert($query) )
			return Response::json(['success' => true, 'data' => $query]);


		return Response::json(['success' => false, 'error_msg' => 'Unable to save categories.', 'error_code' => 1]);

	}

	public function update($id)
	{

	}

	public function delete($id)
	{
		$categoryRelation = TermRelationship::find($id);

		if( !$categoryRelation )
			return Response::json(['success' => false, 'error_msg' => 'Event category doesn\'t exists.', 'error_code' => 404]);

		$organizer = GigEventOrganizer::where(['user_id' => Auth::id(), 'event_id' => $categoryRelation->termable_id ])->first();

		if( !$organizer )
			return Response::json(['success' => false, 'error_msg' => "You're not an organizer for this event",'error_code' => 400,'msg' => 'Unauthorized']);
		//if( !$organizer->is_admin || !$organizer->is_publisher)
		//	return Response::json(['success' => false, 'error_msg' => "You don't have enough privilege for this event", 'error_code' => 400, 'msg' => 'Unauthorized']);

		if( $categoryRelation->delete() )
			return Response::json(['success' => false, 'data' => $categoryRelation]);

		return Response::json(['success' => false, 'error_msg' => 'Unable to delete event category.', 'error_code' => 1]);

	}
}