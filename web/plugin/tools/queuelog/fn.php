<?php if(!(defined('_SECURE_'))){die('Intruder alert');}; ?>
<?php

function queuelog_get($line_per_page='', $limit='') {
	global $core_config;
	$ret = array();
	if ($core_config['user']['status'] != 2) {
		$user_query = "AND uid='".$core_config['user']['uid']."'";
	}
	if ($line_per_page) {
		$line_per_page_query = "LIMIT $line_per_page";
	}
	if ($limit) {
		$limit_query = "OFFSET $limit";
	}
	$db_query = "SELECT * FROM "._DB_PREF_."_tblSMSOutgoing_queue WHERE flag='0' ".$user_query." ORDER BY id ".$line_per_page_query." ".$limit_query;
	$db_result = dba_query($db_query);
	while ($db_row = dba_fetch_array($db_result)) {
		$ret[] = $db_row;
	}
	return $ret;
}

function queuelog_countall() {
	global $core_config;
	$ret = 0;
	if ($core_config['user']['status'] != 2) {
		$user_query = "AND uid='".$core_config['user']['uid']."'";
	}
	$db_query = "SELECT count(*) AS count FROM "._DB_PREF_."_tblSMSOutgoing_queue WHERE flag='0' ".$user_query;
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result)) {
		$ret = $db_row['count'];
	}
	return $ret;
}

?>