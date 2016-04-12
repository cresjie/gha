<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Helpers\Upload\Image;
use App\Helpers\Generator;

use Input;
use Response;
use Validator;
use Auth;
use Exception;
use Image as Intervention_Image;


class UploadController extends Controller
{


	public function upload($objectType)
	{
		$objectType = str_replace('-','_', $objectType);

	}

	public function postEventDescriptionImg()
	{
		$validator = Validator::make(['img' => Input::file('img')], Image::acceptRules() );

		if($validator->fails())
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]); 

		$result = Image::store(Input::file('img'),Auth::id() . '-' . Generator::id(),'event_description_img');

		unset($result['base_path']);
		return Response::json(['success' => true, 'link' => $result['link'], 'data' => $result]);
	}

	public function postProfileImage()
	{
		
		$validator = Validator::make(['img' => Input::file('img')], Image::acceptRules() );

		if($validator->fails())
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]); 
		

		$result = Image::store(Input::file('img'), Auth::id(),'user_profile_img', Input::all());

		$user = Auth::user();
		$user->profile_img = $result['filename'];
		$user->save();

		unset($result['base_path']);
		return Response::json(['success' => true, 'data' => $result]);
	}

	public function postEventPoster()
	{

		$validator = Validator::make(['img' => Input::file('img')], Image::acceptRules() );

		if($validator->fails())
			return Response::json(['success' => false, 'error_msg' => $validator->messages()]); 
		$result = Image::store( Input::file('img'), Auth::id() . '-' . Generator::id(), 'event_poster');
		
		unset($result['base_path']);
		return Response::json(['success' => true, 'data' => $result]);
	}
}