<?php
/**
 * Простейший шаблонизатор для NanoGrabbr
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class NanoTemplate {
	
	var $templateFile; // файл с шаблоном
	var $template; // шаблонон
	var $parseResilt; 
	var $needed_blocks = array(); // массив блоков, которые используются
	var $blocks_stack = array(); // массив блоков, которые в обработке
	var $language; // используемый язык
	var $lang; // массив фраз
	var $vars; // массив переменных и значений для замены в шаблоне
	
	function NanoTemplate($tplFile, $lang = null) {
		$this->templateFile = $tplFile;
		$this->readTpl();
		$this->parseTpl();
		$this->language = $lang ? $lang : 'ru';		
	}
	
/**
 * Читаем шаблон в память	 
 */
	function readTpl() {
		$fp = fopen($this->templateFile, "r");	
		$this->template = fread($fp, filesize($this->templateFile));
		fclose($fp);		
	}
	
/**
 * Первоначальный парсинг шаблона	 
 */
	function parseTpl() {
		$pattern = "/{{ (.*) }}/imU";
		preg_match_all($pattern, $this->template, $this->parseResilt);
		$this->parseResilt = $this->parseResilt[1];				
	}	
	
/**
 * Установить блок в шаблоне
 *
 * @param unknown_type $blockName - имя блока ({{ BEGIN blockName }})
 */
	function setBlock($blockName) {
		$this->needed_blocks[] = $blockName;
	}

/**
 * Устанавливаем переменную в шаблоне
 *
 * @param string $tplName - имя переменной в шаблоне
 * @param string $value - хначение
 */
	function set($tplName, $value) {
		$this->vars['$'.$tplName] = $value;
	}
	
/**
 * Локализация фраз в шаблоне
 *
 * @param unknown_type $element
 * @return unknown
 */
	function langInTpl($element) {
		$f = strpos($element, '.');
		$l = strrpos($element, '.');		
		$lang_file = substr($element, $f+1, ($l-$f-1));
		$lang_element = substr($element, ($l+1));			
		if (strpos($_SERVER['PHP_SELF'], 'install')===false) include("langs/".$this->language."/".$lang_file.".inc.php");				
		else include("../langs/".$this->language."/".$lang_file.".inc.php");				
		if (isset($lang)) $this->lang = $lang;		
		return $this->lang[$lang_file][$lang_element];
	}
	
/**
 * Парсинг блоков, замена перемнных на значения и вывод всего этого на экран
 *
 */
	function create() {					
		for ($i= 0; $i<count($this->parseResilt); $i++) {			
			$tplEl = $this->parseResilt[$i];
			if (strpos($tplEl, 'BEGIN')!==false) {		
				$blockName = substr($tplEl, strpos($tplEl, " ")+1);		
				if (in_array($blockName, $this->needed_blocks)) {
					// начинается какой-то блок
					array_push($this->blocks_stack, $blockName);
				} else {
					// этот блок неактивный
					// Удаляем блок из шаблона								
					$this->template = preg_replace("/{{ BEGIN ".$blockName." }}(.*){{ END ".$blockName." }}/imUs", "", $this->template);
					do {
						$i++;
					} while ($this->parseResilt[$i] != "END ".$blockName);
				} 			
			}			
			if (strpos($tplEl, 'END')!==false && in_array(substr($tplEl, strpos($tplEl, " ")+1), $this->needed_blocks)) {				
				// кончился какой-то блок
				$b = array_pop($this->blocks_stack);
				if ($b != substr($tplEl, strpos($tplEl, " ")+1)) {
					die("Error on template! BEGIN and END block mixed!");
				}
			}
			
			if (strpos($tplEl, 'START')!==false) {
				// цикл					
				$forName = substr($tplEl, strpos($tplEl, " ")+1);				
				$s_s = strpos($this->template, '{{ START '.$forName.' }}')+strlen('{{ START '.$forName.' }}');
				$forBody = substr($this->template, $s_s, strpos($this->template, '{{ FINISH '.$forName.' }}')-$s_s);				
				if (isset($this->vars[$forName]) && !empty($this->vars[$forName])) {					
					$t = $this->template;
					$this->template = str_replace('{{ START '.$forName.' }}'.$forBody.'{{ FINISH '.$forName.' }}', '{{ FOR '.$forName.' }}', $this->template);															
					$forBody .= '{{ FOR '.$forName.' }}';
					for ($j=0; $j<count($this->vars[$forName]); $j++) {
						$fb = $forBody;						
						foreach ($this->vars[$forName][$j] as $k=>$v) {
							$fb = str_replace('{{ '.$forName.'.'.$k.' }}', $v, $fb);							
						}												
						$this->template = str_replace('{{ FOR '.$forName.' }}', $fb, $this->template);							
					}
				} else {
					// нет значения для вывода в цикле					
					$this->template = str_replace('{{ START '.$forName.' }}'.$forBody.'{{ FINISH '.$forName.' }}', '', $this->template);															
				}
			}
			if (strpos($tplEl, '$lang')!==false) {				
				 $this->template = str_replace("{{ ".$tplEl." }}", $this->langInTpl($tplEl), $this->template);				
			}
			if (strpos($tplEl, ".")) $tplEl = substr($tplEl, 0, strpos($tplEl, "."));
			if (isset($this->vars[$tplEl]) && !empty($this->vars[$tplEl])) {
				if (@is_array($this->vars[$tplEl])) {					
					foreach ($this->vars[$tplEl] as $key=>$val) {
						$this->template = str_replace("{{ ".$tplEl.".".$key." }}", $val, $this->template);
					}
				} else {
					$this->template = str_replace("{{ ".$tplEl." }}", $this->vars[$tplEl], $this->template);
				}
			}
										
		}
		$this->template = preg_replace("{{{ .*? }}}", "", $this->template);
		return $this->template;
	}
	
/**
 * Вывод сформированного шаблона
 *
 */
	function show() {
		echo $this->create();
	}
	
} // class

?>