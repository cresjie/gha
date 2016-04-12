<?php

namespace App\Helpers\Eloquent;

use App\Helpers\Generator;

use Illuminate\Database\Eloquent\Model as BaseModel;

use Exception;

class Model extends BaseModel
{	
	public $incrementing = false;

	protected $errorCount = 0;
	
	protected static function boot()
	{
		parent::boot();
		
		static::registerModelEvent('creating',function($model){

			
			$model->id = Generator::id();
			
			
		});

	}
	/**
	 * @param array $attributes
	 * GigEventController@store
	 */
	public function setAttributes($attributes)
	{
		foreach($attributes as $key => $value )
			$this->setAttribute($key, $value);

		return $this;
	}

	
	public function save(array $options = [])
	{
		$result = null;
		try
		{
			$result =  parent::save($options);	
		}
		catch(\Illuminate\Database\QueryException $e)
		{
			$this->errorCount++;
			
			if( $this->errorCount > 5 )
				throw $e;
			

			switch( $e->getCode() )
			{
				case 23000: 
					//Duplicate Primary key
					//resave model and generate Id;
					$this->save($options);
					break;
				default:
					throw $e;
			}
		}

		return $result;
		
	}


}