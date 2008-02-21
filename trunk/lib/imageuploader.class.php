<?php

/**
 * Загрузчик картинок для NanoGrabber
 * 
 * @package NanoGrabbr <http://nanograbbr.com> 
 * @author Aist <aist@nanograbbr.org>
 * 
 */

class ImageUploader {
	
	var $upload_path;
	var $upload_dir;
	var $need_resize = false; // флаг о необходимости ресайзить картинку
	var $max_width; // максимальная допустимая ширина картинки
	var $image_ext = array('gif', 'jpg', 'png', 'jpeg');	

/**
 * Конструктор класса
 *
 * @param unknown_type $upload_path - путь, по которому будут доступны для скачивания сохраненные картинки
 * @param unknown_type $upload_dir - директория, куда будут сохранятся картинки
 * @param unknown_type $max_width - максимальная ширина картинки, до которой будут уменьшены большие изображения при закачивании
 * @return ImageUploader
 */
	function ImageUploader($upload_path, $upload_dir, $max_width = 650) {
		$this->upload_path = $upload_path;
		$this->upload_dir = $upload_dir;
		$this->max_width = $max_width;
	}
	
/**
 * Загрузка картинки с компьютера пользователя
 *
 * @return unknown - true или массив с ошибками
 */
	function imageUpload() {
		$error  =array();
		if ($_FILES['img_file']['error'] !== 0) {									
			$error[] = '{{ $lang.site.error_upload_file }}';
		} elseif (strpos($_FILES['img_file']['type'], "image")===false) {
			$error[] = '{{ $lang.site.error_wrong_file_type }}';
		} else {
			if (!file_exists($this->upload_path.date("Ym"))) {
				mkdir($this->upload_path.date("Ym"));
				@chmod($this->upload_path.date("Ym"), 0777);
			}
			if (file_exists($this->upload_path.date("Ym")."/".$_FILES['img_file']['name'])) $pref = time().'_';
			else $pref = '';
			if (!move_uploaded_file($_FILES['img_file']['tmp_name'], $this->upload_path.date("Ym")."/".$pref.$_FILES['img_file']['name'])) {
				$error[] = '{{ $lang.site.error_move_error }}';
			} else {
				@chmod($this->upload_path.date("Ym")."/".$pref.$_FILES['img_file']['name'], 0777);
				$_POST['url'] = $this->upload_dir.date("Ym")."/".$pref.$_FILES['img_file']['name'];
			}
		}		
		return empty($error) ? true : $error;
	}
	
/**
 * Загрузка картинки с удаленного сервера
 *
 * @return unknown
 */
	function imageDownload() {
		// указан урл картинки и нужно её сохранить у себя
		$error = array();
		$file_name = pathinfo($_POST['url']);
		$file = file_get_contents($_POST['url'], 3145728);
		if (!file_exists($this->upload_path."tmp")) {
			mkdir($this->upload_path."tmp");
			@chmod($this->upload_path."tmp", 0777);
		}
		$tmp_name = md5(time()).$file_name['basename'];
		$fp = fopen($this->upload_path."tmp/".$tmp_name, "w");
		fwrite($fp, $file);
		fclose($fp);
		$ext = strtolower(substr($tmp_name, strrpos($tmp_name, ".")+1));
		if (in_array($ext, $this->image_ext)) {									
			if (!file_exists($this->upload_path.date("Ym"))) {
				mkdir($this->upload_path.date("Ym"));
				@chmod($this->upload_path.date("Ym"), 0777);
			}									
			if (file_exists($this->upload_path.date("Ym")."/".$file_name['basename'])) $pref = time().'_';
			else $pref = '';			
			copy($this->upload_path."tmp/".$tmp_name, $this->upload_path.date("Ym")."/".$pref.$file_name['basename']);
			unlink($this->upload_path."tmp/".$tmp_name);
			@chmod($this->upload_path.date("Ym")."/".$pref.$file_name['basename'], 0777);
			$_POST['url'] = $this->upload_dir.date("Ym")."/".$pref.$file_name['basename'];
			unset($_POST['save_url']);									
		} else {
			// закачивают не картинку!
			unlink($this->upload_path."tmp/".$tmp_name);
			$error[] = '{{ $lang.site.error_wrong_file_type }}';
		}
		return empty($error) ? true : $error;			
	}
	
/**
* Проверка необходимости ресайза картинки
*
*/
	function checkResize() {		
		$tmp =  getimagesize($_POST['url']);
		list($width, $height) = $tmp;
		$mime = $tmp['mime'];
		if ($width > $this->max_width) {
			/**
			 * большая картинка, нужно ресайзить
			 * Ресайз производится следующим способом: к имени картинки добавляется слово _original 
			 * для обозначения того, что это оригинал картинки
			 * 
			 */
			$new_file_name = substr_replace($_POST['url'], '_original'.substr($_POST['url'], strrpos($_POST['url'],'.')), strrpos($_POST['url'],'.'));
			$ext = explode("/", $mime);
			$ext = $ext[1];
			copy($_POST['url'], $new_file_name);
			chmod($new_file_name, 0777);
			
			$percent = (($this->max_width * 100) / $width) / 100;
			$new_width = $width * $percent;
			$new_height = $height * $percent;					

			$new_image = imagecreatetruecolor($new_width, $new_height);
			switch ($ext) {
				case "jpg":
				case "jpeg":
					$source = imagecreatefromjpeg($_POST['url']);
					imagecopyresized($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					imagejpeg($new_image, $_POST['url']);
					break;
				case 'png':
					$source = imagecreatefrompng($_POST['url']);
					imagecopyresized($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					imagepng($new_image, $_POST['url']);					
					break;
				case 'gif':
					$source = imagecreatefromgif($_POST['url']);
					imagecopyresized($new_image, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					imagegif($new_image, $_POST['url']);					
					break;
			}
						
		}
	}
	
} // class

?>