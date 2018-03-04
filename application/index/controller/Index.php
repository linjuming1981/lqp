<?php
namespace app\index\controller;
use think\Controller;
use think\Db;

class Index extends Controller{

	public function index(){
		// return '献给苹儿老师的礼物';
		$data = Db::query('select * from ts_collect_category');
		echo '<pre>';
		print_r($data);

	}

	public function sumiao(){
		// echo '<pre>';
		// print_r($_SERVER);exit;
		$model = new \app\index\model\Sumiao;
		$list = $model->getRandImgs(10);
		$this->assign('list', $list);
		return $this->fetch();
	}


	public function getSumiaoList(){
		$model = new \app\index\model\Sumiao;
		$list = $model->getRandImgs(50);
		return json($list,200);
	}


	public function sumiaoCollectTools(){
		return $this->fetch();
	}


	// 收集分页网址
	public function sumiaoCollectPage(){
		
		$dl = new \DownLoader;

		// 收集分类下分页的连接
		$data = Db::query('select * from ts_collect_category where is_done=0 order by id asc limit 1');
		if(!$data){
			die('all collected');
		}

		$cate_id = $data[0]['id'];
		$cate_url = $data[0]['category_url'];
		$page_urls = [$cate_url];

		$code = $dl->curlGet($cate_url);
		if(!$code) die('curlGet timeout: '.$cate_url);

		$code = iconv('gb2312', 'utf-8//IGNORE', $code);
		preg_match('@<div id="page">.*</div>@isU', $code, $result);
		if($result){
			preg_match_all('@href=[\'"]([^\'"]+)[\'"]@isU', $result[0], $res);
			foreach($res[1] as $k=>$v){
				$page_urls[] = $cate_url.'/'.$v;
			}
			$page_urls = array_unique($page_urls);
		}

		$items = [];
		foreach($page_urls as $k=>$v){
			$items[] = [
				'category_id' => $cate_id,
				'page_url' => $v
			];
		}
		Db::table('ts_collect_page')->insertAll($items);
		Db::table('ts_collect_category')->where('id',$cate_id)->update(['is_done'=>1]);

		echo '<script>location.reload()</script>';

	}


	public function sumiaoCollectAlbum(){
		$dl = new \DownLoader;

		// 收集分类下分页的连接
		$data = Db::query('select * from ts_collect_page where is_done=0 order by id asc limit 1');
		if(!$data){
			die('all collected');
		}

		$page_id = $data[0]['id'];
		$page_url = $data[0]['page_url'];
		$album_urls = [];

		$code = $dl->curlGet($page_url);
		if(!$code) die('curlGet timeout: '.$page_url);

		$code = iconv('gb2312', 'utf-8//IGNORE', $code);
		preg_match('@<div id="index_img">.*<div id="page">@isU', $code, $result);
		if($result){
			preg_match_all('@href=[\'"]([^\'"]+\.html)[\'"]@isU', $result[0], $res);
			foreach($res[1] as $k=>$v){
				$album_urls[] = 'http://www.sumiao.net'.$v;
			}
			$album_urls = array_unique($album_urls);
		}


		$items = [];
		foreach($album_urls as $k=>$v){
			$items[] = [
				'page_id' => $page_id,
				'album_url' => $v
			];
		}
		Db::table('ts_collect_album')->insertAll($items);
		Db::table('ts_collect_page')->where('id',$page_id)->update(['is_done'=>1]);

		echo '<script>location.reload()</script>';

	}


	public function sumiaoCollectImg(){
		$dl = new \DownLoader;

		$data = Db::query('select * from ts_collect_album where is_done=0 order by id asc limit 1');
		if(!$data){
			die('all collected');
		}

		$album_id = $data[0]['id'];
		$album_url = $data[0]['album_url'];

		$code = $dl->curlGet($album_url);
		if(!$code) die('curlGet timeout: '.$album_url);

		$code = iconv('gb2312', 'utf-8//IGNORE', $code);
		// 检查是否有分页
		preg_match('@<div id="page">.*共(\d+)页.*</div>@isU', $code, $result);
		$page_count =1;
		if($result){
			$page_count = $result[1];
		}

		for($i=1; $i<=$page_count; $i++){
			if($i > 1){
				$code = '';
			}
			$this->_sumiaoCollectImg_sub($album_id,$album_url, $i, $code);
		}

		Db::table('ts_collect_album')->where('id',$album_id)->update(['is_done'=>1]);
		// Db::execute("update ts_collect_album set is_done=1 where id={$album_id}");

		echo '<script>location.reload()</script>';

	}


	private function _sumiaoCollectImg_sub($album_id,$album_url, $i, $code=''){
		$dl = new \DownLoader;
		$sub_url = $album_url;
		if($i>1){
			$sub_url = str_replace('.html', '_'.$i.'.html', $album_url);
		}
		if(!$code){
			$code = $dl->curlGet($sub_url);
			$code = iconv('gb2312', 'utf-8//IGNORE', $code);
		}

		preg_match('@<div id="article_img">.*<div id="page">@isU', $code, $result);
		if(empty($result)) return;

		preg_match_all('@src="([^"]+\.jpg)"@isU', $result[0], $res);
		if(!empty($res[1])){
			$img_urls = [];
			foreach($res[1] as $k=>$img_url){
				$img_urls[] = $img_url;
			}
			$datas = [];
			foreach($img_urls as $k=>$v){
				$datas[] = [
					'album_id' => $album_id,
					'img_url' => $v
				];
			}

			Db::table('ts_collect_img')->insertAll($datas);
		}
	}

}
