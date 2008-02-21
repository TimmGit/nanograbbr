<?php

/**
 * @package NanoGrabbr 
 * @link http://nanograbbr.com
 * @example http://demo.nanograbbr.com
 * @author Aist <aist@nanograbbr.org>
 *
 */

error_reporting(0); // отключение вывода ошибок

$site_path = str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']);
include("lib/nanostarter.php");
include("lib/nanoconfig.class.php");
$conf = new NanoConfig($site_path);
$db = new NanoSQL($conf);
$post = new NanoPost(&$conf);
$tpl = new NanoTemplate("templates/".$conf->config_val['template']."/index.tpl");
$site = $conf->get('site');
if (strlen($conf->config_val['site']['dir']) > 1) $_SERVER['REDIRECT_URL'] = '/'.str_replace($conf->config_val['site']['dir'], '', $_SERVER['REDIRECT_URL']);
$path = explode("/", substr($_SERVER['REDIRECT_URL'], 1));
if (strlen($_COOKIE['nanoGrabbrLogin']) == 32) {
	session_start();
	session_regenerate_id(true);
	@ini_set("session.gc_maxlifetime", "2000000");
}

$isOnePost = false;
$section = '';
switch ($path[0]) {	
	case 'ajax':	
		// AJAX запросы на всякое разное		
		include_once('lib/nanoajax.php');		
		$ajax = new Ajax($conf, $path);
		exit;
		break;
	case 'captcha':
		// Запрос картинки (CAPTCHA)
		include_once('lib/nanocaptcha.class.php');
		$captcha = new NanoCaptcha();
		$captcha->create();
		exit;
		break;
	case 'images':
		$post = new NanoPost($conf);
		$section = 'images';
		if (empty($path[1])) $posts = $post->getList($_GET['page'], 'image');
		else {
			$posts = $post->getOne($path[1], 'image');			
			$isOnePost = true;
		}
		break;
	case 'texts':
		$post = new NanoPost($conf);
		$section = 'texts';
		if (empty($path[1])) $posts = $post->getList($_GET['page'], 'text');
		else {
			$posts = $post->getOne($path[1], 'text');			
			$isOnePost = true;
		}		
		break;
	case 'quotes':
		$post = new NanoPost($conf);
		$section = 'quotes';
		if (empty($path[1])) $posts = $post->getList($_GET['page'], 'quote');
		else {
			$posts = $post->getOne($path[1], 'quote');			
			$isOnePost = true;
		}		
		break;
	case 'videos':
		$post = new NanoPost($conf);
		$section = 'videos';
		if (empty($path[1])) $posts = $post->getList($_GET['page'], 'video');
		else {
			$posts = $post->getOne($path[1], 'video');			
			$isOnePost = true;
		}		
		break;
	case 'links':
		$post = new NanoPost($conf);
		$section = 'links';
		if (empty($path[1])) $posts = $post->getList($_GET['page'], 'link');
		else {
			$posts = $post->getOne($path[1], 'link');			
			$isOnePost = true;
		}		
		break;
	case 'feeds':
		$post = new NanoPost($conf);
		if (empty($path[1])) $posts = $post->getList($_GET['page'], 'feed');
		else {
			$posts = $post->getOne($path[1], 'feed');			
			$isOnePost = true;
		}		
		break;
	case "rss":
		// Генерация RSS контента			
		$rss = new nanoRSS($conf);		
		switch ($path[1]) {
			case "images":
				$rss->createRSS('image');
				break;
			case "texts":
				$rss->createRSS('text');
				break;
			case "quotes":
				$rss->createRSS('quote');
				break;
			case "videos":
				$rss->createRSS('video');
				break;
			case "links":
				$rss->createRSS('link');
				break;
			default:
				$rss->createRSS();				
				break;
		}
		exit();
		break;
	default:
		// первая или ошибочная страница
		$post = new NanoPost($conf);
		$posts = $post->getList($_GET['page']);		
		break; 
}

$site['top_title'] = $site['title'];
// Вывод результата
if ($posts['result'] == true && empty($posts['msg'])) {
	if ($isOnePost) {
		// один пост			
		$site['top_title'] = $site['title'].$site['title_separator'].$posts['title'];
		$posts['text'] = nl2br($posts['text']);	
		if ($path[2] === 'comment') {
			// Сохранение комментария
			session_start();
			$commentData = array(); // в случае ошибки сюда запомним форму комментария
			if (strtoupper($_POST['captcha']) === $_SESSION['nano_captcha']) {
				// капча введена правильно, можно сохранять пост
				setcookie("nano_user_name", addslashes($_POST['name']), time()+3600*24*60, "/", false, 0);									
				setcookie("nano_user_email", addslashes($_POST['email']), time()+3600*24*60, "/", false, 0);
				$tmpPost = $post->getOne($_POST['post_id']);
				if (!$tmpPost['comments']) {
					// Этот пост нельзя комментировать!
					$commentData = $_POST;
				}
				$comment = new NanoComments($conf);
				$result = $comment->saveComment($_POST);
				if ($result['result'] == true) {					
					// посылаем уведомление (если нужно)
					if ($conf->config_val['notification']['active']) {
						// посылаем уведомление на @-почту автору				
						$notification = new NanoNotification($conf);
						$url = 'http://'.$_SERVER['SERVER_NAME'].$conf->config_val['site']['dir'].$path[0].'/'.$path[1];
						$notification->sendNotification($_POST['name'].' ['.$_POST['email'].']', $_POST['comment'], $_SERVER['REMOTE_ADDR'], $url, $tmpPost['title'], ($tmpPost['comments_count']+1));
					}
					header("Location: ".$conf->config_val['site']['dir'].$path[0].'/'.$path[1].'/#comment'.$result['comment_id']);
				}
			} else {
				// нифига не верная капча!
				$commentData = $_POST;
			}
		}
		$tpl_post = new NanoTemplate('templates/'.$conf->config_val['template'].'/'.$posts['post_type'].'_post.tpl');
		$tpl->set('show_comments_form', 'none');
		if ($posts['comments']) {
			$tpl->setBlock('can_have_comments');
			if ($posts['comments_count'] == 0) $tpl->setBlock('no_comments');
			else {
				// есть комментарии
				$comment = new NanoComments($conf);
				$comments = $comment->getList($path[1]);
				$tpl->set('comments', $comments['comments']);
			}
			$tpl_comment_form = new NanoTemplate('templates/'.$conf->config_val['template'].'/comment_form.tpl');
			$tpl_comment_form->setBlock('new_comment');
			$tpl_comment_form->set('post_id', $path[1]);
			$tpl_comment_form->set('user_name', $_COOKIE['nano_user_name']);
			$tpl_comment_form->set('user_email', $_COOKIE['nano_user_email']);
			$tpl_comment_form->set('site_dir', $site['dir']);
			$tpl_comment_form->set('post_type', $path[0]);
			if ($commentData) {
				$tpl_comment_form->set('comment', stripslashes($commentData['comment']));
				$tpl->set('show_comments_form', 'block');
			}
			$tpl->set('comment_form', $tpl_comment_form->create());
		}		
		switch($posts['post_type']) {
			case 'image':												
				$tpl_post->set('alt', stripslashes($posts['title']));
				$tpl_post->set('text', stripslashes($posts['text']));
				if (strpos($posts['url'], 'http:')===false) {															
					list($width, $height, $tmp, $size) = @getimagesize($site_path.$posts['url']);							
					$tpl_post->set('size', $size);
					$posts['url'] = $site['dir'].$posts['url'];
					$big_img = substr(substr_replace($posts['url'], '_original'.substr($posts['url'], strrpos($posts['url'],'.')), strrpos($posts['url'],'.')), 1);					
					if ($site['dir'] != '/') $big_img = str_replace($site['dir'], '', '/'.$big_img);
					if (file_exists($big_img)) {
						// есть большая картинка!
						$tpl_post->setBlock('original');
						$tpl_post->set('big_url', $big_img);
					} else {
						$tpl_post->setBlock('small');
					}
				}						
				$tpl_post->set('url', $posts['url']);				
				$posts['body'] = $tpl_post->create();						
				break;
			case 'quote':
				if ($posts['url']) {
					$tpl_post->setBlock('have_url');
					$tpl_post->set('url', $posts['url']);
				}						
				$tpl_post->set('text', $posts['text']);
				$posts['body'] = $tpl_post->create();							
				break;	
			case 'link':
				$tpl_post->set('url', $posts['url']);
				$tpl_post->set('text', $posts['text']);
				$tpl_post->set('title', stripslashes($posts['title']));
				$posts['body'] = $tpl_post->create();							
				break;	
			case 'feed':
				$tpl_post->set('url', $posts['url']);
				$tpl_post->set('text', $posts['text']);
				$tpl_post->set('title', stripslashes($posts['title']));
				$posts['body'] = $tpl_post->create();							
				break;					
			default:
				$tpl_post->set('text', $posts['text']);
				$posts['body'] = $tpl_post->create();
				break;
		}
		$tpl->set("one_post", $posts);		
		$tpl->setBlock("one_post");		
	} else {
		// лента постов		
		$site['top_title'] = stripslashes($site['title']);
		for ($i=0; $i<count($posts['posts']); $i++) {	
			$tpl_post = new NanoTemplate('templates/'.$conf->config_val['template'].'/'.$posts['posts'][$i]['post_type_name'].'_post.tpl');
			switch($posts['posts'][$i]['post_type_name']) {
				case 'image':												
					$tpl_post->set('alt', stripslashes($posts['posts'][$i]['title']));
					$tpl_post->set('text', stripslashes($posts['posts'][$i]['text']));
					if (strpos($posts['posts'][$i]['url'], 'http:')===false) {
						list($width, $height, $tmp, $size) = @getimagesize($site_path.$posts['posts'][$i]['url']);							
						$tpl_post->set('size', $size);
						$posts['posts'][$i]['url'] = $site['dir'].$posts['posts'][$i]['url'];
						$big_img = substr(substr_replace($posts['posts'][$i]['url'], '_original'.substr($posts['posts'][$i]['url'], strrpos($posts['posts'][$i]['url'],'.')), strrpos($posts['posts'][$i]['url'],'.')), 1);
						if ($site['dir'] != '/') $big_img = str_replace($site['dir'], '', '/'.$big_img);
						if (file_exists($big_img)) {
							// есть большая картинка!
							$tpl_post->setBlock('original');
							$tpl_post->set('big_url', $big_img);
						} else {
							$tpl_post->setBlock('small');
						}
					} else {
						// картинка с внешнего сервера
						$tpl_post->setBlock('small');
					}
					$tpl_post->set('url', $posts['posts'][$i]['url']);
					$posts['posts'][$i]['body'] = $tpl_post->create();						
					break;
				case 'quote':
					if ($posts['posts'][$i]['url']) {
						$tpl_post->setBlock('have_url');
						$tpl_post->set('url', $posts['posts'][$i]['url']);
					}						
					if ($posts['posts'][$i]['text'] != $posts['posts'][$i]['title']) $tpl_post->set('text', $posts['posts'][$i]['text']);
					$posts['posts'][$i]['body'] = $tpl_post->create();							
					break;	
				case 'link':
					$tpl_post->set('url', $posts['posts'][$i]['url']);
					if ($posts['posts'][$i]['text'] != $posts['posts'][$i]['title']) $tpl_post->set('text', $posts['posts'][$i]['text']);
					$tpl_post->set('title', stripslashes($posts['posts'][$i]['title']));
					$posts['posts'][$i]['body'] = $tpl_post->create();							
					break;	
				case 'feed':
					$tpl_post->set('url', $posts['posts'][$i]['url']);
					if ($posts['posts'][$i]['text'] != $posts['posts'][$i]['title']) $tpl_post->set('text', $posts['posts'][$i]['text']);
					$tpl_post->set('title', $posts['posts'][$i]['title']);
					$posts['posts'][$i]['body'] = $tpl_post->create();							
					break;	
				default:
					if ($posts['posts'][$i]['text'] != $posts['posts'][$i]['title']) $tpl_post->set('text', $posts['posts'][$i]['text']);
					$posts['posts'][$i]['body'] = $tpl_post->create();
					break;
			}
			if ($posts['posts'][$i]['comments'] || $posts['posts'][$i]['comments_count']) {				
				$tpl_comments_line = new NanoTemplate("templates/".$conf->config_val['template']."/comments_after_post.tpl");
				if ($posts['posts'][$i]['comments_count'] == 0) $tpl_comments_line->setBlock('no_comments');
				if ($posts['posts'][$i]['comments_count'] > 0) $tpl_comments_line->setBlock('have_comments');
				if ($posts['posts'][$i]['comments_count'] == 1) $tpl_comments_line->setBlock('1comment');
				if ($posts['posts'][$i]['comments_count'] > 1 && $posts['posts'][$i]['comments_count'] < 5) $tpl_comments_line->setBlock('2comment');
				if ($posts['posts'][$i]['comments_count'] >= 5) $tpl_comments_line->setBlock('5comment');
				$tpl_comments_line->set('comments_count', $posts['posts'][$i]['comments_count']);
				$tpl_comments_line->set('site', $site);
				$tpl_comments_line->set('post_type', $posts['posts'][$i]['post_type_name']);
				$tpl_comments_line->set('post_id', $posts['posts'][$i]['id']);
				$posts['posts'][$i]['comments_link'] = $tpl_comments_line->create();				
			}
		}	
		// пагинация
		if ($posts['pages']['pages']) {			
			$paginator = '';			
			for ($p=0; $p<count($posts['pages']['pages']); $p++) {
				$t = new NanoTemplate("templates/".$conf->config_val['template']."/paginator.tpl");
				if ($posts['pages']['pages'][$p]['active']) $t->setBlock('active_page');
				else $t->setBlock('page');				
				$t->set('page', $posts['pages']['pages'][$p]['page']);
				if ($p == count($posts['pages']['pages'])-1) {
					if ($posts['pages']['prev']) {
						$tpl->setBlock('prev');
						$tpl->set('prev_page', $posts['pages']['prev']);
					}
					if ($posts['pages']['next']) {
						$tpl->setBlock('next');
						$tpl->set('next_page', $posts['pages']['next']);
					}
				}
				$paginator .= $t->create();								
			}			
			$tpl->setBlock('paginator');
			$tpl->set('paginator', $paginator);
		}
	}
	$tpl->set("posts", $posts['posts']);
}

if ($section) $tpl->set('section', $section); // выбран какой-то подраздел на сайте

if ($_SESSION['isLogin']) {
	$tpl->setBlock("can_edit");	
	$tpl->setBlock("author_form");	
	$tpl_author = new NanoTemplate('templates/'.$conf->config_val['template'].'/author_form.tpl');
	$tpl_author->set('site', $site);
	if ($_SESSION['haveNewVersion']) {						
		$tpl_author->setBlock('have_new_version');
		$tpl_author->set('new_version_text', $_SESSION['haveNewVersionMsg']);
		$_SESSION['haveNewVersion'] = false; // чтобы показывалось один раз, а не мусолило глаза
	}	
	$tpl->set('author_form', $tpl_author->create());	
	
	$tpl->setBlock('post_form');
	$tpl_form = new NanoTemplate('templates/'.$conf->config_val['template'].'/posts_forms.tpl');
	$r = $db->select('rss_feeds', '*', 1, array('rss_url'=>'ASC'));
	$tmp = array();
	while ($f = mysql_fetch_array($r)) {
		$tmp[] = $f;
	}
	$tpl_form->set('my_rss', $tmp);	
	$tpl->set('post_form', $tpl_form->create());
	$tpl->setBlock('logout');
	
} else {
	$tpl->setBlock('login_form');
	$tpl->setBlock('login');
	if ($conf->config_val['check_update']) $tpl->set('needCheck', 'yes');
	else  $tpl->set('needCheck', 'no');
}

if (isset($conf->config_val['without_cron']) && $conf->config_val['without_cron'] == true) {
	// рекомендую включить обновления RSS каналов в cron !!!
	// подробности в файле cron/rssupdater.php
	$tpl->setBlock("cron_off");
}
 
$site = array_map('stripslashes', $site);
$tpl->set('site', $site);
$tpl->show();

?>