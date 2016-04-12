<?php
namespace App\Http\Controllers\Api;

use Exception;

use App\Http\Controllers\Controller;
use Input;
use Response;
use Auth;
use Validator;
use Carbon\Carbon;


use App\User;
use App\Models\GigGroupMembers;
use App\Models\GigGroup;

use App\Helpers\Generator;
use App\Helpers\Query;

class GigGroupMembersController extends Controller 
{

	protected $limit = 20;
	protected $httpParams = [
		'limit' => 20,
		'order_by' => [
			'is_admin' => 'desc',
			'first_name' => 'asc'
		]
	];

	
	/**
	 * @http @param int group_id
	 * @http @param int limit
	 */

	public function index()
	{
		if( Input::has('group_id') )
		{
			

			$gigGroup = GigGroup::find( Input::get('group_id') );

			if( !$gigGroup )
				return Response::json(['success' => false, 'error_msg' => 'Group doesn\'t exists', 'error_code' => 404]);

			$theMember = GigGroupMembers::where(['group_id' => $gigGroup->id, 'user_id' => Auth::id()])->first();

			if( $gigGroup->privacy == 'public' || $theMember ) //if group is public or the requestor is a member
			{
				//$gigMembers = GigGroupMembers::with('user')->where(['group_id' => $gigGroup->id])->paginate( $limit );

				try{
					
					switch ( Input::get('cmd') ) {
						case 'pending':
							$members = GigGroupMembers::where(['group_id' => $gigGroup->id, 'pending' => true])
										->with('user')
										->leftJoin('user','gig_group_members.user_id','=','user.id')
										->select('gig_group_members.*');
							break;

						case 'admin':
							$members = GigGroupMembers::where(['group_id' => $gigGroup->id, 'pending' => false,'is_admin' => true])
										->with('user')
										->leftJoin('user','gig_group_members.user_id','=','user.id')
										->select('gig_group_members.*');
							
							break;
						case 'members':
							$members = GigGroupMembers::where(['group_id' => $gigGroup->id, 'pending' => false, 'is_admin' => false])
										->with('user')
										->leftJoin('user','gig_group_members.user_id','=','user.id')
										->select('gig_group_members.*');
							break;

						case 'all-members':
						default:
							$members = GigGroupMembers::where(['group_id' => $gigGroup->id, 'pending' => false])
										->with('user')
										->leftJoin('user','gig_group_members.user_id','=','user.id')
										->select('gig_group_members.*');				
							break;
					}

					$members = Query::httpQuery( $members, Input::all(), $this->httpParams )
										->paginate( Input::get('limit', 20) );
					return $members;
					

				}
				catch(Exception $e)
				{
					return Response::json(['success' =>false, 'error_code' => $e->getCode()]);
				}
				//return Response::json(['success' => true, 'data' => $users]);
			}
		}

		return Response::json();
	}

	protected function _singleInsert()
	{
		$validator = Validator::make(Input::all(), GigGroupMembers::createRules());

		if( $validator->fails() )
				return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		$theMember = GigGroupMembers::where(['group_id' => Input::get('group_id'), 'user_id' => Auth::id() ])->first();

		// if the requestor is not a member
		if( !$theMember )
			return Response::json(['success' => false, 'error_msg' => 'Unauthorized', 'error_code' => 401]);

		$user = User::where('user_id', '=', Input::get('user_id') )->whereHas('member_of', function($q){
					$q->where('group_id', '=', Input::get('group_id'));
				})->first();

		if( $user ){ // user is already in the group

		}

		$newMember = new GigGroupMembers;
		$newMember->fill( Input::all() );
		$newMember->added_by = Auth::id();
		$newMember->pending = $theMember->is_admin ? false : true; // if the theMember is not an admin, sets the pending to true
		$newMember->added_at = Carbon::now();
		
		if( $newMember->save() )
			return Response::json(['success' => true, 'data' => $newMember]);

		return Response::json(['success' => false, 'error_msg' => 'Error occured while saving', 'error_code' => 1]);
	}

	protected function _multipleInsert()
	{
		$validator = Validator::make(Input::all(), ['group_id' => 'required', 'user_ids' => 'required']);

			if( $validator->fails() )
				return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

			$theMember = GigGroupMembers::where(['group_id' => Input::get('group_id'), 'user_id' => Auth::id() ])->first();


			// if the requestor is not a member
			if( !$theMember )
				return Response::json(['success' => false, 'error_msg' => 'Unauthorized', 'error_code' => 401]);

			/**
			 *check if the @http @param is an array of id
			 * checks the @http @param user_ids in the user table if it exists
			 * and retrieve only the user that aren't member of the group
			 */
			if( !is_array(Input::get('user_ids')) )
				return Response::json(['success' => false, 'error_msg' => 'Bad request', 'error_code' => 400]);

			$users = user::whereIn('id', Input::get('user_ids') )->whereDoesntHave('member_of', function($q){
				$q->where('group_id', '=', Input::get('group_id'));
			})->get();
			

			
			$query = [];
			foreach($users as $user) {
				array_push($query,[
					'id' => Generator::id(),
					'group_id' => Input::get('group_id'),
					'user_id' => $user->id,
					'added_by' => Auth::id(),
					'pending' => $theMember->is_admin ? false : true, // if the theMember is not an admin, sets the pending to true
					'added_at' => Carbon::now(),
					'updated_at' => Carbon::now()
				]);
			}


			if( GigGroupMembers::insert($query) )
				return Response::json(['success' => true, 'msg' => 'Succesfully added']);

		return Response::json(['success' => false, 'error_msg' => 'Error occured while saving', 'error_code' => 1]);
	}

	/**
	 * @http @param int group_id
	 * @http @param int|array user_ids
	 */
	public function store()
	{	
		return  Input::has('user_ids')  ? $this->_multipleInsert() : $this->_singleInsert();		

	}

	
	
	public function show($id)
	{
		/*
		$gigMember = GigGroupMembers::find($id);

		if( !$gigMember )
			return Response::json(['success' => false, 'error_msg' => 'Member doesn\'t exists', 'error_code' => 404], 404);

		return Response::json(['success' => false, 'error_msg' => 'Error occured while retrieving', 'error_code' => 1]);
		*/
 	}
	
 	/**
 	 * @required @http @param string cmd [approve, set-as-admin, remove-as-admin]
	 */
	public function update($id)
	{
		$gigMember = GigGroupMembers::find($id);

		if( !$gigMember )
			return Response::json(['success' => false, 'error_msg' => 'Member doesn\'t exists', 'error_code' => 404]);

		$theMember = GigGroupMembers::where(['user_id' => Auth::id(), 'group_id' => $gigMember->group_id])->first();

		if( !$theMember )
			return Response::json(['success' => false, 'error_code' => 'Unauthorized', 'error_code' => 401]);

		if( !$theMember->is_admin )
			return Response::json(['success' => false, 'error_msg' => 'Unauthorized', 'error_code' => 401]);

		switch( Input::get('cmd') ){
			case 'approve':
				$gigMember->fill([
					'pending' => false,
					'approved_by' => Auth::id(),
					'approved_at' => Carbon::now()
				]);
			break;

			case 'set-as-admin':
				$gigMember->is_admin = true;
			break;

			case 'remove-as-admin':
				$gigMember->is_admin = false;
			break;
		}

		if( $gigMember->save() )
			return Response::json(['success' => true, 'data' => $gigMember]);


		return Response::json(['success' => false, 'error_msg' => 'Error occured while updating', 'error_code' => 1]);
	}

	/**
	 * Only admin is the last one to leave the group
	 * Only admin can remove member
	 * member can leave the group
	 */
	public function destroy($id)
	{

		$gigMember = GigGroupMembers::find($id);

		if( !$gigMember )
			return Response::json(['success' => false, 'error_msg' => 'Member doesn\'t exists']);

		$theMember = GigGroupMembers::where(['user_id' => Auth::id(), 'group_id' => $gigMember->group_id])->first();

		if( $theMember->is_admin || $theMember->user_id == $gigMember->user_id ) // if the member is admin or the member remove himself from the group
		{
			if( $theMember->is_admin ){
				$totalAdmins = GigGroupMembers::where(['group_id' => $id, 'is_admin' => true])->count();
				$totalMembers = GigGroupMembers::where(['group_id' => $id])->get();

				if( $totalAdmins ==1 && $totalMembers == 1){ // if he/she is the last member
					$gigGroup = GigGroup::find( $gigMember->group_id );

					if( $gigGroup->delete() ) // automatically delete the group if the last member leaves
						return Response::json(['success' => true]);

					return Response::json(['success' => false, 'error_msg' => 'Error occured while removing', 'error_code' => 1]);
				}
				else if( $totalAdmins == 1) {
					return Response::json(['success' => false, 'error_msg' => 'There should be atleast one admin in a group.<br> Assign a new admin before leaving the group']);
				}
			}

			if( $gigMember->delete() )
				return Response::json(['success' => true]);
		}	


		return Response::json(['success' => false, 'error_msg' => 'Error occured while removing', 'error_code' => 1]);
	}
}

