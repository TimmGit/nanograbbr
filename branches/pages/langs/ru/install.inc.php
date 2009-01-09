<?php
/**
 * Локализация сообщений для установщика
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

$lang['install']['title'] = 'Система установки движка для микроблогинга NanoGrabbr';
$lang['install']['first_page_text'] = 'Вы приступаете к установки системы для микроблогинга NanoGraddr.<br>Выберите язык';
$lang['install']['second_page_text'] = 'Проверка основных настроек сервера';
$lang['install']['check_config_writable'] = 'Есть ли возможность писать в файл конфигурации: ';
$lang['install']['config_unwritable'] = 'Необходимо установить права: ';
$lang['install']['check_img_writable'] = 'Есть ли возможность писать в папку для картинок: ';
$lang['install']['check_gdlib'] = 'Библиотека GD lib: ';
$lang['install']['next'] = 'дальше';
$lang['install']['back'] = 'вернуться';
$lang['install']['check'] = 'проверить';
$lang['install']['db_host'] = 'Сервер БД';
$lang['install']['db_user'] = 'Пользователь БД';
$lang['install']['db_passwd'] = 'Пароль к БД';
$lang['install']['db_name'] = 'Имя БД';
$lang['install']['db_prefix'] = 'Префикс для имён таблиц';
$lang['install']['password'] = 'Пароль администратора';
$lang['install']['password2'] = 'Подтверждение пароля';
$lang['install']['site_title'] = 'Имя сайта';
$lang['install']['all_fields'] = 'Все поля обязательны для заполнения!';
$lang['install']['check_updates'] = 'Включить функцию проверки выхода новых версий движка (рекомендуется)';
$lang['install']['email4notification'] = '@-адрес для получения уведомлений';
$lang['install']['notification'] = 'Посылать ли вам уведомления по почте о новых комментариях';


$lang['install']['wrong_filesystem_settings'] = 'Прежде чем продолжить установку системы исправьте, пожалуйста, перечисленные выше недочёты';
$lang['install']['sql_connect_error'] = 'Неудалось получить доступ к БД с указанными параметрами';
$lang['install']['password_error'] = 'Пароли не совпадают! Или в пароле есть запрещенные символы (например, &)...';
$lang['install']['install_completed'] = 'Установка успешно завершена! Обязательно сотрите папку install, без этого ничего не будет работать!';

$lang['install']['go2firstpage'] = 'Перейти на первую страницу сайта';
$lang['install']['where_is_config'] = 'Основные настройки сайте (пароли к БД, пути, визуальне темы) задаются в конфикурационном файле cfg/config.php. Вы момете вносить в него правки по своему желанию. ВАЖНО! Сохраните копию config.php перед исправлением, что бы иметь возможность вернуться к рабочей версии конфига.';

?>