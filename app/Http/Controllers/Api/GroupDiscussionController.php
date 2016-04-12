<?php
namespace App\Http\Controllers\Api;

use Exception;

use App\Http\Controllers\Controller;
use Input;
use Response;
use Auth;
use Validator;
use Carbon\Carbon;

use App\Models\GroupDiscussion;
use App\Models\GigGroupMembers;
use App\Models\GroupDiscussionReply;

class GroupDiscussionController extends Controller
{

	public function index()
	{
		try
		{
			if( Input::get('group_id') )
			{
				$groupId = Input::get('group_id');

				$theMember = GigGroupMembers::where(['group_id' => $groupId, 'user_id' => Auth::id()])->first();

				if( !$theMember )
					return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

				$discussions = GroupDiscussion::where(['group_id' => $groupId])
					->orderBy('created_at', Input::get('order_direction', 'asc'))
					->paginate( Input::get('limit', 20) );

				return $discussions;
			}
		}
		catch(Exception $e)
		{
			return Response::json(['success' => false, 'error_code' => $e->getCode()]);
		}

		return Response::json(['success' => false, 'error_code' => 1]);
	}

	/**
	 * @http @param int group_id
	 * @http @param string subject
	 * @http @param string message
	 *
	 * @return JSON success data
	 */
	public function store()
	{
		$validator = Validator::make(Input::all(), GroupDiscussion::createRules());

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]);

		$theMember = GigGroupMembers::where(['group_id' => Input::get('group_id'), 'user_id' => Auth::id()])->first();

		if( !$theMember )
			return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

		$discussion = new GroupDiscussion;
		$discussion->fill( Input::all() );
		$discussion->user_id = Auth::id();

		if( $discussion->save() )
			return Response::json(['success' => true, 'data' => $discussion]);

		return Response::json(['success' => false, 'error_code' => 1]);
	}

	public function show($id)
	{
		$discussion = GroupDiscussion::with('user')->with('discussion_replies')->find($id);

		if( !$discussion )
			return Response::json(['success' => false, 'error_msg' => 'Group discussion doesn\'t exists', 'error_code' => 404]);

		$theMember = GigGroupMembers::where(['group_id' => $discussion->group_id, 'user_id' => Auth::id()])->first();

		if( !$theMember )
			return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

		return Response::json(['success' => true, 'data' => $discussion]);
	}

	public function update($id)
	{
		$discussion = GroupDiscussion::find($id);

		if( !$discussion )
			return Response::json(['success' => false, 'error_msg' => 'Group discussion doesn\'t exists', 'error_code' => 404]);

		if( $discussion->user_id != Auth::id() )
			return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

		$discussion->fill( Input::all() );

		$validator = Validator::make($discussion->toArray(), GroupDiscussion::updateRules());

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]);
		
		if( $discussion->save() )
			return Response::json(['success' => true, 'data' => $discussion]);

		return Response::json(['success' => false, 'error_code' => 1]);

	}

	public function destroy($id)
	{
		GroupDiscussionReply::where(['gd_id' => $id])->delete();
		GroupDiscussion::where(['id' => $id])->delete();

		return Response::json(['success' => true]);
	}
}