<?php 

namespace app\index\model;
use think\Model;

class Sumiao extends Model{


	public function getRandImgs($limit){
		$limit = (int)$limit;
		$sql = "select * from ts_collect_img order by rand() limit {$limit}";
		$list = $this->query($sql);
		return $list;
	}

}