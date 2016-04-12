<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Helpers\Slug;

use App\User;
use Auth;
use Response;
use Input;
use Validator;

class UserController extends Controller
{

	public function index()
	{

		$users = User::paginate();
		$users->each(function($user){$user->addHidden('email');});
		return $users;
	}
	public function show($id)
	{
		
		if( $id == 'me')
			$id = Auth::id();
		
		$user = User::find($id);
		if( $user ){
			if( $user->id != Auth::id() )
				$user->addHidden('email');
			
			return Response::json(['success' => true, 'data' => $user]);
		}

		return Response::json(['success' => false, 'error_msg' => 'User doesn\'t exists.', 'error_code' => 404]);
	}

	public function update($id)
	{
		if( $id == Auth::id() || $id == 'me'){
			$id = Auth::id();
			$user = User::find( Auth::id() );
			$user->fill( Input::except(['email']) );
			$user->slug = Slug::user( Input::get('slug') ? Input::get('slug') : Input::get('first_name'), $id  );

			$validator = Validator::make( $user->toArray(), User::updateRules() );

			if( $validator->fails() )
				return Response::json(['success' => false, 'error_msg' => $validator->messages()]);

			if( $user->save() )
				return Response::json(['success' => true, 'data' => $user]);

			return Response::json(['success' => false, 'error_msg' => 'Error occured while saving', 'error_code' => 1]);
		}
		return Response::json(['success' => false,'error_msg' => 'Unauthorized', 'error_code' => 401]);
	}
}