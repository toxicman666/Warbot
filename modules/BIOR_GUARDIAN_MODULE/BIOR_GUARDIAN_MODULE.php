<?php
	$MODULE_NAME = "BIOR_GUARDIAN_MODULE";

	//Bio Regrowth module
	Event::register($MODULE_NAME, "leavePriv", "bior_left_chat.php", "bior", "Remove player who leaves chat from bior list if he was on it");
	Event::register($MODULE_NAME, "joinPriv", "bior_joined_chat.php", "bior", "Add player to bior list when he joins chat if he should be on it (Keep,Adv,Enf,Eng)");
	Event::register($MODULE_NAME, "2sec", "bior_check.php", "bior", "Timer check for bior list");
	
	Command::register($MODULE_NAME, "", "bior_order.php", "bior", "leader", "Show Bio Regrowth Order");
	Command::register($MODULE_NAME, "", "cast_bior.php", "b", "all", "Show Bio Regrowth Cast");
	
	Setting::add($MODULE_NAME, "bior_max", "Max Persons that are shown on BioR list", "edit", "number", "10", "10;15;20;25;30", '', "mod");

	//Helpfiles
	Help::register($MODULE_NAME, "bior", "bior.txt", "all", "Bio Regrowth Macro and List");
	Help::register($MODULE_NAME, "bior_max", "bior_max.txt", "mod", "Set the max numbers of players on the Bio Regrowth List");
	
	//Guardian module
	Event::register($MODULE_NAME, "leavePriv", "guardian_left_chat.php", "guard", "Remove player who leaves chat from guardian list if he was on it");
	Event::register($MODULE_NAME, "joinPriv", "guardian_joined_chat.php", "guard", "Add player to guardian list when he joins chat if he should be on it (Soldier)");
	Event::register($MODULE_NAME, "2sec", "guard_check.php", "guard", "Timer check for guardian list");
	
	Command::register($MODULE_NAME, "", "guard_order.php", "guard", "leader", "Show Guardian Order");
	Command::register($MODULE_NAME, "", "cast_guard.php", "g", "all", "Show Guardian Cast");
	
	Setting::add($MODULE_NAME, "guard_max", "Max Persons that are shown on Guard list", "edit", "number", "10", "10;15;20;25;30", '', "mod");

	//Helpfiles
	Help::register($MODULE_NAME, "guard", "guard.txt", "all", "Guardian Macro and List");
	Help::register($MODULE_NAME, "guard_max", "guard_max.txt", "mod", "Set the max numbers of players on the Guardian List");
?>