<?php
/**
 * Инсталятор системы NanoGrabbr
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

/**
 * 2DO!
 * 
 * Запретить в пароле &
 */
	include_once('../lib/nanotemplate.class.php');
	session_start();
	$tpl = new NanoTemplate('install.tpl');
	switch ($_POST['page']) {
		case "second":
			// вторая страница инсталятора
			if (!file_exists("../langs/".$_POST['lang'])) die("Language is not support");
			$_SESSION['lang'] = $_SESSION['lang'] ? $_SESSION['lang'] : $_POST['lang'];
			if (isset($_POST['user'])) {
				$tpl->set("post", $_POST);
			}
			$tpl->setBlock("second_page");			
			$checkOk = true;
			if (is_writable("../cfg/config.php")) $tpl->setBlock("config_writable");
			else {
				$tpl->setBlock("config_unwritable");
				$tpl->set("config_file_path", str_replace($_SERVER['PHP_SELF'], "/cfg/config.php", $_SERVER['SCRIPT_FILENAME']));
				$checkOk = false;				
			}
			if (is_writable("../img")) $tpl->setBlock("img_writable");
			else {
				$tpl->setBlock("img_unwritable");
				$tpl->set("img_file_path", str_replace($_SERVER['PHP_SELF'], "/img/", $_SERVER['SCRIPT_FILENAME']));
				$checkOk = false;
			}
			if (function_exists('imagecopyresized')) $tpl->setBlock("have_gd");
			else {
				$tpl->setBlock("havenot_gd");				
				$checkOk = false;
			}						
			if ($checkOk) {
				// можно просить ввести параметры доступа к БД				
				$tpl->setBlock("db_settings_form");
				if (empty($_POST['host']) && empty($_POST['prefix'])) $tpl->set('post', array('host'=>'localhost', 'prefix'=>'nano_'));				
			} else {
				// нельзя показывать форму для настроек, пока не исправит недочеты
				$tpl->setBlock("wrong_settings");
			}
			break;
		case "third":
			// третья страница
			$checkOk = true;
			$_POST = array_map('trim', $_POST);
			@mysql_select_db($_POST['name'], @mysql_connect($_POST['host'], $_POST['user'], $_POST['passwd']));
			if (mysql_error()) {
				// нифига не получилось подключиться к БД
				$checkOk = false;
				$tpl->setBlock("sql_error");
			}
			if ($_POST['password'] != $_POST['password2'] || strpos($_POST['password'], '&')!==false) {
				// пароли не совпадают
				$checkOk = false;
				$tpl->setBlock("password_error");	
				$tpl->setBlock("error");			
			}
			if (!$checkOk) {
				// нифига не получилось подключиться к БД или ещё какие-то проблемы
				$checkOk = false;
				$tpl->setBlock("sql_error");
				$tpl->setBlock("error");
				$tpl->setBlock("third_page");
				$tpl->set("post", $_POST);
			} else {
				// подключится к БД получилось, можно идти дальше				
				$configTxt = getConfigText();
				$tmp = explode("/", $_SERVER['PHP_SELF']);
				$dir = '/';
				for ($i=0; $i<count($tmp); $i++) {
					if (strpos($tmp[$i], 'install')!==false) break;
					$dir = $tmp[$i] ? '/'.$tmp[$i].'/' : '/';					
				}
				$_POST['notification'] = ($_POST['notification'] == 'on') ? 'true' : 'false'; 
				$_POST['check_update'] = ($_POST['check_update'] == 'on') ? 'true' : 'false'; 
				$a = array('_host_'=> $_POST['host'], '_user_'=>$_POST['user'], '_passwd_'=>$_POST['passwd'], 
						   '_name_'=>$_POST['name'], '_prefix_'=>$_POST['prefix'], '_dir_' => $dir, '_site_title_'=>addslashes($_POST['site_title']),
						   '_password_'=>md5($_POST['password']), '_notification_'=>$_POST['notification'], '_update_'=>$_POST['check_update'], 
						   '_email_'=>$_POST['email']);
				$configTxt = str_replace(array_keys($a), array_values($a), $configTxt);
				$fp = fopen("../cfg/config.php", "w");
				fwrite($fp, $configTxt);
				fclose($fp);
				$sql_tpl = implode("", file('./db.mysql.sql'));
				$sql = explode(";", $sql_tpl);
				$sqlDone = true;
				for ($i=0; $i<count($sql); $i++) {
					$sql[$i] = trim($sql[$i]);
					if (empty($sql[$i])) continue;
					mysql_query(str_replace('_prefix_', $_POST['prefix'], $sql[$i]))
						or mysqlError(mysql_error(), $tpl, $sqlDone);
					if (!$sqlDone) break;
				}
				if ($sqlDone) {
					$tpl->setBlock("third_page");
					$tpl->setBlock("install_completed");					
					$tpl->set("dir", $dir);					
				}
			}
			break;
		default:
			// первая страница инсталятора
			$tpl->setBlock("first_page");	
			session_destroy();		
			break;
	}
	
	$tpl->show();
	
function mysqlError($error, &$tpl, &$sqlDone) {	
	$tpl->setBlock("sql_error");
	$tpl->setBlock("error");
	$tpl->setBlock("sql_error_msg");
	$tpl->set("sql_error", $error);
	$tpl->setBlock("third_page");
	$tpl->set("post", $_POST);					
	$sqlDone = false;						
}
	
	
function getConfigText() {
	$str = <<<CFGTEXT
<?
	/**
	* Данный конфиг содержит информацию, необходимую для функционирования сайта
	* Вы можете по своему усмотрению менять настройки. Некоторые значения могут принимать только два значений: true - включено и false - выключено
	*/
	
	\$cfg['version'] = '0.7'; // установленная версия движка
	
	\$cfg['db']['host'] = '_host_'; // сервер БД
	\$cfg['db']['user'] = '_user_'; // пользователь БД
	\$cfg['db']['passwd'] = '_passwd_'; // пароль к БД
	\$cfg['db']['name'] = '_name_'; // имя БД
	\$cfg['db']['prefix'] = '_prefix_'; // префикс всех таблиц в БД
	
	\$cfg['site']['dir'] = '_dir_'; // Директория, в которой размещён сайт
	\$cfg['site']['title'] = '_site_title_'; // TITLE сайта
	\$cfg['site']['title_separator'] = ' / '; // разделитель для TITLE

	\$cfg['notification']['active'] = _notification_; // нужно ли посылать уведомления о новых комментариях
	\$cfg['notification']['email'] = '_email_'; // @-адрес для отсылки уведомлений о комментариях	

	\$cfg['template'] = 'default'; // шаблон, используемый сайтом (все шаблоны находятся в папке templates)
	
	\$cfg['password'] = '_password_'; // пароль администратора в зашифрованном виде
	
	\$cfg['without_cron'] = true; // флаг, указывающий на то, что сайт не использует cron (ЭТО ПЛОХО! CRON НУЖНО ИСПОЛЬЗОВАТЬ!). Подробности в cron/rssupdater.php
	
	\$cfg['check_update'] = _update_; // флаг, указывающий на то, данная установка будет проверять обновления движка на сайте nanograbbr.com (для отключения нужно заменить true на false)

?>	
CFGTEXT;
	return trim($str);
}
?>