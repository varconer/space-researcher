<?php
/**
 * Название: vcDatabaseWork 2.0
 * Описание: класс для работы с БД
 * Автор: Рыбин Роман aka VARCONER
 * Релиз: 14.11.2009
 * Версия 1.7: 15.12.2014
 * Версия 1.8: 04.02.2015
 * Версия 2.0: 18.11.2016
 * Контакты: romarybin@yandex.ru
 * Список функций:
 *	db_work()
 *	db_backup()
 */

$vcfunc_file_version = 2000000;
if (!isset($vcfunc_version) || $vcfunc_version<$vcfunc_file_version) $vcfunc_version = $vcfunc_file_version;
 
class vcDatabaseWork {
	// функция обработки запросов СУБД
	public static function db_work($db_query, $db_alt_connect_settings = array(), $allow_replace_data = true)
	{
		// $db_query - запрос
		// $db_alt_connect_settings - массив альтернативных настроек подключения к БД
		// $allow_replace_data - флаг использования массива подстановки данных в результате запроса
		
		// $db_connection - ID основного подключения к БД
		// $db_log - лог БД
		// $db_errors - лог ошибок БД
		// $vcwe_alt_db - глобальный массив альтернативных настроек подключения к БД
		// $vcwe_db_replace_data - глобальный массив принудительной подстановки значений в результат запроса БД в формате array(таблица => array(поле => значение))
		// $vcwe_allow_replace_data - глобальный флаг использования массива подстановки данных в результате запроса

		// $db_read - если true, возвращает массив результатов
		// $db_result - ID запроса БД
		// $db_result_len - длинна таблицы результата запроса
		// $db_result_array - возвращаемый массив результатов
		
		// Verify
		if (!defined('VCWE_DB_TYPE')) return false;
		if (!defined('VCWE_DB_HOST')) return false;
		if (!defined('VCWE_DB_LOGIN')) return false;
		if (!defined('VCWE_DB_PASSWORD')) return false;
		if (!defined('VCWE_DB_ENCODE')) return false;
		if (!defined('VCWE_DB_NAME')) return false;
		
		global $db_connection, $db_log, $db_errors, $vcwe_alt_db, $vcwe_db_replace_data, $vcwe_allow_replace_data;
		
		// объединение переданного массива настроек с глобальным массивом альтернативных настроек БД
		$db_alt_connect_settings = array_merge($vcwe_alt_db, $db_alt_connect_settings);
		// выбор альтернативного или обычного режима
		$db_alt_mode = $db_alt_connect_settings?true:false;
		// проверка флагов разрешения подстановки
		if ($allow_replace_data && !$vcwe_allow_replace_data) $allow_replace_data = false;
		
		// выбор настроек подключения к БД
		$db_type = isset($db_alt_connect_settings['type'])?$db_alt_connect_settings['type']:VCWE_DB_TYPE;
		$db_host = isset($db_alt_connect_settings['host'])?$db_alt_connect_settings['host']:VCWE_DB_HOST;
		$db_login = isset($db_alt_connect_settings['login'])?$db_alt_connect_settings['login']:VCWE_DB_LOGIN;
		$db_password = isset($db_alt_connect_settings['password'])?$db_alt_connect_settings['password']:VCWE_DB_PASSWORD;
		$db_encode = isset($db_alt_connect_settings['encode'])?$db_alt_connect_settings['encode']:VCWE_DB_ENCODE;
		$db_name = isset($db_alt_connect_settings['name'])?$db_alt_connect_settings['name']:VCWE_DB_NAME;
		
		// Для СУБД MySQL
		if ($db_type == 'mysql') {
			// Определение типа запроса
			$db_read = false;
			$db_read_table = "";
			if (strpos($db_query, "SELECT")!==false) {
				$db_read = true;
				// определение основной таблицы-источника данных
				if ($allow_replace_data && preg_match("/FROM `([a-z]+)`/", $db_query, $matches)) {
					if (isset($matches[1])) $db_read_table = $matches[1];
				}
			} elseif (substr($db_query, 0, 4)=="SHOW") {
				$db_read = true;
			}
			if (!$db_connection || $db_alt_mode) {
				// в случае необходимости альтернативного подключения, отключить основное
				if ($db_connection && $db_alt_mode) {
					@mysql_close($db_connection);
					$db_connection = false;
				}
				// Соединение с СУБД
				$temp = @mysql_connect($db_host, $db_login, $db_password) or die("Error: ".mysql_error());
				if ($db_alt_mode) {
					$db_alt_connection = $temp;
				} else {
					$db_connection = $temp;
				}
				// Выбор БД
				if ($db_encode) {
					@mysql_query("/*!40101 SET NAMES '".$db_encode."'".(define('VCWE_DB_COLLATION')?" COLLATE '".VCWE_DB_COLLATION."'":"")." */", $db_alt_mode?$db_alt_connection:$db_connection) or die("Error: ".mysql_error()); // решение проблемы кодировки
				}
				@mysql_select_db($db_name, $db_alt_mode?$db_alt_connection:$db_connection) or die("Error: ".mysql_error());
			}
			// запрос
			$db_result = @mysql_unbuffered_query($db_query, $db_alt_mode?$db_alt_connection:$db_connection);
			$db_log .= "<b>MySQL query:</b> ".$db_query."<br>";
			$error = $db_result === false ? mysql_errno().": ".mysql_error() : "";
			// получение результата запроса к БД
			if ($db_result && $db_read) {
				$i = 0;
				// создание массива результатов запроса в формате:
				// $db_result_array ([имя поля] => array([индекс строки] => [содержание строки соответствующего поля]))
				while ($row = @mysql_fetch_array($db_result, MYSQL_ASSOC)) {
					foreach ($row as $name => $value) {
						$db_result_array[$name][$i] = $value;
					}
					$i++;
				}
			}
			if ($db_alt_mode) @mysql_close($db_alt_connection);
			// запись ошибки в лог
			if ($error) {
				$db_log .= $error."<br>";
				$db_errors .= "<b>Error query:</b> ".$db_query."<br><b>Error description:</b> ".$error."<br><br>";
				return false;
			}
			// Если ненужны данные из БД, возвратить ID запроса или false в случае ошибки
			if (!$db_read) return $db_result;
			// Возвратить данные из БД если есть
			if (isset($db_result_array)) {
				// если известна таблица источник и есть поля для замены данными из массива подстановки
				if ($allow_replace_data && $db_read_table && isset($vcwe_db_replace_data[$db_read_table]) && is_array($vcwe_db_replace_data[$db_read_table])) {
					foreach ($vcwe_db_replace_data[$db_read_table] as $hole => $value) {
						if (array_key_exists($hole, $db_result_array)) {
							$db_result_array[$hole][0] = $value;
						}
					}
				}
				return $db_result_array;
			} else return false;
		}
	}

	// создание резервной копии таблицы текущей БД
	public static function db_backup($table, $type = "INSERT")
	{
		// возвращаемый SQL-код
		$sql_code = "";
		
		// получение данных таблицы
		$db_struct = db_work("SHOW CREATE TABLE `".$table."`");
		$db_fields = db_work("SHOW FIELDS FROM `".$table."`");
		$db_res = db_work("SELECT * FROM `".$table."`");
		
		// проверка
		if (!isset($db_struct['Create Table'][0])) return "";
		if (!isset($db_fields['Field'][0])) return "";
		
		// определение первого поля таблицы
		$first_field = $db_fields['Field'][0];
		
		if ($type == "INSERT") {
			// определение AUTO_INCREMENT
			if (isset($db_fields['Extra'])) {
				$k = array_search("auto_increment", $db_fields['Extra']);
				if ($k !== false) {
					$ai_field = $db_fields['Field'][$k]; // имя поля, содержащего AUTO_INCREMENT
					if ($db_ai = db_work("SELECT MAX(`".$ai_field."`) as `ai` FROM `".$table."`")) {
						$ai_value = $db_ai['ai'][0] + 1; // значение AUTO_INCREMENT
					}
				}
			}
			
			// код структуры таблицы
			$sql_code .= "DROP TABLE IF EXISTS `".$table."`;\r\n";
			$sql_code .= str_replace("\n", "\r\n", $db_struct['Create Table'][0]);
			if (isset($ai_value)) {
				$sql_code .= " AUTO_INCREMENT=".$ai_value;
			}
			$sql_code .= ";\r\n\r\n";
			
			// вставка данных
			$quan_fields = count($db_res);// кол-во полей
			if ($db_res[$first_field]) {
				foreach ($db_res[$first_field] as $key => $value) {
					$sql_code .= "INSERT INTO `".$table."` VALUES (";
					$i = 0;
					foreach ($db_res as $field => $arr_val) {
						// вставка значения
						if (is_numeric($arr_val[$key])) {
							$sql_code .= $arr_val[$key];
						} else {
							$sql_code .= "'".str_replace("'", "''", $arr_val[$key])."'";
						}
						// вставка разделителя
						$i++;
						if ($i != $quan_fields) $sql_code .= ", ";
					}
					$sql_code .= ");\r\n";
				}
				$sql_code .= "\r\n\r\n";
			}
		}
		
		return $sql_code;
	}
}
?>