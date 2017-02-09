<?php
// определение глобальных переменных
if (!isset($db_connection)) $db_connection = false;
if (!isset($db_log)) $db_log = "";
if (!isset($db_errors)) $db_errors = "";
if (!isset($vcwe_alt_db)) $vcwe_alt_db = array();
if (!isset($vcwe_db_replace_data)) $vcwe_db_replace_data = array();
if (!isset($vcwe_allow_replace_data)) $vcwe_allow_replace_data = false;
// подключение класса vcDatabaseWork
require_once('vc_database_work.php');
// переопределение функций
function db_work($db_query, $db_alt_connect_settings = array(), $allow_replace_data = true) {
	if (is_array($db_query)) {
		foreach ($db_query as $query) {
			$response = vcDatabaseWork::db_work($query, $db_alt_connect_settings, $allow_replace_data);
		}
		return $response;
	} else {
		return vcDatabaseWork::db_work($db_query, $db_alt_connect_settings, $allow_replace_data);
	}
}
function db_backup($table, $type = "INSERT") {return vcDatabaseWork::db_backup($table, $type);}

// подключение класса vcPictureWork
require_once('vc_picture_work.php');
?>