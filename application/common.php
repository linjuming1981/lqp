<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件




/**
 * js跳转代码
 * @param  string $url
 */
function jsJump($url=''){
	if(!$url){
		die('<script>location.reload()</script>');
	}
	die('<script>location.href="'.$url.'"</script>');
}


/**
 * 从html中抓取有用信息数组，用于爬取列表
 * @param  string $html 源html
 * @param  array $p 提取规则 
 * @return array
 */
function getItemsFromHtml($html, $p=[]){

	$items = [];
	preg_match("@{$p['start']}.*{$p['end']}@isU", $html, $result);
	if(!$result) return [];

	preg_match_all("@{$p['item_start']}.*{$p['item_end']}@isU", $result[0], $res);
	if(empty($res[0])) return [];



	/*<div class="piclist_k ">
		<a href="https://www.zhidiy.com/quanzi/175.html" title="漂亮的圣诞节剪纸雪花装饰"><img src="https://www.zhidiy.com/uploadfile/group/1436/1436862/1436862351_thumb.jpg" width="224" height="224" alt="漂亮的圣诞节剪纸雪花装饰"/></a>
		<h2><a href="https://www.zhidiy.com/quanzi/175.html">漂亮的圣诞节剪纸雪花装饰</a></h2>
	</div>*/
	foreach($res[0] as $k=>$v){
		preg_match_all('@([a-z]+)\s?=\s?[\'"]([^\'"]+)[\'"]@', $v, $result);
		if(!empty($result[0])){
			$item = array_combine($result[1],$result[2]);
			$item['html'] = $v;
			$item['inner_text'] = strip_tags($v);
			$items[] = $item;
		}
	}

	return $items;

}