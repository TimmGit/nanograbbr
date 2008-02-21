<?php

/**
 * Обработчик AJAX-запросов
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class Ajax {
	
	var $conf;
	var $db;
	var $content;
	var $upload_path;
	var $upload_dir = 'img/';	
	
/**
 * Конструктор класса
 *
 * @param $conf - конфиг
 * @param array $path - массив URI (строка запроса, разитая по /)
 * @return Ajax
 */
	function Ajax($conf, $path) {	
		global $db;	
		$this->db = $db;
		$this->conf = $conf;		
		$this->upload_path = $conf->sitePath.$this->upload_dir;		
		switch ($path[1]) {
			case "login":
				// Попытка залогинивания автора
				if (md5($_POST['password']) == $this->conf->config_val['password']) {
					// удачно
//					setcookie("nanoGrabbrLogin", md5('guessWord:)'.md5($_POST['password'])), time()+3600*24*30, "/", $_SERVER['HTTP_HOST'], false);					
					setcookie("nanoGrabbrLogin", md5('guessWord:)'.md5($_POST['password'])), time()+3600*24*30, "/", false, 0);					
					$this->addContent("result", "good");
					$this->addContent("url", $this->conf->config_val['site']['dir']);
					session_start();					
					$_SESSION['isLogin'] = true;
					$informer = new nanoInformer();
					$informer->getCurrentVersion();		
					if ($informer->version != $conf->config_val['version']) {
						$_SESSION['haveNewVersion'] = true;					
						if (!empty($informer->newsText)) $_SESSION['haveNewVersionMsg'] = trim($informer->newsText);
					} else {
						$_SESSION['haveNewVersion'] = false;					
					}									
				} else {
					// не удачно
					$this->addContent("result", "bad");
				}
				break;
			case 'logout':
				session_unset();
				$this->addContent("result", "good");
				$this->addContent("url", $this->conf->config_val['site']['dir']);				
				break;
			case "html":
				// получение HTML кода для отображения на странице
				if (!session_id()) session_start();
				if (!$_SESSION['isLogin']) echo "You are not author!";
				switch ($_POST['code']) {
					case "author_panel":
						// панель автора
						$tpl = new NanoTemplate("templates/".$conf->config_val['template']."/author_form.tpl");
						$tpl->show();
						break;
					case "comment_form":
						// форма для редактирования коммента
						$comment_id = intval(substr($_POST['element'], strpos($_POST['element'], '_')+1));
						$nanoComment = new NanoComments($conf);
						$comment = $nanoComment->getOne($comment_id);
						if ($comment['result']) {
							$com = $comment['comment'];
							unset($com['ip]']); unset($com[2]);
							unset($com['posted_date']); unset($com[3]);
							unset($com['email']); unset($com[6]);							
							$tpl_comment_form = new NanoTemplate('templates/'.$conf->config_val['template'].'/comment_form.tpl');
							$tpl_comment_form->setBlock('edit_comment');
							$tpl_comment_form->set('comment_id', $comment_id);
							$tpl_comment_form->set('comment', $com['comment']);
							$tpl_comment_form->set('user_name', $com['author']);
							$tpl_comment_form->set('site_dir', $conf->config_val['site']['dir']);
							echo $tpl_comment_form->create();					
						} else echo 'Wrong commentID'; // ошибка и комментарий не может быть выбран
						break;
				}							
				break;
			case "captcha":
				// Проверка капчи
				session_start();				
				if (strtoupper($_POST['captcha']) === $_SESSION['nano_captcha']) {
					$this->addContent("result", "good");
				} else {
					$_SESSION['captcha_try'] = $_SESSION['captcha_try'] + 1;
					$this->addContent("result", "bad");
					if ($_SESSION['captcha_try'] >= 3) {
						// количество попыток ввести капчу слишком большое!
						session_destroy();
						$this->addContent("msg", "Session crushed! Refrashe page.");
					} else {
						$this->addContent("msg", "null");						
					}
				}				
				break;
			case "post":
				// работа с постами
				if (!$_SESSION['isLogin']) {
					$this->addContent("result", "bad");
					$this->addContent("msg", "You are not author!");
					break;
				}
				$post = new NanoPost($this->conf);				
				switch ($path[2]) {
					case "save":
						// сохранение поста
						if ($_POST['allow_comment'] == "on") {
							$_POST['comments'] = 1;
							unset($_POST['allow_comment']);
						} else {
							$_POST['comments'] = 0;
						}
						
						$error = array();
						if ($_POST['post_type']=="image") {							
							// загружают картинку			
							$img = new ImageUploader($this->upload_path, $this->upload_dir);				
							if ($_POST['url'] && $_POST['save_url']=="on") {
								$result = $img->imageDownload();
							} elseif($_FILES['img_file']['tmp_name']) {	
								$result = $img->imageUpload();								
							} else {
								$result = true;
							}
							if ($result !== true) $error = array_merge($error, $result);
							else {
								$img->checkResize(); // ресайзим картинку (если нужно)
							}
							unset($_POST['save_url']);
						} elseif ($_POST['post_type']=="link") {
							// постинг ссылки
							if (eregi("^http:\/\/", $_POST['url'])) $error[] = '{{ $lang.site.error_wrong_url }}';
						} elseif ($_POST['post_type']=='rss') {
							// сохранение RSS фида
							$rss = new nanoRSS($this->conf);
							$result = $rss->saveRSS($_POST);
							if ($result['result'] == true) {
								$this->addContent("result", "good");
								$this->addContent("post_id", $result['feed_id']);
								$this->addContent("url", $this->conf->config_val['site']['dir']);
							} else {
								$this->addContent("result", "bad");
							}
							break;
						}
														
						if ($error) {
							// есть ошибки														
							echo 'Shit happends'; // @todo: исправить, а-то как-то не солидно :)
							break; 
						} 
						
						$result = $post->savePost($_POST);						
						if ($result['result'] == true) {
							// проверка с какой страницы пришел запрос
							if (eregi('page=[0-9]+$', $_SERVER['HTTP_REFERER'])) {
								$fromPage = substr($_SERVER['HTTP_REFERER'], (strpos($_SERVER['HTTP_REFERER'], 'page=')+5));								 
							} else {
								$fromPage = 0;
							}														
 							if ($_POST['post_type']=="image" && $fromPage) header("Location: ".$this->conf->config_val['site']['dir'].'?page='.$fromPage);
 							elseif ($_POST['post_type']=="image" && !$fromPage) header("Location: ".$this->conf->config_val['site']['dir']);
							$this->addContent("result", "good");
							$this->addContent("post_id", $result['post_id']);
							if ($fromPage) $url = $this->conf->config_val['site']['dir'].'?page='.$fromPage;
							else $url = $this->conf->config_val['site']['dir'];
							$this->addContent("url", $url);
						} else {
							$this->addContent("result", "bad");							
						}
						break;
					case "delete":
						// удаление поста и комментариев														
						if ($_POST['posttype']=='rss') {							
							// удаления целой rss ленты
							$rss = new nanoRSS($this->conf);
							$result = $rss->deleteRSS($_POST['post_id']);							
						} else {
							$result = $post->deleteOne($_POST['post_id'], $_POST['posttype']);
						}
						if ($result['result']) {
							$this->addContent("result", "good");
							$this->addContent("url", $this->conf->config_val['site']['dir']);
						} else {
							$this->addContent("result", "bad");
							$this->addContent("msg", $result['msg']);
						}
						break;
					case "get":
						// получение одного поста
						$onePost = $post->getOne($_POST['post_id']);
						if (!$onePost['result']) {
							$this->addContent("result", "bad");
							$this->addContent("msg", $onePost['msg']);							
						} else {
							$this->addContent("result", "good");
							$this->addContent("id", $onePost['id']);
							$this->addContent("post_type", $onePost['post_type']);							
							$this->addContent("comments", $onePost['comments']);							
							$this->addContent("title", $onePost['title'] ? htmlspecialchars($onePost['title']) : '0');							
							$this->addContent("text", $onePost['text'] ? htmlspecialchars($onePost['text']) : '0');							
							$this->addContent("url", $onePost['url'] ? htmlspecialchars($onePost['url']) : '0');							
							$this->addContent("feed_id", $onePost['feed_id']);							
						}
						break;
				}
				break;
			case "comment":				
				// редактирование комментария
				if (!$_SESSION['isLogin']) {
					$this->addContent("result", "bad");
					$this->addContent("msg", "You are not author!");
					break;
				}
				$comment_id = $_POST['comment_id'];
				$nanoComment = new NanoComments($conf);
				if ($_POST['delete']) {
					$res = $nanoComment->deleteOne($_POST['comment_id']);
					if ($res['result']) {
						$this->addContent("result", "good");
					} else {
						$this->addContent("result", "bad");
					}
				} else {
					$comment = $nanoComment->getOne($comment_id);				
					if (!$comment['result']) {
						$this->addContent("result", "bad");
						$this->addContent("msg", "Wrong commentID!");
					} else {
						$res = $nanoComment->saveComment(array('comment'=>$_POST['comment'], 'comment_id'=>$_POST['comment_id'], 'name'=>$_POST['name']));
						if ($res['result']) {
							// обновили коммент
							$this->addContent("result", "good");
						} else {
							$this->addContent("result", "bad");
						}
					}
				}
				break;
			case 'rssfeed':
				// получение rss feed
				$rss_id = intval($_REQUEST['feed_id']);
				$r = $this->db->select('rss_feeds', '*', array('id'=>$rss_id));
				if (@mysql_num_rows($r)) {
					$this->addContent("result", "good");
					$f = mysql_fetch_array($r);
					$this->addContent("rss_url", $f['rss_url']);
					$this->addContent("update_period", ceil($f['update_period']/60));
					$this->addContent("rss_id", $f['id']);
				} else {
					$this->addContent("result", "bad");
				}
				break;
			case "rssupdater":
				// сюда приходят запросы в случае, если не запущен cron для обновления RSS
				// Подробности смотри в файле cron/rssupdater.php
				// РЕКОМЕНДУЕТСЯ ИСПОЛЬЗОВАТЬ CRON !!!
				$rss = new nanoRSS($conf);
				$rss->updater();
				$this->addContent("result", "ok");				
				break;
		}
		$this->send();
	}
	
/**
 * Добавление информации в массив, из которо потом будет построен XML ответа
 *
 * @param string $key - будущий нод в XML
 * @param string $value - значение нода
 */
	function addContent($key, $value=null) {
		$this->content[$key] = $value;
	}
	
/**
 * Формирование из массива $this->content XML ответа и вывод его пользователю
 *
 */
	function send() {
		if (empty($this->content)) return;
		header("Content-type: text/xml; charset=utf-8");
		echo '<?xml version="1.0" encoding="UTF-8"?><xmlresponse>';		
		foreach ($this->content as $key=>$value) {
			echo '<'.$key.'>'.$value.'</'.$key.'>'."\n";
			if ($_POST['post_type']=="image" && $key=="url") header("Location: ".$value);
		}
		echo '</xmlresponse>';		
	}
	
} // class

?>