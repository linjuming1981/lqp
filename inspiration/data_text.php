<?php 

/*

/data/wyl


*/


// 文字
$dirs = glob('data/wyl/*',GLOB_ONLYDIR);
$count = count($dirs);
$p = rand(1,$count);

$json_files = glob('data/wyl/'.$p.'/*.json');
$articles = array();

foreach($json_files as $k=>$v){
	$code = file_get_contents($v);
	$articles[] = json_decode($code, true);
}

die(json_encode($articles));
