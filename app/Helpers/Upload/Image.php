<?php

namespace App\Helpers\Upload;

use File;
use Image as Intervention_Image;
use Config;


class Image
{
	static public function acceptRules()
	{
		return ['img' => 'required|image|mimes:jpeg,jpg,png,gif,bmp|max:5000'];
	}

	static public function store($img, $filename,  $configName, $crop = [])
	{
		$config = Config::get( "gha.upload.image.{$configName}" );
		$filename = $filename . '.' . $img->getClientOriginalExtension();

		$image =  Intervention_Image::make( $img );

		//cropping
		$width = isset($crop['width']) ? round($crop['width']) : $image->getWidth();
		$height = isset( $crop['height']) ? round($crop['height']) : $image->getHeight();
		$x = isset($crop['x']) ? round($crop['x']) : null;
		$y = isset($crop['y']) ? round($crop['y']) : null;
		$image->crop($width, $height, $x, $y);
		$image->save( $config['base_path'] . "/o/$filename", 90);

		$recordSizes = [];
		foreach( $config['wSizes'] as $wSize )
		{
			$imageTemp = Intervention_Image::make( $image->basePath() );
			$imageTemp->resize($wSize, null, function($constraint){
				$constraint->aspectRatio();
			})->save( $config['base_path']  . "/w{$wSize}/$filename", 90);

			$recordSizes["w{$wSize}"] = $config['base_url'] . "/w{$wSize}/$filename";
		}
		return [
			'filename' => $filename,
			'link' => $config['base_url'] . "/o/$filename",
			'base_url' => $config['base_url'],
			'base_path' => $config['base_path'],
			'wSizes' => $recordSizes

		];
	}


	static public function getImage($filename, $configName, $size = null)
	{
		$config = Config::get( "gha.upload.image.{$configName}" );

		if( $size )
		{
			//checks if the size exists in the configuration
			if( in_array($size, $config['wSizes'] ) ) 
				return $config['base_url'] . "/w{$size}/{$filename}";

			return null;
		}
		
		return $config['base_url'] . "/o/{$filename}";	
		
	}

	static public function getImageLinks($filename, $configName)
	{
		$config = Config::get("gha.upload.image.{$configName}");

		foreach($config['wSizes'] as $wSize){
			$recordSizes["w{$wSize}"] = $config['base_url'] . "/w{$wSize}/$filename"; 
		}

		return [
			'link' => $config['base_url'] . "/o/$filename",
			'base_url' => $config['base_url'],
			//'base_path' => $config['base_path'],
			'sizes' => $recordSizes
		];
	}

}