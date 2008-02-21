<?php
/**
 * Рисование капчи (картинки для защиты от спама в комментах)
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class NanoCaptcha {
	
	var $chars = '23456789DWQJNZRS'; // допустимые на капче символы
	var $captchaLength = 5; // количество символов
	var $captchaWight = 80; // ширина картинки
	var $captchaHeight = 35; // высота картинки
	var $captchaString = ''; // текст на капче
		
	function NanoCaptcha() {
		session_start();
		$_SESSION['captcha_try'] = 0;
		$this->createString();	// формирование строки, которая будет выведена на капче
	}
	
/**
 * Вывод сформированной картинки
 *
 */
	function create() {
		header ("Content-type: image/png");
		$im = @imagecreate($this->captchaWight, $this->captchaHeight);
		$background = imagecolorallocate($im, mt_rand(10,75), mt_rand(10,75), mt_rand(10,75));
		for ($i=0; $i<strlen($this->captchaString); $i++) {
			$text_color = imagecolorallocate($im, mt_rand(75,255), mt_rand(75,255), mt_rand(75,255));
			imagestring($im, mt_rand(3, 5), mt_rand(5,10)+$i*12+mt_rand(5,10), mt_rand(0, $this->captchaHeight-20),  $this->captchaString[$i], $text_color);			
		}
		$start = mt_rand(0, 3);
		for ($i=0; $i<10; $i++) {
			$color = imagecolorallocate($im, mt_rand(1,255), mt_rand(1,255), mt_rand(1,255));			
			imageline($im, $start*$i*10, 0, $start*$i*10, $this->captchaHeight, $color);
		}
		imagepng($im);
		imagedestroy($im);		
	}
	
/**
 * Генерации строки для размещения на капче и сохранение ее в сессии
 *
 */
	function createString() {
		$len = strlen($this->chars);
		do {
			$this->captchaString .= $this->chars[mt_rand(0, $len-1)];
		} while (strlen($this->captchaString) < $this->captchaLength);
		$_SESSION['nano_captcha'] = $this->captchaString;		
	}
	
} // class
?>