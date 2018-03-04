<?php 

set_time_limit(1800);
ini_set('memory_limit','300M');

require('classes/TextCollecter.php');
require('classes/History.php');
$collecter = new TextCollecter;
$history = new History;


if(empty($_GET['p'])){
	$p = $history->getHistory('text_last_down_page_num');
	if($p){
		$p++;
	}else{
		$p = 1;
	}
}else{
	$p = (int)$_GET['p'];
}

$items = $collecter->getPageItems($p);
foreach($items as $k=>$v){
	$collecter->downArticle($v['url'],$p);
}
$history->setHistory('text_last_down_page_num', $p);

echo '<script>location.href="collect_text.php?p='.($p+1).'"</script>';