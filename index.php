<?php
	include("includes/startup.php");

	# ��������� router
	$router = new Router($Registry);

	$r = Registry::getInstance();
	$r->set('router', $router);	

	$router->setPath (site_path . 'controllers');

	$router->delegate();

	exit;
?>
