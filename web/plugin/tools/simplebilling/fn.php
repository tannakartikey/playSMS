<?php if(!(defined('_SECURE_'))){die('Intruder alert');}; ?>
<?php
function simplebilling_hook_billing_post($smslog_id,$rate,$credit,$count,$charge) {
	global $datetime_now;
	$ok = false;
	logger_print("saving smslog_id:".$smslog_id." rate:".$rate." credit:".$credit." count:".$count." charge:".$charge, 2, "simplebilling post");
	$db_query = "INSERT INTO "._DB_PREF_."_tblBilling (post_datetime,smslog_id,rate,credit,count,charge,status) VALUES ('$datetime_now','$smslog_id','$rate','$credit','$count','$charge','0')";
	if ($id = @dba_insert_id($db_query)) {
		logger_print("saved smslog_id:".$smslog_id." id:".$id, 2, "simplebilling post");
		$ok = true;
	}
	return $ok;
}

function simplebilling_hook_billing_rollback($smslog_id) {
	$ok = false;
	logger_print("checking smslog_id:".$smslog_id, 2, "simplebilling rollback");
	$db_query = "SELECT id FROM "._DB_PREF_."_tblBilling WHERE smslog_id='$smslog_id'";
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result)) {
		$id = $db_row['id'];
		logger_print("saving smslog_id:".$smslog_id." id:".$id, 2, "simplebilling rollback");
		$db_query = "UPDATE "._DB_PREF_."_tblBilling SET status='2' WHERE id='$id'";
		if ($db_result = dba_affected_rows($db_query)) {
			logger_print("saved smslog_id:".$smslog_id, 2, "simplebilling rollback");
			$ok = true;
		}
	}
	return $ok;
}

function simplebilling_hook_billing_finalize($smslog_id) {
	$ok = false;
	logger_print("saving smslog_id:".$smslog_id, 2, "simplebilling finalize");
	$db_query = "UPDATE "._DB_PREF_."_tblBilling SET status='1' WHERE smslog_id='$smslog_id'";
	if ($db_result = dba_affected_rows($db_query)) {
		logger_print("saved smslog_id:".$smslog_id, 2, "simplebilling finalize");
		$ok = true;
	}
	return $ok;
}

function simplebilling_hook_setsmsdeliverystatus($smslog_id,$uid,$p_status) {
	//logger_print("checking smslog_id:".$smslog_id, 2, "simplebilling setsmsdeliverystatus");
	if (($p_status == 1) || ($p_status == 3)) {
		$db_query = "SELECT id FROM "._DB_PREF_."_tblBilling WHERE status='0' AND smslog_id='$smslog_id'";
		$db_result = dba_query($db_query);
		if ($db_row = dba_fetch_array($db_result)) {
			billing_finalize($smslog_id);
		}
	}
}

function simplebilling_hook_billing_getdata($smslog_id) {
	$ret = array();
	logger_print("smslog_id:".$smslog_id, 2, "simplebilling getdata");
	$db_query = "SELECT id,rate,credit,count,charge,status FROM "._DB_PREF_."_tblBilling WHERE smslog_id='$smslog_id'";
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result)) {
		$id = $db_row['id'];
		$post_datetime = $db_row['post_datetime'];
		$rate = $db_row['rate'];
		$credit = $db_row['credit'];
		$count = $db_row['count'];
		$charge = $db_row['charge'];
		$status = $db_row['status'];
		$ret = array('id' => $id, 'smslog_id' => $smslog_id, 'post_datetime' => $post_datetime, 'status' => $status, 'rate' => $rate, 'credit' => $credit, 'count' => $count, 'charge' => $charge);
	}
	return $ret;
}

?>