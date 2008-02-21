<?php

/**
 * @package NanoGrabbr <http://nanograbbr.com>
 * Скрипт, вызов которого нужно установить в cron, для обновления RSS лент.
 * ВАЖНО! Если вы решили использовать крон, то необходимо в файле cfg/config.php закомментировать строку номер 17:
 * 	$cfg['without_cron'] = true;
 * 
 * Прочитать о cron'е можно тут: 
 * http://masterhost.ru/support/doc/cron/
 * http://www.host.ru/support/hosting-new/cron.html
 * http://www.sitepoint.com/article/introducing-cron
 * 
 * О наличии на вашем хостинге cron'а можно уточнить на сайте вашего хостинг-провайдера или в службе техподдержки.
 * 
 * Рекомендованная строка для запуска: */
// */5 * * * * cd /path/to/site; /path/to/php cron/rssupdater.php

define('IS_CRON', true);
include("lib/nanoconfig.class.php");
$site_path = '';
$conf = new NanoConfig($site_path);
$db = new NanoSQL($conf);

$rss = new nanoRSS($conf);
$rss->updater();

?>