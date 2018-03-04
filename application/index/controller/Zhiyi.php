<?php 

namespace app\index\controller;
use think\Controller;
use think\Db;

class Zhiyi extends Controller{


	// http://localhost/lqp/public/?s=index/zhiyi/index
	// http://180.76.172.3/lqp/public/?s=index/zhiyi
	// http://www.linjuming.top/lqp/index/zhiyi
	public function index(){
		$model = new \app\index\model\Zhiyi;
		$list = $model->getRandArticles(10);
		$this->assign('list',$list);
		return $this->fetch();
	}

	// http://localhost/lqp/public/?s=index/zhiyi/test
	public function test(){
		$dl = new \DownLoader;
		$code = $dl->curlGet('https://www.zhidiy.com/gongyizhiyi/5016_18/');

	}


	public function getArticleList(){
		$model = new \app\index\model\Zhiyi;
		$list = $model->getRandArticles(50);
		return json($list,200);
	}


	// http://localhost/lqp/public/?s=index/zhiyi/collectPages
	// http://180.76.172.3/lqp//public/?s=index/zhiyi/collectPages
	public function collectPages(){
		$data = Db::query("select * from ts_zhiyi_category_collection where is_done=0 limit 1");
		if(!$data) die('all collected');

		$row = $data[0];
		for($i=1;$i<=$row['max_page_num'];$i++){
			$page_url = $row['category_url'].'page_'.$i.'.html';
			Db::table('ts_zhiyi_category_page_collection')->insert(['page_url'=>$page_url]);
			echo $page_url.'<br>';
		}
		Db::table('ts_zhiyi_category_collection')->where(['id'=>$row['id']])->update(['is_done'=>1]);
		jsJump();
	}


	// http://localhost/lqp/public/?s=index/zhiyi/collectAticles
	// http://180.76.172.3/lqp/public/?s=index/zhiyi/collectAticles
	public function collectAticles(){
		$data = Db::query("select * from ts_zhiyi_category_page_collection where is_done=0 limit 1");
		if(!$data) die('all collected');

		$row = $data[0];
		$page_url = $row['page_url'];

		$dl = new \DownLoader;
		$html = $dl->curlGet($page_url);

		$params = [
			'start' => '<div class="sgk-gerenzhuye-jc fixed">',
			'end' => '<div class="width960 classview newpager">',
			'item_start' => 'class="piclist_k',
			'item_end' => '</div>',
		];

		$items = getItemsFromHtml($html, $params);
		$save_datas = [];
		foreach($items as $k=>$v){
			$data = [];
			$data['title'] = $v['title'];
			$data['cover_img'] = str_replace('_thumb', '', $v['src']);
			$data['article_url'] = $v['href'];
			$save_datas[] = $data;
		}
		Db::table('ts_zhiyi_article')->insertAll($save_datas);
		Db::table('ts_zhiyi_category_page_collection')->where(['id'=>$row['id']])->update(['is_done'=>1]);
		jsJump();

	}


	// http://localhost/lqp/public/?s=index/zhiyi/collectAticleContents
	// http://180.76.172.3/lqp/public/?s=index/zhiyi/collectAticleContents
	public function collectAticleContents(){
		$data = Db::query("select * from ts_zhiyi_article where is_done=0 limit 1");
		if(!$data) die('all collected');

		$row = $data[0];
		$art_url = $row['article_url'];

		$dl = new \DownLoader;
		$html = $dl->curlGet($art_url);

		preg_match('@(<div class="sgk-jc-info" id="tujia">.*)<div class="zuopin_l">@isU',$html,$result);
		if($result){
			$content = preg_replace('@<script.*</script>@i', '', $result[1]);
			$content = preg_replace('@src="/@i', 'src="https://www.zhidiy.com/', $content);
			Db::table('ts_zhiyi_article')->where('id',$row['id'])->update(['is_done'=>1,'content'=>$content]);
		}
		jsJump();

	}


	// http://localhost/lqp/public/?s=index/zhiyi/collectAticleFixContents
	// http://180.76.172.3/lqp/public/?s=index/zhiyi/collectAticleFixContents
	public function collectAticleFixContents(){
		$data = Db::query("select * from ts_zhiyi_article where is_done=2 limit 1");
		if(!$data) die('all collected');

		$row = $data[0];
		$art_url = $row['article_url'];
		$content = $row['content'];

		// 有分页
		$save['is_done']=3;
		if(strpos($content, '<div class="classview newpager newpager_neiye">') !== false){
			$content.= '<!-- sub pages bellow -->';
			for($i=1; $i<20; $i++){
				$sub_cont = $this->_getAtricleSubPageContent($art_url, $i);
				if(!$sub_cont) break;

				$content.=$sub_cont.'<!-- sub page '.$i.' end -->';
			}
			$save['content'] = $content;
		}
		Db::table('ts_zhiyi_article')->where('id',$row['id'])->update($save);
		jsJump();

	}


	private function _getAtricleSubPageContent($root_url, $page_n){
		preg_match('@^(.*\d)/?$@', $root_url, $result);
		if(!$result) return '';

		$page_url = $result[1].'_'.$page_n;  // https://www.zhidiy.com/gongyizhiyi/5016_1
		$dl = new \DownLoader;
		$html = $dl->curlGet($page_url);

		if($html==404) return '';

		preg_match('@(<div class="sgk-jc-info" id="tujia">.*)<div class="zuopin_l">@isU',$html,$result);
		if($result){
			$content = preg_replace('@<script.*</script>@i', '', $result[1]);
			$content = preg_replace('@src="/@i', 'src="https://www.zhidiy.com/', $content);
			return $content;
		}
		return '';

	}


	// http://localhost/lqp/public/?s=index/zhiyi/collectArticleFixCover
	// http://180.76.172.3/lqp/public/?s=index/zhiyi/collectArticleFixCover
	public function collectArticleFixCover(){
		// https://www.zhidiy.com/uploadfile/200812/5/1018599097.jpg
		// https://www.zhidiy.com/uploadfile/article/xiaotupian/300_1018599097_224.jpg
		$data = Db::query("select * from ts_zhiyi_article where is_done=1 limit 1");
		if(!$data) die('all collected');

		$row = $data[0];
		$content = $row['content'];

		$save = [];

		preg_match('@<img[^>]+src="([^"]+)"@isU',$content, $result);
		$save['is_done'] = 2;
		if($result){
			$save['cover_img'] = $result[1];
		}
		Db::table('ts_zhiyi_article')->where('id',$row['id'])->update($save);

		jsJump();

		
	}

// <div class="nav">
// <a class="prevpage prevdisabled">上一页</a> 
// <a class="current">1</a>
// <a href="https://www.zhidiy.com/gongyizhiyi/5016_1/">2</a>
// <a href="https://www.zhidiy.com/gongyizhiyi/5016_2/">3</a>
// <a href="https://www.zhidiy.com/gongyizhiyi/5016_3/">4</a>
// <a href="https://www.zhidiy.com/gongyizhiyi/5016_4/">5</a>
// <a href="https://www.zhidiy.com/gongyizhiyi/5016_5/">6</a>
// <a class="nextpage" href="https://www.zhidiy.com/gongyizhiyi/5016_1/" title="下一页">下一页</a> </div>
// </div>

}