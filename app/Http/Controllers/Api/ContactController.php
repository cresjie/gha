<?php
namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Models\Contact;
use App\User;

use Auth;
use Input;
use Validator;
use Response;
use Carbon\Carbon;

class ContactController extends Controller
{

	public function index()
	{
		//$this->show( Auth::id() );
		$contacts = null;

		switch ( Input::get('cmd') ) {
			case 'pending':
				//$contacts = Contact::where(['is_confirmed' => false, 'user_id' => Auth::id() ]);
				$contacts = User::whereRaw('(select count(*) from `contact` where `contact`.`is_confirmed` = false and `contact`.`requestor` = `user`.`id` and  `contact`.`user_id` = "'.Auth::id().'" ) >= 1');
				break;

			case 'my-request-pending':
				//$contacts = Contact::where(['is_confirmed', false, 'requestor' => Auth::id() ]);
				$contacts = User::whereRaw('(select count(*) from `contact` where `contact`.`is_confirmed` = false and  `contact`.`user_id` = `user`.`id` and `contact`.`requestor` = "'.Auth::id().'" ) >= 1');
				break;

			case 'contacts-requested':
				
				$contacts = User::whereRaw('(select count(*) from `contact` where `contact`.`is_confirmed` = true and  `contact`.`user_id` = `user`.`id` and `contact`.`requestor` = "'.Auth::id().'" ) >= 1');
				break;

			case 'added-me-as-contact':
				//$contacts = Contact::where(['is_confirmed' => true, 'user_id' => Auth::id() ]);
				$contacts = User::whereRaw('(select count(*) from `contact` where `contact`.`is_confirmed` = true and  `contact`.`requestor` = `user`.`id` and `contact`.`user_id` = "'.Auth::id().'" ) >= 1');
				break;

			case 'all':
				//all contacts either confirmed or pending
				
				$contacts = User::whereRaw('(select count(*) from `contact` where ( (`contact`.`requestor` = "'.Auth::id().'" and `contact`.`user_id` = `user`.`id` ) or (`contact`.`requestor` = `user`.`id` and `contact`.`user_id` = "'.Auth::id().'") )) >= 1');
				break;

			case 'search':
				$q = Input::get('q');

				$contacts = User::whereRaw('(select count(*) from `contact` where ( (`contact`.`requestor` = "'.Auth::id().'" and `contact`.`user_id` = `user`.`id` ) or (`contact`.`requestor` = `user`.`id` and `contact`.`user_id` = "'.Auth::id().'") )) >= 1')
							->whereRaw("(email LIKE '{$q}%' OR CONCAT(first_name,' ',last_name) LIKE '{$q}%' OR CONCAT(last_name,' ',first_name) LIKE '{$q}%')")
							->whereNotIn('id', Input::get('not_in_id',[]));
				break;
			
			default:
				//show all accepted contact list
				
				$contacts = User::whereRaw('(select count(*) from `contact` where ( (`contact`.`requestor` = "'.Auth::id().'" and `contact`.`user_id` = `user`.`id` ) or (`contact`.`requestor` = `user`.`id` and `contact`.`user_id` = "'.Auth::id().'") ) and `contact`.`is_confirmed` = true) >= 1');
				break;
		}

		
		//return $contacts->toSql();
		return $contacts->where('id','!=', Auth::id())
					->orderBy('first_name')
					->paginate( Input::get('limit', 20) );
	}

	public function store()
	{
		$validator = Validator::make(Input::all(), Contact::createRules());

		if( $validator->fails() )
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]);

		$userId = Input::get('user_id');
		$requestor = Auth::id();

		$contact = Contact::where(['requestor' => $requestor, 'user_id' => $userId])->orWhere(['requestor' => $userId, 'user_id' => $requestor])->first();

		if( $contact ){
			if($contact->is_confirmed )
				return Response::json(['success' => false, 'error_msg' => 'This user is already in your contacts.']);
			else
				return Response::json(['success' => false, 'error_msg' => 'You already sent a request to this user']);
		}

		$newContact = new Contact;
		$newContact->fill([
			'user_id' => $userId,
			'requestor' => $requestor
		]);

		if( $newContact->save() )
			return Response::json(['success' => true, 'data' => $newContact]);

		return Response::json(['success' => false, 'error_msg' => 'Error occured while saving', 'error_code' => 1]);
	}



	public function update($id)
	{
		$contact = Contact::find($id);

		if( $contact ){

			switch ( Input::get('cmd') ) {
				case 'seen':
					$contact->seen_at = new Carbon;
					break;

				case 'confirm':
					if( $contact->user_id == Auth::id() )
						$contact->is_confirmed = true;
					else
						return Response::json(['success' => false, 'error_msg' => 'Unauthorized', 'error_code' => 401]);
					break;
				
				default:
					$contact->is_confirmed = true;
					break;
			}

			if( $contact->update() ){
				return Response::json(['success' => true, 'data' => $contact]);
			}
		}
		return Response::json(['success' => false, 'error_msg' => 'Contact does\'t exists', 'error_code' => 404]);
	}

	/**
	 * 
	 * @param string|int id
	 */
	public function destroy($id)
	{

		switch ($id) {
			case 'whose':
				$contact = Contact::where(['requestor' => Auth::id(), 'user_id' => Input::get('user_id') ])->orWhere(['requestor' => Input::get('user_id'), 'user_id' => Auth::id()])->first();
				break;
			
			default:
				$contact = Contact::find($id);
				break;
		}
		

		if( $contact ){
			if( $contact->requestor == Auth::id() || $contact->user_id == Auth::id() ){
				// if the user is in the friend list
				$contact->delete();
				return Response::json(['success' => true, 'data' => $contact]);
			}
			return Response::json(['success' => false, 'error_msg' => 'Unauthorized', 'error_code' => 401]);
		}
		return Response::json(['success' => false, 'error_msg' => 'Contact does\'t exists', 'error_code' => 404]);
	}

	public function putIndex()
	{
		switch ( Input::get('cmd') ) {
			case 'value':
				# code...
				break;
			
			default:
				//update to seen all
				$result = Contact::where(['user_id' => Auth::id() ])->update(['seen_at' => new Carbon]);

				break;
		}

		return Response::json(['success' => true]);
	}
}