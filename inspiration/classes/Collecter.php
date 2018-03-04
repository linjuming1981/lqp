<?php 


/*
相册数 ：18*484 = 8712
文件夹组织
/home/www/default/lqp/inspiration/data
/arting365/1/1.jpg
/arting365/1/2.jpg

*/



require_once(dirname(__FILE__).'/DownLoader.php');

class Collecter{

	public $sleep_sec = 0.5; // 下载图片指向间隔，防止ip被封
	private $dl;


	public function __construct(){
		$this->dl = new DownLoader();
	}


	// 获得专辑数组
	public function getAlumbs($p){
		usleep(1000000 * $this->sleep_sec);
		
		$alumbs = array();
		$url = 'http://dpc.arting365.com/341/'.$p.'.html';
		$code = $this->dl->curlGet($url);
		$is_match = preg_match_all('@<ul class="mwpw">.*</ul>@isU', $code, $result);
		if($is_match){
			$is_match1 = preg_match_all('@<a[^>]+href="([^"]+)"[^>]+title="([^"]+)"[^>]*>\s*<img[^>]+src="([^"]+)"@isU', $result[0][0], $matches);
			if($is_match1){
				foreach($matches[0] as $k=>$v){
					preg_match('@<a[^>]+href="([^"]+)"[^>]+title="([^"]+)"[^>]*>\s*<img[^>]+src="([^"]+)"@isU', $v, $res);

					$basename = basename($res[1],'.html');
					$arr = explode('_', $basename);
					$alumb_id = reset($arr);

					$item = array();
					$item['alumb_id'] = $alumb_id;
					$item['alumb_url'] = $res[1];
					$item['alumb_title'] = $res[2];
					$item['alumb_cover_img'] = $res[3];
					$alumbs[] = $item;
				}
			}
		}
		return $alumbs;

	}

	// 判断一个专辑是否已经下载过
	public function checkHasDowned($alumb_id){
		if(is_dir('./data/arting365/'.$alumb_id)){
			return true;
		}
		return false;
	}


	// 下载专辑内图片
	public function downAlumbPics($alumb_url, $p=0){
		// http://opus.arting365.com/picture_coputer/2016-02-02/1454381211d284864.html
		// http://opus.arting365.com/picture_coputer/2016-02-02/1454381211d284864_1.html

		usleep(1000000 * $this->sleep_sec);

		$p0_url = preg_replace('@_\d+\.html$@','.html', $alumb_url);
		$p_url = $p0_url;
		if($p){
			$p_url = str_replace('.html', '_'.$p.'.html', $p0_url);
		}

		$code = $this->dl->curlGet($p_url);
		if(!$code) return;
		if(strpos($code, 'class="banner_tu"') !== false) return; // 页面不存在，被广告图换了

		preg_match('@<div class="tu">.*<img[^>]+src="([^"]+)".*</div>@isU', $code, $result);
		if($result){
			$img = $result[1];  // http://img.365imgs.cn/opus/picture_coputer/h127/h94/img20160122101154bIY0_800x1035.jpg
			// $alumb_id = preg_replace('@^.*/([^/\._])(\.|_)[^/]*$@', '$1', $alumb_url);
			$alumb_id = basename($p0_url,'.html');
			$dir = './data/arting365/'.$alumb_id;
			$save_path = $dir.'/'.basename($img);

			$dl = $this->dl;
			$dl->downToFile($img, $save_path, false);

			echo $img.'<br>';
			// ob_flush();
			// flush();

		}

		$this->downAlumbPics($alumb_url, $p+1);

	}



}




