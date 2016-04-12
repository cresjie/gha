<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Validator;
use Carbon\Carbon;
use Exception;

class ExtendedValidatorServiceProvider extends ServiceProvider
{

	public function boot()
	{
		Validator::extend('not_numeric',function($attribute, $value, $parameters){
			return !is_numeric($value) ? true:false;
		});

		Validator::extend('minDateTo', function($attr, $value, $param, $validator){
			$date = new Carbon($value);
			try{
				$dateComparison = new Carbon( $validator->getData()[ $param[0] ] );
			}catch(Exception $e){
				return false;
			}
			
			return $date >= $dateComparison;
		});


		Validator::extend('maxDateTo', function($attr, $value, $param, $validator){
			$date = new Carbon($value);
			try{
				$dateComparison = new Carbon( $validator->getData()[ $param[0] ] );
			}catch(Exception $e){
				return false;
			}
			
			return $date <= $dateComparison;
		});

		Validator::extend('minTo',function($attr, $val, $param, $validator){
			return $val >= $validator->getData()[ $param[0] ];
		});


		/**
		 * validation replacer
		 */
		Validator::replacer('minDateTo',function($msg, $attr, $rule, $params){
			$otherField = str_replace('_',' ', $params[0]);
			return str_replace(':other', $otherField, $msg);
		});

		Validator::replacer('maxDateTo',function($msg, $attr, $rule, $params){
			$otherField = str_replace('_',' ', $params[0]);
			return str_replace(':other', $otherField, $msg);
		});

		Validator::replacer('minTo',function($msg, $attr, $rule, $params){
			$otherField = str_replace('_',' ', $params[0]);
			return str_replace(':other', $otherField, $msg);
		});

	}

	public function register()
	{
		
	}
}