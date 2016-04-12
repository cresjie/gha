<?php
namespace App\Http\Controllers\Api;

use Exception;

use App\Http\Controllers\Controller;
use Input;
use Response;
use Auth;
use Validator;
use URL;

use App\Models\GigGroup;
use App\Models\GigGroupMembers;

use App\Helpers\Generator;
use App\Helpers\Slug;

class GigGroupController extends Controller
{
	public function index()
	{
		try
		{
			$gigGroups = GigGroup::whereHas('gig_members', function($q){
				$q->where([
					'user_id' => Auth::id(),
					'pending' => false
				]);
			})
			->orderBy( Input::get('order_field', 'name'), Input::get('order_direction','asc') )
			->paginate( Input::get('limit', 20) );

			return $gigGroups;
		}
		catch(Exception $e)
		{
			return Response::json(['success' => false, 'error_code' => $e->getCode()]);
		}
		
		//return Response::json(['success' => true, 'data' => $gigGroup]);
	}


	public function store()
	{
		$validator = Validator::make(Input::all(), GigGroup::createRules());

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]);

		$gigGroup = new GigGroup;
		$gigGroup->fill( Input::all() );
		$gigGroup->fill([

			//dont trust the user the @http @param slug might be a malicious
			'slug' => Input::has('slug') ? Slug::gigGroup( Input::get('slug') ) : Slug::gigGroup( Input::get('name') ), 
			'created_by' => Auth::id()
		]);

		if( $gigGroup->save() ){
			/* automatically sets the first member of the group (admin|creator)*/
			$admin = new GigGroupMembers;
			$admin->fill([
				'group_id' => $gigGroup->id,
				'user_id' => Auth::id(),
				'is_admin' => true,
			]);

			$admin->save();

			$gigGroup->members = $admin;
			return Response::json(['success' => true, 'data' => $gigGroup, 'redirect' => URL::route('gig_group.show', $gigGroup->slug) ]);
		}

		return Response::json(['success' => false, 'error_msg' => 'Error occured while saving', 'error_code' => 1]);
	}

	/**
	 * @http @param string cmd [pending-members, members, 'all-members']
	 */
	public function show($id)
	{
		
		$gigGroup = GigGroup::find($id);

		if( !$gigGroup )
			return Response::json(['success' => false, 'error_msg' => 'Group doesn\'t exists', 'error_code' => 404]);

		$theMember = GigGroupMembers::where(['user_id' => Auth::id(), 'group_id' => $id])->first();

		// if the group is public or the the group is private and the requestor is a member
		if( $gigGroup->privacy == 'public' || ( $gigGroup->privacy == 'private' && $theMember ) ) 
		{
			switch( Input::get('cmd') ){
				case 'pending-members':
					$gigGroup->gig_members = GigGroupMembers::with('user')->where(['group_id' => $gigGroup->id, 'pending' => true])->get();
				break;

				case 'members':
					$gigGroup->gig_members = GigGroupMembers::with('user')->where(['group_id' => $gigGroup->id, 'pending' => false])->get();
				break;

				case 'all-members':
					$gigGroup->gig_members = GigGroupMembers::with('user')->where(['group_id' => $gigGroup->id])->get();
				break;
				
			}
			return Response::json(['success' => true, 'data' => $gigGroup]);
		}


		return Response::json(['success' => false, 'error_msg' => 'Error occured while retrieving', 'error_code' => 1]);
		
		
	}
	public function update($id)
	{
		$theMember = GigGroupMembers::where(['user_id' => Auth::id(), 'group_id' => $id])->first();

		if( !$theMember )
			return Response::json(['success' => false, 'error_msg' => 'Your not a member of the group', 'error_code' => 401]);

		if( !$theMember->is_admin )
			return Response::json(['success' => false, 'error_msg' => 'Only admin can update the group', 'error_code' => 401]);

		$gigGroup = GigGroup::find($id);

		if( !$gigGroup )
			return Response::json(['success' => false, 'error_msg' => 'Group doesn\'t exists', 'error_code' => 404]);

		$gigGroup->fill( Input::all() );
		$gigGroup->slug = Input::has('slug') ? Slug::gigGroup( Input::get('slug'), $gigGroup->id ) : Slug::gigGroup( Input::get('name'), $gigGroup->id);
		
		$validator = Validator::make($gigGroup->toArray(), GigGroup::updateRules() );

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages() ]);

		if( $gigGroup->save() )
			return Response::json(['success' => true, 'data' => $gigGroup]);

		return Response::json(['success' => false, 'error_msg' => 'Error occured while saving', 'error_code' => 1]);
	}


}