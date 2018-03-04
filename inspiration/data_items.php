<?php 


// 图片 
$z_width = 302;
$quality = '85%';


$dirs = glob('data/arting365/*', GLOB_ONLYDIR);
$k = rand(1,count($dirs)) -1;
$dir = $dirs[$k];  # /data/arting365/1

$files = glob($dir.'/*');
$o_files = array();
$z_files = array();

foreach($files as $k=>$v){
	// /data/arting365/1/img20160122101154bIY0_800x1035.jpg
	// /data/arting365/1/img20160122101154bIY0_800x1035_z300.jpg
	if(strpos($v,'_z') === false){
		$o_files[] = $v;
	}
}

foreach($o_files as $k=>$v){
	$z_file = preg_replace('@\.(jpg|jpeg|gif|png)$@i', '_z'.$z_width.'.$1', $v);
	if(!is_file($z_file)){
		$convert = 'convert';
		if($_SERVER['HTTP_HOST'] == 'localhost'){
			$convert = '/ImageMagick/convert';
		}

		$cmd = "{$convert} {$v} -resize {$z_width} {$z_file} 2>&1";
		$x = exec($cmd,$output,$y);
	}
	$z_files[] = $z_file;
}





// 文字
$dirs = glob('data/wyl/*',GLOB_ONLYDIR);
$count = count($dirs);
$p = rand(1,$count);
$json_id = rand(1,10);
$json_file = 'data/wyl/*/'.$p.'/'.$json_id.'.json';
$article = json_decode($json_decode, true);























