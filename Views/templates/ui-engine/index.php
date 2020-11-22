<?php 
	include 'Template.php';
	$template = new Template();
	$template->assign('username','Terry');
	$template->render('myTemplate'); 
?>