<?php
	include("includes/startup.php");

	# Загружаем router
	$router = new Router($Registry);

	$r = Registry::getInstance();
	$r->set('router', $router);	

	$router->setPath (site_path . 'controllers');

	$router->delegate();

	exit;
?>
