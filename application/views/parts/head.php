<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<base href="<?= $head->base_url;?>">
	<title><?= $head->title;?></title>
	<?php 
	if(isset($head->favicons)): 
		foreach($head->favicons AS $favicon) : 
			echo '<link rel="'. $favicon->rel .'" type="'. $favicon->type .'" sizes="'. $favicon->sizes .'" href="'. $favicon->href .'">'; 
		endforeach;
		unset($head->favicons, $favicon);
	endif;
	?>
	<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700"> -->
	<?php 
	if(isset($head->stylesheets)): 
		foreach($head->stylesheets AS $href) : echo '<link rel="stylesheet" href="' . $href . '"/>'; endforeach;
		unset($head->stylesheets, $href);
	endif;
	?>
	<link rel="stylesheet" id="css-main" href="assets/css/oneui.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div id="page-container" class="sidebar-o sidebar-dark enable-page-overlay side-scroll page-header-fixed">
	<?php 
	include_once('sidebar-left.php');
	include_once('header.php');
	?>
	<main id="main-container">
		<?php if(isset($head->heading) && $head->heading !== FALSE) : $head->heading = (object)$head->heading; ?>
		<div class="bg-body-light d-print-none">
		    <div class="content content-full">
		        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
		            <h1 class="flex-sm-fill h3 my-2"><?= isset($head->heading->title) ? ucwords($head->heading->title) : NULL;?></h1>
		            <?php if(isset($head->heading->breadcrumbs) && is_array($head->heading->breadcrumbs)) : ?>
					<nav class="flex-sm-00-auto ml-sm-3" aria-label="breadcrumb">
						<ol class="breadcrumb breadcrumb-alt">
							<?php foreach($head->heading->breadcrumbs AS $link) : $link = (object)$link ?>
							<li class="breadcrumb-item">
								<?php if(isset($link->is_active) && $link->is_active !== TRUE ) : ?>
								<a class="link-fx" href="<?= isset($link->href)?$link->href :NULL;?>">
									<?= isset($link->title)?ucwords($link->title) :NULL;?>
								</a>
								<?php 
								else : 
									echo isset($link->title)?ucwords($link->title) :NULL;
								endif;
								?>
							</li>
							<?php endforeach;?>
						</ol>
					</nav>
					<?php endif;?>
		        </div>
		   </div>
		</div>
		<?php endif;?>
