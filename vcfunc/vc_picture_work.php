<?php
/**
 * ��������: vcPictureWork 1.6
 * ��������: ����� ��� ������ � �������������
 * �����: ����� ����� aka VARCONER
 * �����: 26.11.2009
 * ������ 1.2: 25.02.2011
 * ������ 1.3: 19.09.2011
 * ������ 1.4: 27.07.2012
 * ������ 1.5: 28.01.2013
 * ������ 1.6: 18.11.2016
 * ��������: romarybin@yandex.ru
 * ������ �������:
 *	getImgSrc()
 *	calcSize()
 *	str2url()
 *	cyr2translit()
 */

$vcfunc_file_version = 1006000;
if (!isset($vcfunc_version) || $vcfunc_version<$vcfunc_file_version) $vcfunc_version = $vcfunc_file_version;
 
class vcPictureWork {
	// �������� src ������������ ��������
	public static function getImgSrc(&$arr)
	{
		// $filename - ������������ ��� �����
		$filename = isset($arr['filename'])?$arr['filename']:false;
		// $callPath - ����, ������ ������� �������
		$callPath = isset($arr['callPath'])?$arr['callPath']:"";
		// $firstPath - ���� ������ ������ ���� ��������� ��������
		$firstPath = isset($arr['firstPath'])?$arr['firstPath']:false;
		// $secondPath - ���� ���� ������ ���� ��������� ��������
		$secondPath = isset($arr['secondPath'])?$arr['secondPath']:false;
		// $setWidth - ����� ������
		$setWidth = isset($arr['setWidth'])?$arr['setWidth']:false;
		// $quality - �������� JPEG �� ������
		$quality = isset($arr['quality'])?$arr['quality']:90;
		// (�����) $seoText - SEO-�����
		$seoText = isset($arr['seoText'])?$arr['seoText']:"";
		// (�����) $modifedFilename - ���������� ��� ����� ��� ���������� ����
		$modifedFilename = isset($arr['modifedFilename'])?$arr['modifedFilename']:false;
		// (�����) $waterMark - ��� ����� �������� �����
		$waterMark = isset($arr['waterMark'])?$arr['waterMark']:false;
		// (�����) $waterMarkPosX - ������� X �������� �����
		$waterMarkPosX = isset($arr['waterMarkPosX'])?$arr['waterMarkPosX']:false;
		// (�����) $waterMarkPosY - ������� Y �������� �����
		$waterMarkPosY = isset($arr['waterMarkPosY'])?$arr['waterMarkPosY']:false;
		// (�����) $waterMarkPosY - ������� Y �������� �����
		$waterMarkPosY = isset($arr['waterMarkPosY'])?$arr['waterMarkPosY']:false;
		// (�����) $waterMarkTransparent - ������� ������������ �������� �����
		$waterMarkTransparent = isset($arr['waterMarkTransparent'])?$arr['waterMarkTransparent']:50;
		// (�����) $waterMarkLargerTo - �� ������� ��� ������� ���� ������ ���� ������ �����������
		$waterMarkLargerTo = isset($arr['waterMarkLargerTo'])?$arr['waterMarkLargerTo']:2;
		// (�����) $strictSize - ������� ������
		$strictSize = isset($arr['strictSize'])?$arr['strictSize']:false;
		// (�����) 
		$verifyRemoteFile = isset($arr['verifyRemoteFile'])?$arr['verifyRemoteFile']:false;
		
		// ��������
		if (!$filename || $firstPath===false || $secondPath===false) return "err-getImgSrc-noData";
		
		// �������������� SEO-������
		if ($seoText) {
			$seoText = vcPictureWork::str2url($seoText);
			$seoText .= "-";
		}
		// ������ ��������� ���� �����
		$secondFullPath = $secondPath.$seoText.($modifedFilename?$modifedFilename:$filename);
		// ������ ��������� ���� ����� � ����
		$secondFullCachePath = $secondPath."cache/".$seoText."w".$setWidth."-".($modifedFilename?$modifedFilename:$filename);
		
		// ���� ����� ��������� �������
		if ($setWidth) {
			// ���� ���� ���������� � ����, ���������� src			
			if (file_exists(str_replace($callPath, "", $secondFullCachePath))) return $secondFullCachePath;
		// ���� �� ����� ��������� �������
		} else {
			// ���� ���� ���������� �� ������� ����, ���������� src
			if (file_exists(str_replace($callPath, "", $secondFullPath))) return $secondFullPath;
		}
		
		// ����������� ���� � �������� ��������
		$pathForLoad = str_replace($callPath, "", $firstPath.$filename);
		$source = false;
		if (!$verifyRemoteFile || vcPictureWork::remote_file_exists($pathForLoad)) { // �������� ���������� ����� // vcfunc 1.5		
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
		
		// ���. �������� // ���� �� ������� ���������, ������� ������ �����������
		/* if (!$source) {
			$source = @imagecreatetruecolor($setWidth?$setWidth:100, isset($arr['newHeight'])&&$arr['newHeight']?$arr['newHeight']:100);
		} */
		
		// ���� ������� ��������� ��������
		if ($source) {
			// ���� ����� ��������� �������
			if ($setWidth) {
				// �������� ������ �������� �� ������
				$err = vcPictureWork::calcSize($arr);
				if ($err !== false) {
					if (isset($arr['newWidth']) && isset($arr['newHeight']) && isset($arr['width']) && isset($arr['height'])) {
						// ������� ����� �����������
						if ($strictSize && isset($arr['setHeight']) && $arr['setHeight']) {
							// ���� ����� ������� ������
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
						// ��������� �������
						@imagecopyresampled($thumb, $source, $shiftX, $shiftY, 0, 0, $arr['newWidth'], $arr['newHeight'], $arr['width'], $arr['height']);
						// ������� ����
						if ($waterMark) {
							$sourceWM = @imagecreatefrompng(str_replace($callPath, "", $waterMark));
							$widthWM = @imagesx($sourceWM);
							$heightWM = @imagesy($sourceWM);
							// ���� ����������
							if ($widthWM < $arr['newWidth'] && $heightWM < $arr['newHeight']) {
								if ($widthWM < ($arr['newWidth'] / $waterMarkLargerTo) || $heightWM < ($arr['newHeight'] / $waterMarkLargerTo)) {
									// ������� ���������
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
									// �����������
									$completeWM = @imagecopymerge($thumb, $sourceWM, $xWM, $yWM, 0, 0, $widthWM, $heightWM, $waterMarkTransparent);
									if (!$completeWM) return "err-getImgSrc-noSetWM";
								}
							}
						}
						// ������ � ���
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
			// ���� �� ����� ��������� �������
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
	
	// ���������� ������ ������� ��������
	public static function calcSize(&$arr)
	{
		// $filename - ��� �����
		$filename = isset($arr['filename'])?$arr['filename']:false;
		// $callPath - ����, ������ ������� �������
		$callPath = isset($arr['callPath'])?$arr['callPath']:"";
		// $firstPath - ���� ������ ������ ���� ��������� ��������
		$firstPath = isset($arr['firstPath'])?$arr['firstPath']:false;
		// $setWidth - ����� ������
		$setWidth = isset($arr['setWidth'])?$arr['setWidth']:false;
		// $setHeight - ����� ������ (���� false - ������ ��������������� ������)
		$setHeight = isset($arr['setHeight'])?$arr['setHeight']:false;
		// $increase - ���������� ����������
		$increase = isset($arr['increase'])?$arr['increase']:false;
		// $targetWidth - ���� �������� ������ (���� false - ���� �������� ������)
		$targetWidth = isset($arr['targetWidth'])?$arr['targetWidth']:true;
		
		// ��������
		if (!$filename || $firstPath===false || $setWidth===false) return "err-calcSize-noData";
		
		// ��������� ������� ��������
		$width = false;
		$height = false;
		list($width, $height) = @getimagesize(str_replace($callPath, "", $firstPath.$filename));
		if (!$width || !$height) return "err-calcSize-noGetSize";
		// ����������
		// ���� ������� - ������ ��� ������ �� ������
		if ($targetWidth || !$setHeight) {
			// ���� (������ ������ �������� ��� ������ ������ ��������) ��� ��������� ����������
			if ( ( $width > $setWidth || ($setHeight && $height>$setHeight) ) || $increase ) {
				$how = $width / $setWidth;
				$newWidth = $setWidth;
				$newHeight = round($height / $how);
				// ���������, ���� ����� ������ ������ ��������
				if ($setHeight && $newHeight > $setHeight) {
					$how2 = $setHeight / $newHeight;
					$newWidth = round($newWidth * $how2);
					$newHeight = $setHeight;
				}
			} else {
				$newWidth = $width;
				$newHeight = $height;
			}
		// ���� ������� - ������
		} else {
			// ���� (������ ������ �������� ��� ������ ������ ��������) ��� ��������� ����������
			if ( ($height>$setHeight || $width > $setWidth) || $increase ) {
				$how2 = $height / $setHeight;
				$newWidth = round($width / $how2);
				$newHeight = $setHeight;
				// ���������, ���� ����� ������ ������ ��������
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
		// ������� ����� ������ � ������-������
		$arr['width'] = $width;
		$arr['height'] = $height;
		$arr['newWidth'] = $newWidth;
		$arr['newHeight'] = $newHeight;
		
		return true;
	}
	
	// ���������� ������ ��� URL
	public static function str2url($str) {
		// ��������� � ��������
		$str = vcPictureWork::cyr2translit($str);
		// � ������ �������
		$str = strtolower($str);
		// ������� ��� �������� �� ������
		$str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
		// ������� ��������� ������
		$str = str_replace("--", "-", $str);
		// ������� ��������� � �������� ������
		$str = trim($str, "-");
		
		return $str;
	}
	
	// �������������� ��������
	public static function cyr2translit($str) {
		$converter = array(
			'�' => 'a',   '�' => 'b',   '�' => 'v',
			'�' => 'g',   '�' => 'd',   '�' => 'e',
			'�' => 'e',   '�' => 'j',   '�' => 'z',
			'�' => 'i',   '�' => 'i',   '�' => 'k',
			'�' => 'l',   '�' => 'm',   '�' => 'n',
			'�' => 'o',   '�' => 'p',   '�' => 'r',
			'�' => 's',   '�' => 't',   '�' => 'u',
			'�' => 'f',   '�' => 'h',   '�' => 'c',
			'�' => 'ch',  '�' => 'sh',  '�' => 'sch',
			'�' => '',    '�' => 'y',   '�' => '',
			'�' => 'e',   '�' => 'yu',  '�' => 'ya',
			'�' => 'A',   '�' => 'B',   '�' => 'V',
			'�' => 'G',   '�' => 'D',   '�' => 'E',
			'�' => 'E',   '�' => 'J',   '�' => 'Z',
			'�' => 'I',   '�' => 'I',   '�' => 'K',
			'�' => 'L',   '�' => 'M',   '�' => 'N',
			'�' => 'O',   '�' => 'P',   '�' => 'R',
			'�' => 'S',   '�' => 'T',   '�' => 'U',
			'�' => 'F',   '�' => 'H',   '�' => 'C',
			'�' => 'CH',  '�' => 'SH',  '�' => 'SCH',
			'�' => '',    '�' => 'Y',   '�' => '',
			'�' => 'E',   '�' => 'YU',  '�' => 'YA'
		);
		$str = strtr($str, $converter);
		
		return $str;
	}
	
	// �������� ���������� �����
	public static function remote_file_exists($url) {
		return (bool)preg_match('~HTTP/1\.\d\s+200\s+OK~', @current(get_headers($url)));
	} 
}
?>