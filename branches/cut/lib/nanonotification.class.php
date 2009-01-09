<?php

/**
 * Класс для посылки уведомлений о комментариях
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class NanoNotification {
	
	var $conf;
		
	function NanoNotification($conf) {
		$this->conf = $conf;		
	}
	
/**
 * Формирование и отправка письма с уведомлением о новом комментарии
 *
 * @param string $author - имя автора комментария
 * @param string $comment - текст комментария
 * @param string $ip - IP адрес комментатора
 * @param string $url - ссылка на пост, к которому оставлен комментарий
 * @param string $title - заголовок
 * @param int $commentCounter - счетчик комментариев к посту
 * @return bool
 */
	function sendNotification($author, $comment, $ip, $url, $title, $commentCounter) {
		$tpl = new NanoTemplate('templates/system/comment_notification.tpl', $conf->config_val['language']);
		$tpl->set('author', strip_tags(stripslashes($author)));
		$tpl->set('comment', strip_tags(stripslashes($comment))); // из текста коментария удаляем все теги и лишние слеши
		$tpl->set('ip', $ip);
		$tpl->set('url', $url);
		$tpl->set('date', date('d.m.Y H:i:s'));
		$email_text = $tpl->create();
		$subj = $this->conf->config_val['site']['title'].$this->conf->config_val['site']['title_separator'].stripslashes($title).' ['.$commentCounter.']';
		$text = $tpl->create(); // формирование текста письма
		// посылаем письмо
		@mail($this->conf->config_val['notification']['email'], "=?utf-8?B?".base64_encode($subj)."?=", $text, 'From: '."=?utf-8?B?".base64_encode($this->conf->config_val['site']['title']).'?= <noreply@'.$_SERVER['HTTP_HOST'].">\r\n"."X-Mailer: nanograbbr.com\nContent-type: text/plain; charset=utf-8");
		return true;
	}	
	
} // class

?>