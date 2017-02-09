<?php
/**
 * ��������: vcDatabaseWork 2.0
 * ��������: ����� ��� ������ � ��
 * �����: ����� ����� aka VARCONER
 * �����: 14.11.2009
 * ������ 1.7: 15.12.2014
 * ������ 1.8: 04.02.2015
 * ������ 2.0: 18.11.2016
 * ��������: romarybin@yandex.ru
 * ������ �������:
 *	db_work()
 *	db_backup()
 */

$vcfunc_file_version = 2000000;
if (!isset($vcfunc_version) || $vcfunc_version<$vcfunc_file_version) $vcfunc_version = $vcfunc_file_version;
 
class vcDatabaseWork {
	// ������� ��������� �������� ����
	public static function db_work($db_query, $db_alt_connect_settings = array(), $allow_replace_data = true)
	{
		// $db_query - ������
		// $db_alt_connect_settings - ������ �������������� �������� ����������� � ��
		// $allow_replace_data - ���� ������������� ������� ����������� ������ � ���������� �������
		
		// $db_connection - ID ��������� ����������� � ��
		// $db_log - ��� ��
		// $db_errors - ��� ������ ��
		// $vcwe_alt_db - ���������� ������ �������������� �������� ����������� � ��
		// $vcwe_db_replace_data - ���������� ������ �������������� ����������� �������� � ��������� ������� �� � ������� array(������� => array(���� => ��������))
		// $vcwe_allow_replace_data - ���������� ���� ������������� ������� ����������� ������ � ���������� �������

		// $db_read - ���� true, ���������� ������ �����������
		// $db_result - ID ������� ��
		// $db_result_len - ������ ������� ���������� �������
		// $db_result_array - ������������ ������ �����������
		
		// Verify
		if (!defined('VCWE_DB_TYPE')) return false;
		if (!defined('VCWE_DB_HOST')) return false;
		if (!defined('VCWE_DB_LOGIN')) return false;
		if (!defined('VCWE_DB_PASSWORD')) return false;
		if (!defined('VCWE_DB_ENCODE')) return false;
		if (!defined('VCWE_DB_NAME')) return false;
		
		global $db_connection, $db_log, $db_errors, $vcwe_alt_db, $vcwe_db_replace_data, $vcwe_allow_replace_data;
		
		// ����������� ����������� ������� �������� � ���������� �������� �������������� �������� ��
		$db_alt_connect_settings = array_merge($vcwe_alt_db, $db_alt_connect_settings);
		// ����� ��������������� ��� �������� ������
		$db_alt_mode = $db_alt_connect_settings?true:false;
		// �������� ������ ���������� �����������
		if ($allow_replace_data && !$vcwe_allow_replace_data) $allow_replace_data = false;
		
		// ����� �������� ����������� � ��
		$db_type = isset($db_alt_connect_settings['type'])?$db_alt_connect_settings['type']:VCWE_DB_TYPE;
		$db_host = isset($db_alt_connect_settings['host'])?$db_alt_connect_settings['host']:VCWE_DB_HOST;
		$db_login = isset($db_alt_connect_settings['login'])?$db_alt_connect_settings['login']:VCWE_DB_LOGIN;
		$db_password = isset($db_alt_connect_settings['password'])?$db_alt_connect_settings['password']:VCWE_DB_PASSWORD;
		$db_encode = isset($db_alt_connect_settings['encode'])?$db_alt_connect_settings['encode']:VCWE_DB_ENCODE;
		$db_name = isset($db_alt_connect_settings['name'])?$db_alt_connect_settings['name']:VCWE_DB_NAME;
		
		// ��� ���� MySQL
		if ($db_type == 'mysql') {
			// ����������� ���� �������
			$db_read = false;
			$db_read_table = "";
			if (strpos($db_query, "SELECT")!==false) {
				$db_read = true;
				// ����������� �������� �������-��������� ������
				if ($allow_replace_data && preg_match("/FROM `([a-z]+)`/", $db_query, $matches)) {
					if (isset($matches[1])) $db_read_table = $matches[1];
				}
			} elseif (substr($db_query, 0, 4)=="SHOW") {
				$db_read = true;
			}
			if (!$db_connection || $db_alt_mode) {
				// � ������ ������������� ��������������� �����������, ��������� ��������
				if ($db_connection && $db_alt_mode) {
					@mysql_close($db_connection);
					$db_connection = false;
				}
				// ���������� � ����
				$temp = @mysql_connect($db_host, $db_login, $db_password) or die("Error: ".mysql_error());
				if ($db_alt_mode) {
					$db_alt_connection = $temp;
				} else {
					$db_connection = $temp;
				}
				// ����� ��
				if ($db_encode) {
					@mysql_query("/*!40101 SET NAMES '".$db_encode."'".(define('VCWE_DB_COLLATION')?" COLLATE '".VCWE_DB_COLLATION."'":"")." */", $db_alt_mode?$db_alt_connection:$db_connection) or die("Error: ".mysql_error()); // ������� �������� ���������
				}
				@mysql_select_db($db_name, $db_alt_mode?$db_alt_connection:$db_connection) or die("Error: ".mysql_error());
			}
			// ������
			$db_result = @mysql_unbuffered_query($db_query, $db_alt_mode?$db_alt_connection:$db_connection);
			$db_log .= "<b>MySQL query:</b> ".$db_query."<br>";
			$error = $db_result === false ? mysql_errno().": ".mysql_error() : "";
			// ��������� ���������� ������� � ��
			if ($db_result && $db_read) {
				$i = 0;
				// �������� ������� ����������� ������� � �������:
				// $db_result_array ([��� ����] => array([������ ������] => [���������� ������ ���������������� ����]))
				while ($row = @mysql_fetch_array($db_result, MYSQL_ASSOC)) {
					foreach ($row as $name => $value) {
						$db_result_array[$name][$i] = $value;
					}
					$i++;
				}
			}
			if ($db_alt_mode) @mysql_close($db_alt_connection);
			// ������ ������ � ���
			if ($error) {
				$db_log .= $error."<br>";
				$db_errors .= "<b>Error query:</b> ".$db_query."<br><b>Error description:</b> ".$error."<br><br>";
				return false;
			}
			// ���� ������� ������ �� ��, ���������� ID ������� ��� false � ������ ������
			if (!$db_read) return $db_result;
			// ���������� ������ �� �� ���� ����
			if (isset($db_result_array)) {
				// ���� �������� ������� �������� � ���� ���� ��� ������ ������� �� ������� �����������
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

	// �������� ��������� ����� ������� ������� ��
	public static function db_backup($table, $type = "INSERT")
	{
		// ������������ SQL-���
		$sql_code = "";
		
		// ��������� ������ �������
		$db_struct = db_work("SHOW CREATE TABLE `".$table."`");
		$db_fields = db_work("SHOW FIELDS FROM `".$table."`");
		$db_res = db_work("SELECT * FROM `".$table."`");
		
		// ��������
		if (!isset($db_struct['Create Table'][0])) return "";
		if (!isset($db_fields['Field'][0])) return "";
		
		// ����������� ������� ���� �������
		$first_field = $db_fields['Field'][0];
		
		if ($type == "INSERT") {
			// ����������� AUTO_INCREMENT
			if (isset($db_fields['Extra'])) {
				$k = array_search("auto_increment", $db_fields['Extra']);
				if ($k !== false) {
					$ai_field = $db_fields['Field'][$k]; // ��� ����, ����������� AUTO_INCREMENT
					if ($db_ai = db_work("SELECT MAX(`".$ai_field."`) as `ai` FROM `".$table."`")) {
						$ai_value = $db_ai['ai'][0] + 1; // �������� AUTO_INCREMENT
					}
				}
			}
			
			// ��� ��������� �������
			$sql_code .= "DROP TABLE IF EXISTS `".$table."`;\r\n";
			$sql_code .= str_replace("\n", "\r\n", $db_struct['Create Table'][0]);
			if (isset($ai_value)) {
				$sql_code .= " AUTO_INCREMENT=".$ai_value;
			}
			$sql_code .= ";\r\n\r\n";
			
			// ������� ������
			$quan_fields = count($db_res);// ���-�� �����
			if ($db_res[$first_field]) {
				foreach ($db_res[$first_field] as $key => $value) {
					$sql_code .= "INSERT INTO `".$table."` VALUES (";
					$i = 0;
					foreach ($db_res as $field => $arr_val) {
						// ������� ��������
						if (is_numeric($arr_val[$key])) {
							$sql_code .= $arr_val[$key];
						} else {
							$sql_code .= "'".str_replace("'", "''", $arr_val[$key])."'";
						}
						// ������� �����������
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