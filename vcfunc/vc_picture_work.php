<?php
/**
 * Название: vcPictureWork 1.6
 * Описание: класс для работы с изображениями
 * Автор: Рыбин Роман aka VARCONER
 * Релиз: 26.11.2009
 * Версия 1.2: 25.02.2011
 * Версия 1.3: 19.09.2011
 * Версия 1.4: 27.07.2012
 * Версия 1.5: 28.01.2013
 * Версия 1.6: 18.11.2016
 * Контакты: romarybin@yandex.ru
 * Список функций:
 *	getImgSrc()
 *	calcSize()
 *	str2url()
 *	cyr2translit()
 */

$vcfunc_file_version = 1006000;
if (!isset($vcfunc_version) || $vcfunc_version<$vcfunc_file_version) $vcfunc_version = $vcfunc_file_version;
 
class vcPictureWork {
	// получить src обработанной картинки
	public static function getImgSrc(&$arr)
	{
		// $filename - оригинальное имя фаила
		$filename = isset($arr['filename'])?$arr['filename']:false;
		// $callPath - путь, откуда вызвана функция
		$callPath = isset($arr['callPath'])?$arr['callPath']:"";
		// $firstPath - путь откуда должна быть загружена картинка
		$firstPath = isset($arr['firstPath'])?$arr['firstPath']:false;
		// $secondPath - путь куда должна быть загружена картинка
		$secondPath = isset($arr['secondPath'])?$arr['secondPath']:false;
		// $setWidth - новая ширина
		$setWidth = isset($arr['setWidth'])?$arr['setWidth']:false;
		// $quality - качество JPEG на выходе
		$quality = isset($arr['quality'])?$arr['quality']:90;
		// (опция) $seoText - SEO-текст
		$seoText = isset($arr['seoText'])?$arr['seoText']:"";
		// (опция) $modifedFilename - измененное имя фаила для вторичного пути
		$modifedFilename = isset($arr['modifedFilename'])?$arr['modifedFilename']:false;
		// (опция) $waterMark - имя фаила водяного знака
		$waterMark = isset($arr['waterMark'])?$arr['waterMark']:false;
		// (опция) $waterMarkPosX - позиция X водяного знака
		$waterMarkPosX = isset($arr['waterMarkPosX'])?$arr['waterMarkPosX']:false;
		// (опция) $waterMarkPosY - позиция Y водяного знака
		$waterMarkPosY = isset($arr['waterMarkPosY'])?$arr['waterMarkPosY']:false;
		// (опция) $waterMarkPosY - позиция Y водяного знака
		$waterMarkPosY = isset($arr['waterMarkPosY'])?$arr['waterMarkPosY']:false;
		// (опция) $waterMarkTransparent - процент прозрачности водяного знака
		$waterMarkTransparent = isset($arr['waterMarkTransparent'])?$arr['waterMarkTransparent']:50;
		// (опция) $waterMarkLargerTo - во сколько раз водяной знак должен быть больше изображения
		$waterMarkLargerTo = isset($arr['waterMarkLargerTo'])?$arr['waterMarkLargerTo']:2;
		// (опция) $strictSize - строгий размер
		$strictSize = isset($arr['strictSize'])?$arr['strictSize']:false;
		// (опция) 
		$verifyRemoteFile = isset($arr['verifyRemoteFile'])?$arr['verifyRemoteFile']:false;
		
		// проверка
		if (!$filename || $firstPath===false || $secondPath===false) return "err-getImgSrc-noData";
		
		// преобразование SEO-текста
		if ($seoText) {
			$seoText = vcPictureWork::str2url($seoText);
			$seoText .= "-";
		}
		// полный вторичный путь фаила
		$secondFullPath = $secondPath.$seoText.($modifedFilename?$modifedFilename:$filename);
		// полный вторичный путь фаила в кэше
		$secondFullCachePath = $secondPath."cache/".$seoText."w".$setWidth."-".($modifedFilename?$modifedFilename:$filename);
		
		// если нужны изменения размера
		if ($setWidth) {
			// если фаил существует в кэше, возвратить src			
			if (file_exists(str_replace($callPath, "", $secondFullCachePath))) return $secondFullCachePath;
		// если не нужны изменения размера
		} else {
			// если фаил существует по второму пути, возвратить src
			if (file_exists(str_replace($callPath, "", $secondFullPath))) return $secondFullPath;
		}
		
		// определение типа и загрузка картинки
		$pathForLoad = str_replace($callPath, "", $firstPath.$filename);
		$source = false;
		if (!$verifyRemoteFile || vcPictureWork::remote_file_exists($pathForLoad)) { // проверка удаленного фаила // vcfunc 1.5		
			switch (substr($pathForLoad, -4)) {
			case "jpeg":
			case ".jpg":
			case "JPEG":
			case ".JPG":
				$source = @imagecreatefromjpeg($pathForLoad);
				break;
			case ".png":
			case ".PNG":
				$source = @imagecreatefrompng($pathForLoad);
				break;
			case ".gif":
			case ".GIF":
				$source = @imagecreatefromgif($pathForLoad);
				break;
			}
		}
		
		// доп. проверка // если не удалось загрузить, создать пустое изображение
		/* if (!$source) {
			$source = @imagecreatetruecolor($setWidth?$setWidth:100, isset($arr['newHeight'])&&$arr['newHeight']?$arr['newHeight']:100);
		} */
		
		// если удалось загрузить картинку
		if ($source) {
			// если нужны изменения размера
			if ($setWidth) {
				// изменить размер картинки на нужный
				$err = vcPictureWork::calcSize($arr);
				if ($err !== false) {
					if (isset($arr['newWidth']) && isset($arr['newHeight']) && isset($arr['width']) && isset($arr['height'])) {
						// создать новое изображение
						if ($strictSize && isset($arr['setHeight']) && $arr['setHeight']) {
							// если нужен жесткий размер
							$thumb = @imagecreatetruecolor($setWidth, $arr['setHeight']);
							$white = @imagecolorallocate($thumb, 255, 255, 255);
							@imagefill ($thumb, 0, 0, $white);
							$shiftX = ($setWidth - $arr['newWidth']) / 2;
							$shiftY = ($arr['setHeight'] - $arr['newHeight']) / 2;
						} else {
							$thumb = @imagecreatetruecolor($arr['newWidth'], $arr['newHeight']);
							$shiftX = 0;
							$shiftY = 0;
						}
						// изменение размера
						@imagecopyresampled($thumb, $source, $shiftX, $shiftY, 0, 0, $arr['newWidth'], $arr['newHeight'], $arr['width'], $arr['height']);
						// водяной знак
						if ($waterMark) {
							$sourceWM = @imagecreatefrompng(str_replace($callPath, "", $waterMark));
							$widthWM = @imagesx($sourceWM);
							$heightWM = @imagesy($sourceWM);
							// если помещается
							if ($widthWM < $arr['newWidth'] && $heightWM < $arr['newHeight']) {
								if ($widthWM < ($arr['newWidth'] / $waterMarkLargerTo) || $heightWM < ($arr['newHeight'] / $waterMarkLargerTo)) {
									// подсчет координат
									switch ($waterMarkPosX) {
									case "left":
										$xWM = 0;
										break;
									case "right":
										$xWM = $arr['newWidth'] - $widthWM;
										break;
									default:
										if (is_numeric($waterMarkPosX)) {
											$xWM = $waterMarkPosX;
										} else {
											$xWM = ($arr['newWidth'] - $widthWM) / 2;
										}
									}
									switch ($waterMarkPosY) {
									case "top":
										$yWM = 0;
										break;
									case "bottom":
										$yWM = $arr['newHeight'] - $heightWM;
										break;
									default:
										if (is_numeric($waterMarkPosY)) {
											$yWM = $waterMarkPosY;
										} else {
											$yWM = ($arr['newHeight'] - $heightWM) / 2;
										}
									}
									// объединение
									$completeWM = @imagecopymerge($thumb, $sourceWM, $xWM, $yWM, 0, 0, $widthWM, $heightWM, $waterMarkTransparent);
									if (!$completeWM) return "err-getImgSrc-noSetWM";
								}
							}
						}
						// запись в кэш
						if (@imagejpeg($thumb, $_SERVER["DOCUMENT_ROOT"].(substr($secondFullCachePath,0,1)=="/"?"":"/").$secondFullCachePath, $quality)) {
							return $secondFullCachePath;
						} else {
							return "err-getImgSrc-noCreateResizedPic";
						}
					} else {
						return "err-getImgSrc-noGetNewSize";
					}
				} else {
					return $err;
				}
			// если не нужны изменения размера
			} else {
				if (@imagejpeg($source, $_SERVER["DOCUMENT_ROOT"].(substr($secondFullPath,0,1)=="/"?"":"/").$secondFullPath, $quality)) {
					return $secondFullPath;
				} else {
					return "err-getImgSrc-noCreatePic";
				}
			}
		} else {
			return "err-getImgSrc-noLoadPic";
		}
	}
	
	// вычисление нового размера картинки
	public static function calcSize(&$arr)
	{
		// $filename - имя фаила
		$filename = isset($arr['filename'])?$arr['filename']:false;
		// $callPath - путь, откуда вызвана функция
		$callPath = isset($arr['callPath'])?$arr['callPath']:"";
		// $firstPath - путь откуда должна быть загружена картинка
		$firstPath = isset($arr['firstPath'])?$arr['firstPath']:false;
		// $setWidth - новая ширина
		$setWidth = isset($arr['setWidth'])?$arr['setWidth']:false;
		// $setHeight - новая высота (если false - высота пропорциональна ширине)
		$setHeight = isset($arr['setHeight'])?$arr['setHeight']:false;
		// $increase - разрешение увеличения
		$increase = isset($arr['increase'])?$arr['increase']:false;
		// $targetWidth - цель заданная ширина (если false - цель заданная высота)
		$targetWidth = isset($arr['targetWidth'])?$arr['targetWidth']:true;
		
		// проверка
		if (!$filename || $firstPath===false || $setWidth===false) return "err-calcSize-noData";
		
		// получение текущих размеров
		$width = false;
		$height = false;
		list($width, $height) = @getimagesize(str_replace($callPath, "", $firstPath.$filename));
		if (!$width || !$height) return "err-calcSize-noGetSize";
		// вычисление
		// если главное - ширина или высота не задана
		if ($targetWidth || !$setHeight) {
			// если (ширина больше заданной или высота больше заданной) или разрешено увеличение
			if ( ( $width > $setWidth || ($setHeight && $height>$setHeight) ) || $increase ) {
				$how = $width / $setWidth;
				$newWidth = $setWidth;
				$newHeight = round($height / $how);
				// коррекция, если новая высота больше заданной
				if ($setHeight && $newHeight > $setHeight) {
					$how2 = $setHeight / $newHeight;
					$newWidth = round($newWidth * $how2);
					$newHeight = $setHeight;
				}
			} else {
				$newWidth = $width;
				$newHeight = $height;
			}
		// если главное - высота
		} else {
			// если (высота больше заданной или ширина больше заданной) или разрешено увеличение
			if ( ($height>$setHeight || $width > $setWidth) || $increase ) {
				$how2 = $height / $setHeight;
				$newWidth = round($width / $how2);
				$newHeight = $setHeight;
				// коррекция, если новая ширина больше заданной
				if ($newWidth > $setWidth) {
					$how = $setWidth / $newWidth;
					$newWidth = $setWidth;
					$newHeight = round($newHeight * $how);
				}
			} else {
				$newWidth = $width;
				$newHeight = $height;
			}
		}
		// вставка новых данных в массив-ссылку
		$arr['width'] = $width;
		$arr['height'] = $height;
		$arr['newWidth'] = $newWidth;
		$arr['newHeight'] = $newHeight;
		
		return true;
	}
	
	// подготовка строки для URL
	public static function str2url($str) {
		// переводим в транслит
		$str = vcPictureWork::cyr2translit($str);
		// в нижний регистр
		$str = strtolower($str);
		// заменям все ненужное на дефисы
		$str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
		// удаляем сдвоенные дефисы
		$str = str_replace("--", "-", $str);
		// удаляем начальные и конечные дефисы
		$str = trim($str, "-");
		
		return $str;
	}
	
	// транслитерация кирилицы
	public static function cyr2translit($str) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'j',   'з' => 'z',
			'и' => 'i',   'й' => 'i',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '',    'ы' => 'y',   'ъ' => '',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'J',   'З' => 'Z',
			'И' => 'I',   'Й' => 'I',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'CH',  'Ш' => 'SH',  'Щ' => 'SCH',
			'Ь' => '',    'Ы' => 'Y',   'Ъ' => '',
			'Э' => 'E',   'Ю' => 'YU',  'Я' => 'YA'
		);
		$str = strtr($str, $converter);
		
		return $str;
	}
	
	// проверка удаленного фаила
	public static function remote_file_exists($url) {
		return (bool)preg_match('~HTTP/1\.\d\s+200\s+OK~', @current(get_headers($url)));
	} 
}
?>