<?php
/**
 * 
 * Класс для проверки наличия обновлений движка
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class nanoInformer {

	var $nanograbrURL = 'http://nanograbbr.com/checkupdate'; // по этому URL можно получить информацию о свежей версии
	var $version; // номер версии
	var $newsText; // текст новости, если она будет необходима	
	
/**
 * Получение информации о текущей версии движка на официальном сайте
 *
 */
	function getCurrentVersion() {
		$info = @file($this->nanograbrURL);
		$this->version = trim($info[0]);
		if (count($info) > 1) {
			unset($info[0]);
			$this->newsText = implode('', $info);
		}
	}
	
} //class

?>