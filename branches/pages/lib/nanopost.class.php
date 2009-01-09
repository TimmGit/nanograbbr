<?php

/**
 * Класс для работы с постами в NanoGrabbr
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class NanoPost {
	
	var $conf;
	var $db;
	var $post_types = array('text', 'quote', 'image', 'video', 'link', 'feed');
	var $post_edit_icons = array('page_white_edit', 'script_edit', 'picture_edit', 'film_edit', 'link_edit', 'feed_edit');
	var $postPerPage = 10; // количество постов на странице
	var $pagesPerLine = 5; // количество страниц в блоке пагинатора
	
	function NanoPost($conf) {
		global $db;
		$this->conf = $conf;		
		$this->db = $db;
	}
	
/**
 * Получение списка постов
 *
 * @param int $page - номер страницы (посты в списке разюиваются на страницы)
 * @param string $type - тип запрашиваемых постов
 * @return array
 */
	function getList($page, $type = null) {		
		$post_type_id = array_flip($this->post_types);
		if (empty($type)) $r = $this->db->select('posts', '*', array('published'=>1));
		else {
			// запрошены посты определенного типа
			if (!in_array($type, $this->post_types)) return array('result'=>false, 'msg'=>'Wrong post type');		
			$r = $this->db->select('posts', '*', array('post_type'=>$post_type_id[$type], 'published'=>1));			
		}		
		if (@mysql_num_rows($r)) {
			$posts_count = mysql_num_rows($r);
			$all_pages = ceil($posts_count / $this->postPerPage);
			if ($page) {
				$page = $all_pages - $page;
			} else $page = 0;		
			if (empty($type)) $r = $this->db->select('posts', array('*', 'UNIX_TIMESTAMP(posted_date) as posted_date'), array('published'=>1), array("posted_date"=>"DESC"), ($page * $this->postPerPage).", ".$this->postPerPage);
			else {			
				$r = $this->db->select('posts', array('*', 'UNIX_TIMESTAMP(posted_date) as posted_date'), array('post_type'=>$post_type_id[$type], 'published'=>1), array("posted_date"=>"DESC"), ($page * $this->postPerPage).", ".$this->postPerPage);
			}			
			$posts = array();			
			while ($f=mysql_fetch_array($r)) {
				if (eregi('<nanocut', $f['text'])) {
					preg_match_all('/\<nanocut (text=(.*))+\>/imUs', $f['text'], $cut_text);					
                    if (!empty($cut_text[2][0])) $cut = trim(stripslashes(str_replace('"', '', $cut_text[2][0])));
					else $cut = '>>';					
                    $link = '<a href="'.$this->post_types[$f['post_type']].'s/'.$f['id'].'#nanocut">'.$cut.'</a>';
					$f['text'] = preg_replace('/\<nanocut(.*?)\>/i', $link, $f['text']);
                    $f['text'] = substr($f['text'], 0, strpos($f['text'], $link)+strlen($link));
				}				
				$f['text'] = nl2br($f['text']);
				$f['edit_ico_name'] = $this->post_edit_icons[$f['post_type']];
				$f['post_type_name'] = $this->post_types[$f['post_type']];
				$posts[] = array_map('stripslashes', $f);
			}
			return array('result'=>true, 'posts'=>$posts, 'pages'=>$this->paginator($all_pages, $page));
		} else {
			return array('result'=>true, 'msg'=>'Empty posts');
		}
	}
	
/**
 * Получение одного конкретного поста
 *
 * @param int $postID - идентификатор поста
 * @param int $postType - тип поста
 * @return array
 */
	function getOne($postID, $postType = null) {
		$postID = intval($postID);
		$r = $this->db->select('posts', array('*', 'UNIX_TIMESTAMP(posted_date) as posted_date'), array('id'=>$postID));
		if (!mysql_num_rows($r)) return array("result"=>false, "msg"=>"Wrong post ID");
		$post = mysql_fetch_array($r);
		$post = array_map('stripslashes', $post);
		$post['title'] = stripslashes($post['title']);
		$post['post_type'] = $this->post_types[$post['post_type']];
		if (!empty($postType) && $post['post_type'] != $postType) {
			// нет поста этого типа с таким ID
			$post['result'] = false;
			$post['msg'] = 'Wrong type or id';
		} else {
			$post['result'] = true;
		}
		return $post;
	}
	
/**
 * Удаление поста
 *
 * @param int $postID - идентификатор поста
 * @param int $postType - тип поста
 * @return array
 */
	function deleteOne($postID, $postType) {		
		$postID = intval($postID);
		$r = $this->db->select('posts', array('*'), array('id'=>$postID));
		if (!mysql_num_rows($r)) return array("result"=>false, "msg"=>"Wrong post ID");
		if ($postType == 'image') {
			$post = mysql_fetch_array($r);		
			@unlink($this->conf->sitePath.$post['url']);
			$big_img = substr_replace($post['url'], '_original'.substr($post['url'], strrpos($post['url'],'.')), strrpos($post['url'],'.'));
			if (file_exists($this->conf->sitePath.$big_img)) @unlink($this->conf->sitePath.$big_img);			
		}
		if ($this->db->delete('posts', array('id'=>$postID))) return array("result"=>true);		
		else return array("result"=>false);
	}
	
/**
 * Сохранение нового поста и поста, после редактирования
 *
 * @param array $post - массив с данными поста
 * @return array
 */
	function savePost($post) {
		if (!in_array($post['post_type'], $this->post_types)) return array('result'=>false, 'msg'=>'Wrong post type');		
		$post['post_type'] = array_search($post['post_type'], $this->post_types);		
		if ($post['post_id']) {
			// редактирование поста
			$id = $post['post_id'];
			unset($post['post_id']);
			$pt = $post['post_type'];			
			$result = $this->db->update('posts', $post, array("id"=>$id));			
			if ($result) return array('result'=>true, 'post_id'=>$id, 'post_type'=>$pt);
			else return array('result'=>false);
		} else {
			// сохранение нового поста
			unset($post['post_id']);
			$post['posted_date'] = date('Y-m-d H:i:s');					
			$result = $this->db->insert('posts', $post);
			return array('result'=>true, 'post_id'=>$result['id']);					
		}
	}
	
/**
 * Пагинатор (разбиение списка постов на страницы)
 *
 * @param int $pageCount - счетчик постов
 * @param int $pageNum - номер страницы
 * @return array
 */
	function paginator($pageCount = 1, $pageNum = null) {
		$pageNum++;
		// Номер блока со страницами (0 = 1-8, 1 = 9-16, 2 = 16-...)
		$pagesBlockNum = intval(($pageNum-1) / $this->pagesPerLine);
		// Первая страница в блоке
		$pageStart = $pagesBlockNum * $this->pagesPerLine + 1;
		// Последняя страница в блоке (не больше чем колличество страниц)
		$pageEnd   = $pageStart + $this->pagesPerLine - 1;		
		if($pageEnd > $pageCount) $pageEnd = $pageCount;
		$linkBefore = $pageStart > 1 ? ($pageCount - $pageStart+2) : '';
		$linkAfter = $pageEnd < $pageCount ? ($pageCount - $pageEnd) : '';
		// Страницы
		$pages = array();
		for($i = $pageStart; $i <= $pageEnd; $i++){
			if($pageNum == $i) $pages[] = array('page'=>$pageCount - $i + 1, 'active'=>true);
			else               $pages[] = array('page'=>$pageCount - $i + 1);
		}	
		return array('pages'=>$pages, 'prev'=>$linkBefore, 'next'=>$linkAfter);	
	}
	
} // class

?>