<?php

/**
 * Класс для работы с комментариями 
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 *
 */

class NanoComments {
	
	var $conf;
	var $db;
		
	function NanoComments($conf) {
		global $db;
		$this->conf = $conf;		
		$this->db = $db;
	}
	
/**
 * Сохранение нового комментария и комментария после редактирования
 *
 * @param array $comment - массив с данными для комментария
 * @return array
 */
	function saveComment($comment) {		
		unset($comment['captcha']);
		$comment['author'] = strip_tags($comment['name']);
		$comment['comment'] = strip_tags($comment['comment'], '<a><strong><em><blockquote>');
		$comment['comment'] = str_replace('style', 'stylе', $comment['comment']); // простейщая защита от CSS, запрет атрибута style (англ. e заменяется на русскую е)
		unset($comment['name']);
		if ($comment['comment_id']) {
			// редактирование комментария
			$id = $comment['comment_id'];
			unset($comment['comment_id']);	
			$result = $this->db->update('comments', $comment, array("id"=>$id));			
			if ($result) return array('result'=>true, 'comment_id'=>$id);
			else return array('result'=>false);
		} else {
			// сохранение нового коммента
			unset($comment['comment_id']);
			if ($_SESSION['isLogin']) $comment['is_author'] = 1;
			else $comment['is_author'] = 0;
			$comment['ip'] = getenv("REMOTE_ADDR");
			$comment['posted_date'] = date('Y-m-d H:i:s');					
			$result = $this->db->insert('comments', $comment);
			// нужно увеличить счетчик комментов для поста
			$this->db->update('posts', array('comments_count' => array('comments_count', '+', 1)), array('id'=>$comment['post_id']));
			return array('result'=>true, 'comment_id'=>$result['id']);					
		}		
	}
	
/**
 * Получение списка комментариев для конкретного поста
 *
 * @param int $post_id - идентификатор поста
 * @return array
 */
	function getList($post_id) {
			$r = $this->db->select('comments', array('*', 'UNIX_TIMESTAMP(posted_date) as posted_date'), array('post_id'=>$post_id), array("id"=>"ASC"));			
			$comments = array();			
			while ($f=mysql_fetch_array($r)) {
				$f['comment'] = nl2br($f['comment']);
				if (empty($f['author'])) $f['author'] = 'anonymous';
				$comments[] = array_map('stripslashes', $f);
			}
			return array("result"=>true, "comments"=>$comments);
	}
	
/**
 * Получение одного комментария для последующего редактирования
 *
 * @param int $comment_id - идентификатор комментрия
 * @return array
 */
	function getOne($comment_id) {
			$comment_id = intval($comment_id);
			$r = $this->db->select('comments', array('*'), array('id'=>$comment_id));			
			$comment = array();		
			if (!mysql_num_rows($r)) array("result"=>false);
			$f=mysql_fetch_array($r);
			$com = array_map('stripslashes', $f);
			return array("result"=>true, "comment"=>$com);		
	}
	
/**
 * Удаление одного комментария
 *
 * @param int $comment_id - идентификатор комментария
 * @return array
 */
	function deleteOne($comment_id) {
		$comment_id = intval($comment_id);
		$r = $this->db->select('comments', array('*'), array('id'=>$comment_id));			
		if (!mysql_num_rows($r)) return array("result"=>false, "msg"=>"Wrong comment ID");
		$f = mysql_fetch_array($r);
		$this->db->update('posts', array('comments_count' => array('comments_count', '-', 1)), array('id'=>$f['post_id']));
		if ($this->db->delete('comments', array('id'=>$comment_id))) return array("result"=>true);				
		else return array("result"=>false);
		
	}
		
} // class

?>