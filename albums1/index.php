<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<title>苹儿老师的幻想画廊</title>
<style>
html, body {
	height: 100%;
}

body {
	background-color: #68217A;
	margin: 0;
	font-family: Helvetica, sans-serif;;
	overflow: hidden;
}

a {
	color: #ffffff;
}

#info {
	position: absolute;
	width: 100%;
	color: #ffffff;
	padding: 5px;
	font-family: Monospace;
	font-size: 13px;
	font-weight: bold;
	text-align: center;
	z-index: 1;
}

#menu {
	position: absolute;
	bottom: 20px;
	width: 100%;
	text-align: center;
}

.element {
	width: 120px;
	height: 160px;
	box-shadow: 0px 0px 12px rgba(0,255,255,0.5);
	border: 1px solid rgba(127,255,255,0.25);
	text-align: center;
	cursor: default;
}

.element:hover {
	box-shadow: 0px 0px 12px rgba(0,255,255,0.75);
	border: 1px solid rgba(127,255,255,0.75);
}

	.element .number {
		position: absolute;
		top: 20px;
		right: 20px;
		font-size: 12px;
		color: rgba(127,255,255,0.75);
	}

	.element .symbol {
		position: absolute;
		top: 40px;
		left: 0px;
		right: 0px;
		font-size: 60px;
		font-weight: bold;
		color: rgba(255,255,255,0.75);
		text-shadow: 0 0 10px rgba(0,255,255,0.95);
	}

	.element .details {
		position: absolute;
		left: 0px;
		right: 0px;
		font-size: 12px;
		color: rgba(127,255,255,0.75);
	}

.element .details img{
	width:100%;
	max-height:160px;
}

button {
	color: rgba(127,255,255,0.75);
	background: transparent;
	outline: 1px solid rgba(127,255,255,0.75);
	border: 0px;
	padding: 5px 10px;
	cursor: pointer;
}
button:hover {
	background-color: rgba(0,255,255,0.5);
}
button:active {
	color: #000000;
	background-color: rgba(0,255,255,0.75);
}

.audiojs{
	display: none;
}

.wx_cover{display:none;}

</style>
<div id='wx_pic' style='margin:0 auto;display:none;'>
	<img src='mx_cover.jpg' />
</div>
</head>

<body>
<script src="js/three.min.js"></script>
<script src="js/tween.min.js"></script>
<script src="js/TrackballControls.js"></script>
<script src="js/CSS3DRenderer.js"></script>
<div id="container"></div>
<div id="menu">
<button id="table">TABLE</button>
<button id="sphere">SPHERE</button>
<button id="helix">HELIX</button>
<button id="grid">GRID</button>
</div>

<script>



var table = [
	"H", "Hydrogen", "1.00794", 1, 1,
	"He", "Helium", "4.002602", 18, 1,
];

<?php 
	$imgs = glob('images/*.*');
	// echo '<pre>';
	// print_r($imgs);exit;
	$table = array();
	$x = 1;
	$y = 1;
	foreach($imgs as $k=>$v){
		$table[] = '';
		$table[] = basename($v);
		$table[] = '1';
		$table[] = $x;
		$table[] = $y;

		$x += 1;
		if($x>=8){
			$y += 1;
			$x = 1;
		}
	}
	$json = json_encode($table,true);
	
?>
var table = <?php echo $json; ?>;



var camera, scene, renderer;
var controls;

var objects = [];
var targets = { table: [], sphere: [], helix: [], grid: [] };



if(checkLogin('请老师输入密码（姓名拼音）：')){
	init();
	animate();
}


// 登陆验证
function checkLogin(msg){
	var ls = localStorage;
	var is_login = ls.getItem('is_login');
	if(!is_login){
		var password_ck = 'liqingping';
		var password_in = prompt(msg,'');

		// 点取消时返回null
		if (!password_in && typeof password_in != "undefined" && password_in != 0){
			return false;
		}

		if(password_in == password_ck){
			ls.setItem(is_login,1);
			return true;
		}else{
			return checkLogin('密码错误，你是我苹儿老师么(ｰｰ;)');
		}
	}else{
		return true;
	}
}



function init() {

	camera = new THREE.PerspectiveCamera( 40, window.innerWidth / window.innerHeight, 1, 10000 );
	camera.position.z = 3000;

	scene = new THREE.Scene();

	// table

	for ( var i = 0; i < table.length; i += 5 ) {

		var element = document.createElement( 'div' );
		element.className = 'element';
		element.style.backgroundColor = 'rgba(0,127,127,' + ( Math.random() * 0.5 + 0.25 ) + ')';

		var number = document.createElement( 'div' );
		number.className = 'number';
		// number.textContent = (i/5) + 1;
		element.appendChild( number );

		var symbol = document.createElement( 'div' );
		symbol.className = 'symbol';
		// symbol.textContent = table[ i ];
		element.appendChild( symbol );

		var details = document.createElement( 'div' );
		details.className = 'details';
		// details.innerHTML = table[ i + 1 ] + '<br>' + table[ i + 2 ];
		details.innerHTML = '<img src="images/'+ table[i+1] +'">';
		element.appendChild( details );

		var object = new THREE.CSS3DObject( element );
		object.position.x = Math.random() * 4000/4 - 2000/4;
		object.position.y = Math.random() * 4000/4 - 2000/4;
		object.position.z = Math.random() * 4000/4 - 2000/4;
		scene.add( object );

		objects.push( object );

		//

		var object = new THREE.Object3D();
		// object.position.x = ( table[ i + 3 ] * 140 ) - 1330/4;
		object.position.x = ( table[ i + 3 ] * 140 ) -450;
		// object.position.y = - ( table[ i + 4 ] * 180 ) + 990/4;
		object.position.y = - ( table[ i + 4 ] * 180 ) + 600;

		targets.table.push( object );

	}

	// sphere

	var vector = new THREE.Vector3();

	for ( var i = 0, l = objects.length; i < l; i ++ ) {

		var phi = Math.acos( -1 + ( 2 * i ) / l );
		var theta = Math.sqrt( l * Math.PI ) * phi;

		var object = new THREE.Object3D();

		object.position.x = 800 * Math.cos( theta ) * Math.sin( phi );
		object.position.y = 800 * Math.sin( theta ) * Math.sin( phi );
		object.position.z = 800 * Math.cos( phi );

		vector.copy( object.position ).multiplyScalar( 2 );

		object.lookAt( vector );

		targets.sphere.push( object );

	}

	// helix

	var vector = new THREE.Vector3();

	for ( var i = 0, l = objects.length; i < l; i ++ ) {

		var phi = i * 0.175 + Math.PI;

		var object = new THREE.Object3D();

		object.position.x = 900 * Math.sin( phi );
		object.position.y = - ( i * 8 ) + 450;
		object.position.z = 900 * Math.cos( phi );

		vector.x = object.position.x * 2;
		vector.y = object.position.y;
		vector.z = object.position.z * 2;

		object.lookAt( vector );

		targets.helix.push( object );

	}

	// grid

	for ( var i = 0; i < objects.length; i ++ ) {

		var object = new THREE.Object3D();

		object.position.x = ( ( i % 5 ) * 400 ) - 800;
		object.position.y = ( - ( Math.floor( i / 5 ) % 5 ) * 400 ) + 800;
		object.position.z = ( Math.floor( i / 25 ) ) * 1000 - 2000;

		targets.grid.push( object );

	}

	//

	renderer = new THREE.CSS3DRenderer();
	renderer.setSize( window.innerWidth, window.innerHeight );
	renderer.domElement.style.position = 'absolute';
	document.getElementById( 'container' ).appendChild( renderer.domElement );

	//

	controls = new THREE.TrackballControls( camera, renderer.domElement );
	controls.rotateSpeed = 0.5;
	controls.minDistance = 500;
	controls.maxDistance = 6000;
	controls.addEventListener( 'change', render );

	var button = document.getElementById( 'table' );
	button.addEventListener( 'click', function ( event ) {

		transform( targets.table, 2000 );

	}, false );

	var button = document.getElementById( 'sphere' );
	button.addEventListener( 'click', function ( event ) {

		transform( targets.sphere, 2000 );

	}, false );

	var button = document.getElementById( 'helix' );
	button.addEventListener( 'click', function ( event ) {

		transform( targets.helix, 2000 );

	}, false );

	var button = document.getElementById( 'grid' );
	button.addEventListener( 'click', function ( event ) {

		transform( targets.grid, 2000 );

	}, false );

	transform( targets.table, 5000 );

	//

	window.addEventListener( 'resize', onWindowResize, false );

}

function transform( targets, duration ) {

	TWEEN.removeAll();

	for ( var i = 0; i < objects.length; i ++ ) {

		var object = objects[ i ];
		var target = targets[ i ];

		new TWEEN.Tween( object.position )
			.to( { x: target.position.x, y: target.position.y, z: target.position.z }, Math.random() * duration + duration )
			.easing( TWEEN.Easing.Exponential.InOut )
			.start();

		new TWEEN.Tween( object.rotation )
			.to( { x: target.rotation.x, y: target.rotation.y, z: target.rotation.z }, Math.random() * duration + duration )
			.easing( TWEEN.Easing.Exponential.InOut )
			.start();

	}

	new TWEEN.Tween( this )
		.to( {}, duration * 2 )
		.onUpdate( render )
		.start();

}

function onWindowResize() {

	camera.aspect = window.innerWidth / window.innerHeight;
	camera.updateProjectionMatrix();

	renderer.setSize( window.innerWidth, window.innerHeight );

	render();

}

function animate() {

	requestAnimationFrame( animate );

	TWEEN.update();

	controls.update();

}

function render() {

	renderer.render( scene, camera );

}



</script>
<audio src="congcongnanian.mp3" preload="auto" autoplay="auto" loop="loop" />
</body>
</html>
