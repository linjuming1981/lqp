<?php 


// 图片采集
// collect.php?p=0

set_time_limit(1800);
ini_set('memory_limit','300M');

require('classes/Collecter.php');
require('classes/History.php');
$collecter = new Collecter;
$history = new History;


$ids = $history->getHistory('page_ids_downed');
if(!$ids){
	$ids = array();
	$p = 0;
}else{
	$ids = explode(',', $ids);
	foreach($ids as $k=>$v){
		$ids[$k] = (int)$v;
	}
	$p = max($ids) + 1;
}

if(isset($_GET['p'])){
	$p = (int)$_GET['p'];
}else{
	echo '<script>location.href="collect.php?p='.$p.'"</script>';
	exit;
}

if(!in_array($p, $ids)){
	$alumbs = $collecter->getAlumbs($p);

	// echo '<pre>';
	// print_r($alumbs); exit;

	foreach($alumbs as $k=>$v){
		// echo $k.'<br>';
		if($collecter->checkHasDowned($v['alumb_id'])) continue;
		// echo $k.' '.$v['alumb_id'].' end<br>';exit;
		echo '<hr>';
		echo $v['alumb_id'].' start<br>';
		// ob_flush();
		// flush();
		$collecter->downAlumbPics($v['alumb_url']);
		echo $v['alumb_id'].' done<br>';
		// ob_flush();
		// flush();
	}

	$ids[] = $p;
	sort($ids);
	$ids = implode(',',$ids);
	$history->setHistory('page_ids_downed', $ids);
}


$p++;
echo '<script>location.href="collect.php?p='.$p.'"</script>';