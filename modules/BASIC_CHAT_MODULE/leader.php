<?php
   /*
   ** Author: Derroylo (RK2)
   ** Description: Sets or clears the raidleader
   ** Version: 1.0
   **
   ** Developed for: Budabot(http://sourceforge.net/projects/budabot)
   **
   ** Date(created): 17.02.2006
   ** Date(last modified): 02.02.2007
   ** 
   ** Copyright (C) 2006, 2007 Carsten Lohmann
   **
   ** Licence Infos: 
   ** This file is part of Budabot.
   **
   ** Budabot is free software; you can redistribute it and/or modify
   ** it under the terms of the GNU General Public License as published by
   ** the Free Software Foundation; either version 2 of the License, or
   ** (at your option) any later version.
   **
   ** Budabot is distributed in the hope that it will be useful,
   ** but WITHOUT ANY WARRANTY; without even the implied warranty of
   ** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   ** GNU General Public License for more details.
   **
   ** You should have received a copy of the GNU General Public License
   ** along with Budabot; if not, write to the Free Software
   ** Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
   */

$chatBot->settings["leaderecho"] = 1;
//if ($chatBot->settings["leaderecho"] == 1) {
	$status = "<green>Enabled<end>";
	$cmd = "off";
//} else {
//	$status = "<red>Disabled<end>";
//	$cmd = "on";
//}

if (preg_match("/^leader$/i", $message)) {
  	if ($chatBot->data["leader"] == $sender) {
		unset($chatBot->data["leader"]);
	  	$msg = "Leader cleared.";
	} else if ($chatBot->data["leader"] != "") {
		if ($chatBot->admins[$sender]["level"] >= $chatBot->admins[$chatBot->data["leader"]]["level"]){
  			$chatBot->data["leader"] = $sender;
		  	$msg = "{$sender} is now Leader. Leader echo is currently {$status}. You can change it with <symbol>leaderecho {$cmd}";
		} else {
			$msg = "You can't take leader from <highlight>{$chatBot->data["leader"]}<end>.";
		}
	} else {
		$chatBot->data["leader"] = $sender;
	  	$msg = "{$sender} is now Leader. Leader echo is currently {$status}. You can change it with <symbol>leaderecho {$cmd}";
	}
  	$chatBot->send($msg, 'priv');

} else {
	$syntax_error = true;
}

?>