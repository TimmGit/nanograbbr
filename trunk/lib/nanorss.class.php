<?php

/**
 * Класс для генерации и грабинга RSS фидов
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

include_once('simplepie.php');

class nanoRSS {		
	
	var $conf;
	var $db;
	var $rss_reader;
	var $max_items = 5; // количество читаемых из rss постов
	
	function nanoRSS($conf) {
		global $db;
		$this->db = $db;
		$this->conf = $conf;
		$this->rss_reader = new SimplePie();		
	}
	
/**
 * Сохранение фида
 *
 * @param array $data - массив с информацией о фиде
 * @return array
 */
	function saveRSS($data) {
		if (!eregi('^http(s)?:\/\/', $data['url'])) $data['url'] = 'http://'.$data['url'];
		if (empty($data['feed_id'])) {
			// сохраняем новый фид
			$feed = $this->readRSSFeed($data['url']);
			if ($feed) {			
				if (!mysql_num_rows($this->db->select('rss_feeds', '*', array('rss_url'=>$data['url'])))) {
					$result = $this->db->insert('rss_feeds', array('rss_url'=>$data['url'], 'update_period'=>(intval($data['period']) ? (intval($data['period'])*60) : 3600)));			
					$this->saveRSSItems($feed, $result['id']);				
				} else {
					return array('result'=>false, 'msg'=>'Not uniq feed!');	
				}
			}
			return array('result'=>true, 'feed_id'=>$result['id']);			
		} else {
			$data['feed_id'] = intval($data['feed_id']);
			$this->db->update('rss_feeds', array('rss_url'=>$data['url'], 'update_period'=>(intval($data['period']) ? (intval($data['period'])*60) : 3600)), array('id'=>$data['feed_id']));	
			return array('result'=>true, 'feed_id'=>$data['feed_id']);	
		}
	}
	
/**
 * Удаление фида и всех его постов
 *
 * @param int $rss_id - идентификатор фида
 * @return array
 */
	function deleteRSS($rss_id) {
		$rss_id = intval($rss_id);
		// удаляем все посты этого RSS
		$r = $this->db->select('posts', array('id'), array('feed_id'=>$rss_id));		
		while ($f = mysql_fetch_array($r)) {			
			$this->db->delete('comments', array('post_id'=>$f['id']));
			$this->db->delete('posts', array('id'=>$f['id']));
		}	
		$this->db->delete('rss_feeds', array('id'=>$rss_id));	
		return array('result'=>true);	
	}

/**
 * Чтение фида
 *
 * @param string $url - ссылка на фид
 * @return array - массив записей в фиде
 */
	function readRSSFeed($url) {
		$this->rss_reader->set_feed_url($url);
		$this->rss_reader->init();
		return $this->rss_reader->get_items();
	}
	
/**
 * Сохранение записей из фида в качестве поста
 *
 * @param array $items - посты из фида
 * @param int $feed_id - идентификатор фида
 * @return bool
 */
	function saveRSSItems($items, $feed_id) {		
		foreach($items as $item) {
			if (mysql_num_rows($this->db->select('posts', '*', array('url'=>trim($item->get_permalink()))))) continue; // проверяем, есть ли у нас такой пост				
			$this->db->insert('posts', array('posted_date'=>$item->get_date('Y-m-d H:i:s'), 'feed_id'=>$feed_id, 'title'=>strip_tags($item->get_title()), 'text'=>trim($item->get_content()), 'url'=>trim($item->get_permalink()), 'post_type'=>5));			
		}
		return true;
	}
	
/**
 * Проверка фидов на обновление
 *
 * @return bool
 */
	function updater() {
		$r = $this->db->select('rss_feeds', '*');
		if (@mysql_num_rows($r)) {			
			while ($f = mysql_fetch_array($r)) {
				unset($feed);
				if ($f['last_update'] <= (time()-$f['update_period'])) { // проверяем время последнего обновления фида
					// нужно обновить этот фид
					$feed = $this->readRSSFeed($f['rss_url']);
					if ($feed) {			
						$this->saveRSSItems($feed, $f['id']);				
					}
					$this->db->update('rss_feeds', array('last_update'=>time()), array('id'=>$f['id']));
				}
			}
		}
		return true;
	}
	
/**
 * Формирование RSS-фида для постов
 *
 * @param string $type - тип постов, для которых формируется фид
 * @param int $outputCount - количество постов, выводимых в фиде
 */
	function createRSS($type = null, $outputCount = 10) {
		 $site_url = 'http://'.$_SERVER['HTTP_HOST'].$this->conf->config_val['site']['dir'];
		 $site_title = stripslashes(htmlspecialchars($this->conf->config_val['site']['title']));
		 $rss_text ='<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xml:base="'.$site_url.'" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
 <title>'.$site_title.'</title>
 <link>'.$site_url.'</link>
 <description></description>
 <language>ru</language>
';
		 $post = new NanoPost($this->conf);
		 $posts = $post->getList(0, $type);
$item = '<item>
 <title><![CDATA[{title}]]></title>
 <link>{link}</link>
 <description><![CDATA[{description}]]></description>
 <pubDate>{pubdate}</pubDate> 
</item>';		 
		 if ($posts['result'] == true && empty($posts['msg'])) {		 	
			for ($i=0; $i<count($posts['posts']); $i++) {		
				if (empty($type)) $type_name = $posts['posts'][$i]['post_type_name'];
				else $type_name = $type;		
				$title = stripslashes($posts['posts'][$i]['title']);
				$link = $site_url.$type_name.'/'.$posts['posts'][$i]['id'];
				switch ($type_name) {
					case "image":
						$description = '<img src="http://'.$_SERVER['HTTP_HOST'].$this->conf->config_val['site']['dir'].$posts['posts'][$i]['url'].'" title="'.$title.'" alt="'.$title.'"><br>'.nl2br($posts['posts'][$i]['text']);
						break;
					case "link":
						$description = '<a href="'.$posts['posts'][$i]['url'].'" title="'.$title.'" taget="_blank">'.$title.'</a><br>'.nl2br($posts['posts'][$i]['text']);
						break;
					default:
						$description = nl2br($posts['posts'][$i]['text']);
						break;
				}				
				$description = stripslashes($description);
				$pubdate = date('r', $posts['posts'][$i]['posted_date']);
//				$tmp = $item;
				$search = array('{title}', '{link}', '{description}', '{pubdate}');
				$replace = array($title, $link, $description, $pubdate);
				$rss_text .= str_replace($search, $replace, $item);
			}
		 } else {
		 	// пустая RSS лента
		 }

$rss_text .= '</channel>
</rss>';
		 echo $rss_text; // вывод сформированного фида
	}
	
	
} // class

?>