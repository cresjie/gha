<?php


namespace App\Helpers;

class Query
{

	static public function httpQuery($model, $data = array(), $defaults = array())
	{
		$data = array_merge($defaults, $data);

		if( isset($data['order_by']) && is_array($data['order_by']) ){
			foreach ($data['order_by'] as $field => $direction) {
				$model->orderBy($field, $direction);
			}
		}
		return $model;
	}
}