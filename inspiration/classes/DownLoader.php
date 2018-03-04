<?php 
class DownLoader{


	public function downToFile($url, $save_path, $force=false){
		if(!$force){
			if(is_file($save_path)) return;
		}
		$code = $this->_downCode($url);
		$save_dir = dirname($save_path);
		@mkdir($save_dir,0777,true);
		$resource = fopen($save_path, 'a');
		fwrite($resource, $code);
		fclose($resource);
	}


	public function downToDir($url, $save_dir){
		$code = $this->_downCode($url);
		$filename = pathinfo($url, PATHINFO_BASENAME);
		@mkdir($save_dir,0777,true);
		$resource = fopen($save_dir.'/'.$filename, 'a');
		fwrite($resource, $code);
		fclose($resource);
	}

	private function _downCode($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		$file = curl_exec($ch);
		curl_close($ch);
		return $file;
	}


	// 获取页面内容
	public function curlGet($url){
    $curl = curl_init (); // 启动CURL会话
    curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
    curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, 0 ); // 对认证证书来源
    curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 2 ); // 从证书中查SSL加密算法是否存在
    curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36' ); // 模拟用户使用的浏览器
    @curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, 1 ); // 使用自动跳转
    curl_setopt ( $curl, CURLOPT_AUTOREFERER, 1 ); // 自动设置Referer
    curl_setopt ( $curl, CURLOPT_HTTPGET, 1 ); // 发一个常规的Post请求
    // curl_setopt ( $curl, CURLOPT_COOKIEFILE, $GLOBALS ['cookie_file'] ); // 读取上面储存的Cookie信息
    curl_setopt ( $curl, CURLOPT_TIMEOUT, 10 ); // 设置超时限制防止死循
    curl_setopt ( $curl, CURLOPT_HEADER, 0 ); // 显示返回的Header区域内容
    curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec ( $curl ); // 执行操作
    if (curl_errno ( $curl )) {
        echo 'Errno' . curl_error ( $curl );
    }
    curl_close ( $curl ); // 关闭CURL会话
    return $tmpInfo; // 返回数据
	}


}