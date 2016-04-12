<?php namespace App;

use Illuminate\Auth\Authenticatable;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use App\Helpers\Eloquent\Model;

use App\Helpers\Generator;



class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	

	protected $fillable = ['email', 'first_name','middle_name', 'last_name','suffix','gender','profile_img'	];

	public $incrementing = false;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token','password_reminder');


	
	// Rules ---------

	static public function createRules(){
		return [
				'email' => 'required|email|unique:user',
				'password' => 'required|min:8',
				'retype_password' => 'required|same:password',
				
				/**********
				*Custom Validator (Not numeric)
				***********/
				'slug' => 'not_numeric', 

				'first_name' => 'required|min:2',
				'last_name' => 'required|min:2',
				'gender' => 'required|in:male,female',
				//'g-recaptcha-response' => 'required|recaptcha'
			];
	}

	static public function passwordRules($additional = []){
		return array_merge([
			'current_password' => 'required|min:8',
			'new_password' => 'required|min:8',
			'retype_new_password' => 'required|same:new_password',
		], $additional);
	}

	static public function updateRules($additional = [])
	{
		return array_merge([
			'email' => 'required|email',
			'slug' => 'not_numeric', 
			'first_name' => 'required|min:2',
			'last_name' => 'required|min:2',
			'gender' => 'required|in:male,female',
		], $additional);
	}


	/**
	 * Relationships
	 */


	public function member_of()
	{
		return $this->hasOne('App\Models\GigGroupMembers', 'user_id', 'id');
	}

	public function joined_groups()
	{
		return $this->hasMany('App\Models\GigGroupMembers', 'user_id', 'id');
	}

	public function contacts()
	{
		return $this->hasMany('App\Models\Contact','requestor');
	}

	public function usertype()
	{
		return $this->hasOne('App\Models\Usertype\Relationship','user_id');
	}
	public function test()
	{
		return $this->morphTo('App\Models\GigGroupMembers', 'user_id', 'id');
	}

}
