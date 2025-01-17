<?php

if (preg_match("/^tellrelay (.*)$/", $message, $arr)) {
	$name = ucfirst(strtolower($arr[1]));
	$uid = $chatBot->get_uid($name);
	
	if (!$uid) {
		$msg = "Player <highlight>$name<end> does not exist.";
		$chatBot->send($msg, $sendto);
		return;
	}
	
	Setting::save('relaytype', 1);  // 1 for 'tell'
	Setting::save('relaysymbol', 'Always relay');
	Setting::save('relaybot', $name);
	
	$msg = "Relay set up successfully with <highlight>$name<end>.  Please issue command '/tell $name tellrelay <myname>' if not done so already to complete the setup.";
	$chatBot->send($msg, $sendto);
} else {
	$syntax_error = false;
}

?>