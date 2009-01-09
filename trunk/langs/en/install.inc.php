<?php
/**
 * Localization for installer's text notifications
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

$lang['install']['title'] = 'Installation system for installing Nanograbbr microblogging engine';
$lang['install']['first_page_text'] = 'You\'re starting to install Nanograbbr microblogging engine.<br>Choose language';
$lang['install']['second_page_text'] = 'Checking server main settings';
$lang['install']['check_config_writable'] = 'Do you have enough rights to make records in config file: ';
$lang['install']['config_unwritable'] = 'You need to set permissions: ';
$lang['install']['check_img_writable'] = 'Do you have enough rights to create files in Image folder: ';
$lang['install']['check_gdlib'] = 'Library GD lib: ';
$lang['install']['next'] = 'next';
$lang['install']['back'] = 'back';
$lang['install']['check'] = 'check';
$lang['install']['db_host'] = 'DB server';
$lang['install']['db_user'] = 'DB user';
$lang['install']['db_passwd'] = 'DB password';
$lang['install']['db_name'] = 'DB name';
$lang['install']['db_prefix'] = 'Prefix for tables names in DB';
$lang['install']['password'] = 'Administrator password';
$lang['install']['password2'] = 'Password confirmation';
$lang['install']['site_title'] = 'Site name';
$lang['install']['all_fields'] = 'All fields are mandatory to be filled in!';
$lang['install']['check_updates'] = 'Enable automatic checking for updates (recommended)';
$lang['install']['email4notification'] = 'E-mail for receiving notifications';
$lang['install']['notification'] = 'Would you like to receive notifications about new comments on your e-mail?';


$lang['install']['wrong_filesystem_settings'] = 'Before moving to the next step of installation you should correct defects mentioned above';
$lang['install']['sql_connect_error'] = 'Can\'t access to DB with the following parameters';
$lang['install']['password_error'] = 'Passwords do not matched! Maybe, you have forbidden symbols in password (like '&' or something like that)';
$lang['install']['install_completed'] = 'Installation completed! You should delete \'Install\' folder to finish installation process. Site will be unavailable until you complete this step!';

$lang['install']['go2firstpage'] = 'Go to the first page';
$lang['install']['where_is_config'] = 'Main settings of the site (DB passwords, paths, site themes) could be changed in configuration file cfg/config.php. You could make any changes as you wish. Important! Save copy config.php before you\'ll make any changes in this file - it could be useful if something goes wrong :)';

?>