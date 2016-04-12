<?php
namespace App\Http\Controllers\Api;

use Exception;

use App\Http\Controllers\Controller;
use Input;
use Response;
use Auth;
use Validator;
use Carbon\Carbon;

use App\Models\GigGroupMembers;
use App\Models\GroupDiscussionReply;
use App\Models\GroupDiscussion;

class GroupDiscussionReplyController extends Controller
{
	/**
	 * @http @param int gd_id
	 * @http @param int limit
	 * @http @param string order_direction
	 */
	public function index()
	{
		$discussion = GroupDiscussion::find( Input::get('gd_id') );

		if( !$discussion )
			return Response::json(['success' => false, 'error_msg' => 'Group discussion doesn\'t exists', 'error_code' => 404]);

		$theMember = GigGroupMembers::where(['group_id' => $discussion->group_id, 'user_id' => Auth::id()])->first();

		if( !$theMember )
			return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

		try
		{
			$replies = GroupDiscussionReply::where(['gd_id' => Input::get('gd_id')])
					->orderBy('created_at', Input::get('order_direction', 'desc') )
					->paginate( Input::get('limit', 20) );

			return $replies;
		}
		catch(Exception $e)
		{
			return Response::json(['success' => false, 'error_code' => $e->getCode()]);
		}
		
	}

	/**
	 * @http @param int gd_id
	 * @http @param string message
	 * 
	 * @return JSON success data
	 */
	public function store()
	{
		

		$discussion = GroupDiscussion::find( Input::get('gd_id') );

		if( !$discussion )
			return Response::json(['success' => false, 'error_msg' => 'Group discussion doesn\'t exists', 'error_code' => 404]);

		$theMember = GigGroupMembers::where(['group_id' => $discussion->group_id, 'user_id' => Auth::id()])->first();

		if( !$theMember )
			return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

		$validator = Validator::make(Input::all(), GroupDiscussionReply::createRules());

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]);

		$reply = new GroupDiscussionReply;
		$reply->fill( Input::all() );
		$reply->user_id = Auth::id();


		if( $reply->save() )
			return Response::json(['success' => true, 'data' => $reply]);

		return Response::json(['success' => false, 'error_code' => 1]);

	}

	public function show($id)
	{
		$reply = GroupDiscussionReply::find( $id );

		if( !$reply )
			return Response::json(['success' => false, 'error_msg' => 'Reply doesn\'t exists', 'error_code' => 404]);

		$discussion = GroupDiscussion::find( $reply->gd_id );

		if( !$discussion )
			return Response::json(['success' => false, 'error_msg' => 'Group discussion doesn\'t exists', 'error_code' => 404]);

		$theMember = GigGroupMembers::where(['group_id' => $discussion->group_id, 'user_id' => Auth::id()])->first();

		if( $theMember )
			return Response::json(['success' => true, 'data' => $reply]);

		return Response::json(['success' => false, 'error_code' => 1]);
	}

	public function update($id)
	{
		$reply = GroupDiscussionReply::find($id);

		if( !$reply )
			return Response::json(['success' => false, 'error_msg' => 'Reply doesn\'t exists', 'error_code' => 404]);

		if( $reply->user_id != Auth::id() )
			return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

		$reply->fill( Input::all() );

		$validator = Validator::make($reply->toArray(), GroupDiscussionReply::updateRules());

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]);

		if( $reply->save() )
			return Response::json(['success' => true, 'data' => $reply]);

		return Response::json(['success' => false, 'error_code' => 1]);

	}

	public function destroy($id)
	{
		$reply = GroupDiscussionReply::find($id);

		if( !$reply )
			return Response::json(['success' => false, 'error_msg' => 'Reply doesn\'t exists', 'error_code' => 404]);

		$discussion = GroupDiscussion::find( $reply->gd_id );

		if( !$discussion )
			return Response::json(['success' => false, 'error_msg' => 'Group discussion doesn\'t exists', 'error_code' => 404]);

		$theMember = GigGroupMembers::where(['group_id' => $discussion->group_id, 'user_id' => Auth::id()])->first();

		if( $reply->user_id != Auth::id() && !$theMember->is_admin)
			return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

		$reply->delete();

		return Response::json(['success' => true]);
	}
}