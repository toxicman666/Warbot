<?php

if (preg_match("/^loadsql (.*) (.*)$/i", $message, $arr)) {
	$module = strtoupper($arr[1]);
	$name = strtolower($arr[2]);
	
	$db->beginTransaction();
	
	$msg = DB::loadSQLFile($module, $name, true);
	
	$db->Commit();
	
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = true;
}

?>