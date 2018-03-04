<?php 


require_once(dirname(__FILE__).'/DownLoader.php');

class TextCollecter{

	public $sleep_sec = 0.5; // 下载图片指向间隔，防止ip被封

	public function __construct(){
		$this->dl = new DownLoader();
	}


	public function getPageItems($p){
		$items = array();

		$url = 'http://www.wyl.cc/weiwen';
		if($p > 1){
			$url .= '/page/'.$p;
		}
		$code = $this->dl->curlGet($url);
		if(!$code) return $items;


		$is_match = preg_match_all('@<article.*</article>@isU',$code, $result);
		if($is_match){
			foreach($result[0] as $k=>$v){
				preg_match('@<a href="([^"]+)"@', $v, $res);
				if($res){
					$item = array();
					$item['id'] = basename($res[1],'.html');
					$item['url'] = $res[1];
					$items[] = $item;
				}
			}
		}

		return $items;

	}


	public function downArticle($url, $p){
		usleep(1000000 * $this->sleep_sec);
		$code = $this->dl->curlGet($url);
		if(!$code) return false;

		preg_match('@<h1[^>]*>([^>]+)</h1>.*<div class="single-content">\s*<p>([^>]+)</p>@isU', $code, $result);
		// echo '<pre>';
		// print_r($result);
		// exit;

		if($result){
			$article['url'] = $url;
			$article['title'] = $result[1];
			$article['content'] = $result[2];


			$json = json_encode($article);
			$save_dir = 'data/wyl/'.$p;
			$files = glob($save_dir.'/*.json');
			$file_id = count($files) + 1;

			$save_path = $save_dir.'/'.$file_id.'.json';


			// echo '<pre>';
			// print_r($article);
			// echo $json;
			// echo $save_path;
			// exit;

			if(!is_dir($save_dir)){
				mkdir($save_dir,0777,true);
			}
			file_put_contents($save_path, $json);
			echo $url.' --> '.$save_path.'<br>';
			ob_flush();
			flush();
		}
	}



}