<?php

class MVCSmarty extends Smarty {
	function __construct()
	{
        parent::__construct();

		$this->template_dir = site_path.'templates/templates/';
		$this->compile_dir = site_path.'templates/templates_c/';
		$this->config_dir = site_path.'templates/configs/';
		$this->cache_dir = site_path.'templates/cache/';
		$this->caching = false;
		//** раскомментируйте следующую строку для отображения отладочной консоли
		//$this->debugging = true;
	}
}
?>