<?php 
namespace app\index\model;
use think\Model;

class Zhiyi extends Model{


	function getRandArticles($limit){
		$limit = (int)$limit;
		$sql = "select * from ts_zhiyi_article where is_done=3 order by rand() limit {$limit}";
		$list = $this->query($sql);
		return $list;
	}


}