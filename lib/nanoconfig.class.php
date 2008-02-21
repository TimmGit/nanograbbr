<?php
/**
 * Config класс для работы движка
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class NanoConfig {
	
	var $sitePath; // путь до DocumentRoot сайта
	var $db; // DB
	var $config_val;
	
/**
 * Загрузка конфига и необходимых библиотек
 *
 * @param string $path - путь до диреткории с установленным движком
 * @return NanoConfig
 */
	function NanoConfig($path) {		
		include_once($path.'cfg/config.php');
		$cfg['site']['dir'] = $cfg['site']['dir'] ? $cfg['site']['dir'] : "/";
		$this->config_val = $cfg;				
		if (!defined('IS_CRON')) {
			$this->sitePath = $path;
			if (substr($this->sitePath, strlen($this->sitePath)-1, 1) != "/") $this->sitePath .= '/';
		} else $this->sitePath = $path;
		// подключение всех необходимых библиотек
		include_once($this->sitePath.'lib/nanosql.class.php');
		include_once($this->sitePath.'lib/nanopost.class.php');
		include_once($this->sitePath.'lib/nanotemplate.class.php');
		include_once($this->sitePath.'lib/imageuploader.class.php');
		include_once($this->sitePath.'lib/nanocomments.class.php');
		include_once($this->sitePath.'lib/nanorss.class.php');		
		include_once($this->sitePath.'lib/nanonotification.class.php');
		if ($this->config_val['check_update']) include_once($this->sitePath.'lib/nanoinformer.class.php'); // если нужно, то и проверку обновлений подключаем
	}		

/**
 * Чтение переменной из конфига
 *
 * @param string $varName - имя переменной
 * @return unknown
 */
	function get($varName) {
		if (@!isset($this->config_val[$varName])) return null;
		else return $this->config_val[$varName];
	}	
	
} // class

?>