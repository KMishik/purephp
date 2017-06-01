<?php 
/** Fenom template 'home.tpl' compiled at 2017-06-01 13:18:37 */
return new Fenom\Render($fenom, function ($var, $tpl) {
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		Тестовые уроки на bezumkin.ru
	</title>
	
		<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	
</head>
<body>

	<?php
/* _base.tpl:15: {include '_navbar.tpl'} */
 $tpl->getStorage()->getTemplate('_navbar.tpl')->display($var); ?>

<div class="container">
	<div class="row">
		<div class="col-md-10">
			
	<div class="jumbotron">
		<?php
/* home.tpl:5: {parent} */
 ?>
				<?php
/* _base.tpl:21: {if $longtitle != ''} */
 if((isset($var["longtitle"]) ? $var["longtitle"] : null) != '') { ?>
					<h3><?php
/* _base.tpl:22: {$longtitle} */
 echo (isset($var["longtitle"]) ? $var["longtitle"] : null); ?></h3>
				<?php
/* _base.tpl:23: {elseif $pagetitle != ''} */
 } elseif((isset($var["pagetitle"]) ? $var["pagetitle"] : null) != '') { ?>
					<h3><?php
/* _base.tpl:24: {$pagetitle} */
 echo (isset($var["pagetitle"]) ? $var["pagetitle"] : null); ?></h3>
				<?php
/* _base.tpl:25: {/if} */
 } ?>
				<?php
/* _base.tpl:26: {$content} */
 echo (isset($var["content"]) ? $var["content"] : null); ?>
			
	</div>

		</div>
		<div class="col-md-2">
			
				Сайдбар
			
		</div>
	</div>
</div>
</body>
<footer>
	
		<script src="/assets/js/jquery-3.2.1.min.js"></script>
		<script src="/assets/js/bootstrap.min.js"></script>
	
</footer>
</html><?php
}, array(
	'options' => 2176,
	'provider' => false,
	'name' => 'home.tpl',
	'base_name' => 'home.tpl',
	'time' => 1496301695,
	'depends' => array (
  0 => 
  array (
    '_base.tpl' => 1496303969,
    'home.tpl' => 1496301695,
  ),
),
	'macros' => array(),

        ));
