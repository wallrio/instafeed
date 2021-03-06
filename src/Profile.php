<?php

Use Http as Http;
Use Cache as Cache;

namespace InstaFeed;


class Profile{

	private $account = null;
	public $error = null;
	public $cache = null;
	public $useCache = false;
	



	function __construct(){
		$this->cache = new Cache();
	}

	public function username($account){
		$this->account = $account;
		$this->cache->account = $account;
	}

	public function get($callback = null){

		if($this->account == null){			
			throw new \Exception("missing username", 1);			
			return false;
		}
		
		$this->cache->account = $this->account;

		if($this->cache->check() == true) 
			return json_decode($this->cache->load());
		
		$returnRequest = Http::request(array(
			'url' => 'https://instagram.com/'.$this->account,
			'method' => 'get'
		));
		
		$result = $this->filterSecondary($this->filterPrimary($returnRequest));

		if($callback != null){

			if(isset($GLOBALS['instaFeed'])){
				
				if($this->checkDiff($GLOBALS['instaFeed'],json_encode($result,true)) == true){
					$callback();
				}
			
			}else{
				$callback();
			}
		}

		return $result;
	}
		
	public function checkDiff($source1,$source2){
		$source1 = json_decode($source1,false);
		$source2 = json_decode($source2,false);
		unset($source1->icon);
		unset($source2->icon);

		$source1 = json_encode($source1);
		$source2 = json_encode($source2);

		if(md5($source1) != md5($source2) )
			return true;
		return false;
	}

	public function filterPrimary($content){
	
		$json = strstr($content, 'window._sharedData = ');
		$json = strstr($json, '</script>', TRUE);
		$json = rtrim($json, ';');
		$json = ltrim($json, 'window._sharedData = ');		
		

		$feed = json_decode($json,true);
		return $feed;
	}

	public function filterSecondary($feed){


		$mediasNodes = $feed['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];

		// print_r($mediasNodes);

		$medias = array();
		
		if(count($mediasNodes) > 0)
		foreach ($mediasNodes as $key => $value) {
			$medias[$key]['id'] = $value['node']['id'];
			if(count($value['node']['edge_media_to_caption']['edges'])>0)
				$medias[$key]['description'] = $value['node']['edge_media_to_caption']['edges'][0]['node']['text'];
			else
				$medias[$key]['description'] = '';

			$medias[$key]['image'] = $value['node']['display_url'];
			$medias[$key]['image_dimension'] = $value['node']['dimensions'];
			$medias[$key]['shortcode'] = $value['node']['shortcode'];
			$medias[$key]['url'] = 'https://www.instagram.com/p/'.$value['node']['shortcode'].'/';
			$medias[$key]['liked'] = $value['node']['edge_liked_by']['count'];
			$medias[$key]['preview'] = $value['node']['edge_media_preview_like']['count'];
			$medias[$key]['thumbnail'] = $value['node']['thumbnail_src'];
			
		}
		
		$id = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['id'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['id']:'';
		$name = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['full_name'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['full_name']:'';
		$username = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['username'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['username']:'';
		$private = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['private'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['private']:'';
		$description = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['biography'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['biography']:'';
		$email = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['business_email'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['business_email']:'';
		$phone = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['business_phone_number'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['business_phone_number']:'';
		$category = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['business_category_name'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['business_category_name']:'';
		$followers = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['edge_followed_by']['count'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['edge_followed_by']['count']:'';
		$following = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['edge_follow']['count'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['edge_follow']['count']:'';
		$site = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['external_url'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['external_url']:'';
		$address = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['business_address_json'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['business_address_json']:'';
		$icon = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['profile_pic_url'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['profile_pic_url']:'';
		$mediaCount = isset($feed['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media'])?$feed['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']:'';

		$data = array(
			'id' => $id,
			'name' => $name,
			'username' => $username,
			'private' => $private,
			'description' => $description,
			'email' => $email,
			'phone' => $phone,
			'category' => $category,
			'followers' => $followers,
			'following' => $following,
			'site' => $site,
			'address' => $address,
			'icon' => $icon,			
			'mediaCount' => $mediaCount,
			'medias' => $medias,
			
		);


		$data = json_encode($data);

		if($this->useCache == true){
			$this->cache->save($data);
		}

		return json_decode($data,false);
	}

}