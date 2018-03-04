<?php 

class History{

	public function setHistory($key, $val){
		$his_json_file = './history.json';
		if(!is_file($his_json_file)){
			file_put_contents($his_json_file,'{}');
		}
		$his = json_decode(file_get_contents($his_json_file),true);
		$his[$key] = $val;
		$json = json_encode($his);
		file_put_contents($his_json_file, $json);
		return true;
	}

	public function getHistory($key){
		$his_json_file = './history.json';
		if(!is_file($his_json_file)){
			file_put_contents($his_json_file,'{}');
		}
		$his = json_decode(file_get_contents($his_json_file),true);
		if(isset($his[$key])){
			return $his[$key];
		}else{
			return null;
		}
	}
	
}