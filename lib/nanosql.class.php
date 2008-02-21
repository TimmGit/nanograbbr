<?php

/**
 * Класс для работы с SQL
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class NanoSQL {
	
	var $prefix; // префикс таблиц в БД
	
	function NanoSQL($conf) {		
		// коннект к базе
		mysql_select_db($conf->config_val['db']['name'], 
			mysql_connect($conf->config_val['db']['host'], $conf->config_val['db']['user'], $conf->config_val['db']['passwd']))
			or $this->mydie('Connect to MySQL faild');
		$this->query("SET NAMES utf8"); // установка кодировки
		$this->prefix = $conf->config_val['db']['prefix']; // префикс для таблиц
	}

/**
 * Формирование и выполнение SELECT запроса к БД
 *
 * @param str $tbl - имя таблицы
 * @param array|string $fields - поля, которые нужно выбрать
 * @param array|string $where - условия выборки
 * @param string $order - по какому полю сортировать
 * @param int $limit - ограничение на количество результатов выборки
 * @return resource
 */
	function select($tbl, $fields, $where = 1, $order = null, $limit = null) {
		if (@!is_array($fields)) {
			$field[] = $fields;
			$fields = $field;
		}
		if (@is_array($where)) {
			$w = $where;
			$where = "";
			foreach ($w as $k=>$v) {
				$where[] = "`".$k."` = '".$v."'"; 
			}
			$where = implode(" AND ", $where);
		}
		$sql = "SELECT ".implode(", ", $fields)." FROM `".$this->prefix.$tbl."` WHERE ".$where;
		if ($order) {
			list($k, $v) = each($order);
			$sql .= ' ORDER BY '.$k.' '.$v;
		}
		if ($limit) $sql .= " LIMIT ".$limit;		
		$r = $this->query($sql);
		return $r;
	}
	
/**
 * Формирование и выполнение запроса на INSERT
 *
 * @param string $tbl - имя таблицы, куда нужно добавить запись
 * @param array $fields - массив из полей и их значений, которые нужно использовать в запросе
 * @return array - идентификатор вставленной записи
 */
	function insert($tbl, $fields) {
		foreach ($fields as $k=>$v) {
			if (is_string($v)) $v = "'".addslashes($v)."'";
			$fld[] = $k." = ".$v;
		}
		$fields = implode(", ", $fld);
		
		$sql = "INSERT INTO `".$this->prefix.$tbl."` SET ".$fields;		
		$this->query($sql);
		return array("id" => mysql_insert_id());
	}
	
/**
 * Формирование и выполнение запроса на UPDATE
 *
 * @param string $tbl - имя таблицы
 * @param array $fields - набор полей, который нужно обновить
 * @param array $where - условие для обновления
 * @return bool
 */
	function update($tbl, $fields, $where) {
		foreach ($fields as $k=>$v) {
			if (@is_array($v)) $v = implode('', $v);
			elseif (is_string($v)) $v = "'".addslashes($v)."'";
			$fld[] = $k." = ".$v;
		}
		$fields = implode(", ", $fld);
		$w = $where;
		$where = "";
		foreach ($w as $k=>$v) {
			$where[] = "`".$k."` = '".$v."'"; 
		}
		$where = implode(" AND ", $where);
		$sql = "UPDATE `".$this->prefix.$tbl."` SET ".$fields.' WHERE '.$where;		
		if ($this->query($sql)) return true;
		else return false;		
	}
	
/**
 * Формирование и выполнение запроса DELETE
 *
 * @param string $tbl - имя таблицы
 * @param array $where - условие для удаления
 * @return bool
 */
	function delete($tbl, $where) {
		$w = $where;
		$where = "";
		foreach ($w as $k=>$v) {
			$where[] = "`".$k."` = '".$v."'"; 
		}
		$where = implode(' AND ', $where);
		$sql = 'DELETE FROM `'.$this->prefix.$tbl.'` WHERE '.$where;
		if ($this->query($sql)) return true;
		else return false;				
	}
	
/**
 * Выполнение запроса к БД 
 *
 * @param string $sql - SQL-запрос
 * @return resource
 */
	function query($sql) {
		$r = mysql_query($sql) or $this->mydie(mysql_error(), $sql);		
		return $r;
	}
	
/**
 * Логирование ошибок
 * Ошибки записывабтся в стандертный error_log веб-сервера
 *
 * @param string $msg
 * @param string $sql
 */
	function mydie($msg, $sql = null) {
		error_log("SQL error: ".$msg." on query: ".$sql);
	}
	
} // class

?>