<?php

namespace InstaFeed;

class Cache {
	
	public $account = null;
	private $dirWork = null;
	private $dirTemp = null;
	

	function __construct(){
		$this->dirTemp = sys_get_temp_dir().DIRECTORY_SEPARATOR.'instaFeed'.DIRECTORY_SEPARATOR;		
	}

	public function save($content = ''){	
		if($this->dirWork == null) $cacheDir = $this->dirTemp;
		else $cacheDir = $this->dirWork;

		if(!file_exists($cacheDir)) mkdir($cacheDir,0777,true);
		file_put_contents($cacheDir.$this->account.'.json', $content);
	}

	public function dir($dir = ''){
		$dir = $dir.'instaFeed'.DIRECTORY_SEPARATOR;
		$dir = str_replace('//', '/', $dir);
		$this->dirWork = $dir;
	}

	public function check(){
		if($this->dirWork == null) $cacheDir = $this->dirTemp;
		else $cacheDir = $this->dirWork;	

		if(file_exists($cacheDir.$this->account.'.json'))
			return true;
		return false;
	}

	public function load(){
		if($this->dirWork == null) $cacheDir = $this->dirTemp;
		else $cacheDir = $this->dirWork;
		if(file_exists($cacheDir.$this->account.'.json'))
			return file_get_contents($cacheDir.$this->account.'.json');
		return false;
	}

	public function clear($timeLimit = null,$callback = null){

		$clean = false;

		if($this->dirWork == null) $cacheDir = $this->dirTemp;
		else $cacheDir = $this->dirWork;
		
		if($timeLimit !== null){	

			if( gettype($timeLimit) == 'string'){
				if(strpos($timeLimit, 'd') != false){
					$timeLimit = str_replace('d', '', $timeLimit);
					$timeLimit = $timeLimit * (60*60*24);
					
				}else if(strpos($timeLimit, 'h') != false){
					$timeLimit = str_replace('h', '', $timeLimit);
					$timeLimit = $timeLimit * (60*60);
					
				}else if(strpos($timeLimit, 'm') != false){
					$timeLimit = str_replace('h', '', $timeLimit);
					$timeLimit = $timeLimit * (60);
					
				}else if(strpos($timeLimit, 's') != false){
					$timeLimit = str_replace('s', '', $timeLimit);
					$timeLimit = $timeLimit ;
					
				}
			}

			if(!file_exists($cacheDir.'time.json')){
				@mkdir($cacheDir);
				file_put_contents($cacheDir.'time.json', '{"updated":'.time().'}');	
				return false;
			}else{
				$time = file_get_contents($cacheDir.'time.json');	
				$time = json_decode($time,false);	
				
				if(time() > $time->updated+($timeLimit) ){
					$clean = true;					
				}							
			}
		}else{
			$clean = true;
		}

		if(file_exists($cacheDir)){
			if($clean == true){
				 if(file_exists($cacheDir.$this->account.'.json')){

				 	$GLOBALS['instaFeed'] = file_get_contents($cacheDir.$this->account.'.json');	
				 }

				$this->rrmdir($cacheDir);
				@mkdir($cacheDir);
				file_put_contents($cacheDir.'time.json', '{"updated":'.time().'}');	
					
				if($callback != null)
				$callback();

				return true;
			}else{
				return false;
			}
		}
		return false;
	}

	public function rrmdir($dir) {
        if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
           if ($object != "." && $object != "..") {
             if (is_dir($dir."/".$object))
               $this->rrmdir($dir."/".$object);
             else
               unlink($dir."/".$object);
           }
         }
        rmdir($dir);
        }
    }
}