<?php

/**
 * Проверяющий скрипт для загрузки сайта
 * Если папка install присутсвует в движке, значит еще не завершена установка
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 *  
 */

if (file_exists("install/")) die("Complite <a href='install/'>installation</a> first. After that delete install folder!<br>Сперва <a href='install/'>выполните</a> установку движка. После этого удалите папку install");

?>