<?php 
	$version = 6;

	// 随机给10张图片
	$ziped_imgs = require('zip_500_imgs.php');
	shuffle($ziped_imgs);
	$ziped_imgs = array_slice($ziped_imgs,0,10);
	foreach($ziped_imgs as $k=>$v){
		$ziped_imgs[$k] = 'data/arting365/'.$v;
	}
?>
<!DOCTYPE html>
<html lang="zh-cn" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
		<title>苹儿老师的灵感图册</title>
		<meta name="description" content="A Collection of Page Transitions with CSS Animations" />
		<meta name="keywords" content="page transition, css animation, website, effect, css3, jquery" />
		<meta name="author" content="Codrops" />
		<link rel="shortcut icon" href="../favicon.ico"> 
		<link rel="stylesheet" type="text/css" href="css/default.css?v=<?php echo $version; ?>" />
		<link rel="stylesheet" type="text/css" href="css/component.css?v=<?php echo $version; ?>" />
		<link rel="stylesheet" type="text/css" href="css/animations.css?v=<?php echo $version; ?>" />
		<script src="js/modernizr.custom.js"></script>
		<script src="js/jquery-1.7.1.min.js"></script>
	</head>
	<body>	
		<div id="pt-main" class="pt-perspective">

			<?php for($i=1;$i<=6;$i++){ 
				if($i == 1){
					$img = 'mx_cover.jpg?v='.$version;
				}else{
					$img = 'loading.gif?v='.$version;
				}

			?>

			<div class="pt-page pt-page-<?php echo $i;?>">
				<div class="ui-flex justify-center center">
					<div class="cell">
						<img class="data_img on" src="<?php echo $img ?>" />
						<div class="text off">
							<h2></h2>
							<p></p>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>

		</div>
		<script type="text/javascript">var ziped_imgs = <?php echo json_encode($ziped_imgs) ?>;</script>
		<script src="js/jquery.dlmenu.js"></script>
		<script src="js/pagetransitions.js"></script>
	</body>
</html>
