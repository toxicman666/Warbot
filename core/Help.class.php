<?php

/*
`name` VARCHAR(30) NOT NULL
`module` VARCHAR(50) NOT NULL
`description` VARCHAR(50) NOT NULL DEFAULT ''
`file` VARCHAR(255) NOT NULL
`is_core` TINYINT NOT NULL
`access_level` INT DEFAULT 0
`verify` INT Default 1
*/

class Help {

	/**
	 * @name: register
	 * @description: Registers a help command
	 */
	public static function register($module, $command, $filename, $admin, $description) {
	  	$db = DB::get_instance();
		global $chatBot;
		
		Logger::log('debug', 'Core', "Registering $module:help($command) Helpfile:($filename)");

		$command = strtolower($command);

		//Check if the admin status exists
		if (!is_numeric($admin)) {
			if ($admin == "leader") {
				$admin = 1;
			} else if ($admin == "raidleader" || $admin == "rl") {
				$admin = 2;
			} else if ($admin == "mod" || $admin == "moderator") {
				$admin = 3;
			} else if ($admin == "admin") {
				$admin = 4;
			} else if($admin != "all" && $admin != "guild" && $admin != "guildadmin") {
				Logger::log('ERROR', 'Core', "Error in registrating the $module:help($command). Unknown Admin type: '$admin'. Admin type is set to 'all'.");
				$admin = "all";
			}
		}

		// Check if the file exists
		$actual_filename = Util::verify_filename($module . '/' . $filename);
		if ($actual_filename == '') {
			Logger::log('ERROR', 'Core', "Error in registering the File $filename for Help command $module:help($command). The file doesn't exist!");
			return;
		}

		if (isset($chatBot->existing_helps[$command])) {
			$db->exec("UPDATE hlpcfg_<myname> SET `verify` = 1, `file` = '$actual_filename', `description` = '$description' WHERE `name` = '$command'");
		} else {
			$db->exec("INSERT INTO hlpcfg_<myname> (`name`, `module`, `file`, `description`, `admin`, `verify`) VALUES ('$command', '$module', '$actual_filename', '$description', '$admin', 1)");
		}

		$db->query("SELECT * FROM hlpcfg_<myname> WHERE `name` = '$command'");
		$row = $db->fObject();
		$chatBot->helpfiles[$command]["filename"] = $actual_filename;
		$chatBot->helpfiles[$command]["admin"] = $row->admin;
		$chatBot->helpfiles[$command]["info"] = $description;
		$chatBot->helpfiles[$command]["module"] = $module;
		
		if (substr($actual_filename, 0, 7) == "./core/") {
			$chatBot->helpfiles[$command]["status"] = "enabled";

		}
	}
	
	/**
	 * @name: find
	 * @description: Find a help topic by name if it exists and if the user has permissions to see it
	 */
	public static function find($helpcmd, $char, $return_as_bloblink = true) {
		global $chatBot;
	
		$helpcmd = strtolower($helpcmd);

		if (isset($chatBot->helpfiles[$helpcmd])) {
			$filename = $chatBot->helpfiles[$helpcmd]["filename"];
			$admin = $chatBot->helpfiles[$helpcmd]["admin"];
		}

		if ($char === null) {
			$access = true;
		} else {
			$access = AccessLevel::checkAccess($char, $admin);
		}
		if ($access === true && file_exists($filename)) {
			$data = file_get_contents($filename);
			if ($return_as_bloblink) {
				$helpcmd = ucfirst($helpcmd);
				$msg = Text::make_link("Help($helpcmd)", $data);
			} else {
				$msg = $data;
			}
		} else {
			return false;
		}

		return $msg;
	}
}

?>
