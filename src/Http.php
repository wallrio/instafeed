<?php

namespace InstaFeed;

class Http{
	
	public static function request(array $parameters){		
		return self::curl($parameters);
	}

	public static function curl(array $parameters){

		$url = $parameters['url'];
		$method = isset($parameters['method'])?$parameters['method']:'get';
		$data = isset($parameters['data'])?$parameters['data']:array();

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if(strtolower($method) == 'post'){
			$data_string = http_build_query($data);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
		}
		$result = curl_exec($ch);		
		curl_close($ch);
		return $result;
	}

}