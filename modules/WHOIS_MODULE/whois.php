<?php


if (preg_match("/^whois (.+)$/i", $message, $arr)) {
    $uid = $chatBot->get_uid($arr[1]);
    $name = ucfirst(strtolower($arr[1]));
    if ($uid) {
        $whois = Player::get_by_name($arr[1]);
        if ($whois === null) {
        	$msg = "Could not find character info for {$name}.";
        } else {
	        $msg = Player::get_info($whois);

	        $list = "<header> :::::: Detailed info for {$name} :::::: <end>\n\n";
	        $list .= "Name: <highlight>{$whois->firstname} \"{$name}\" {$whois->lastname}<end>\n";
			if ($whois->guild) {
				$list .= "Guild: <highlight>{$whois->guild} ({$whois->guild_id})<end>\n";
				$list .= "Guild Rank: <highlight>{$whois->guild_rank} ({$whois->guild_rank_id})<end>\n";
			}
			$list .= "Breed: <highlight>{$whois->breed}<end>\n";
			$list .= "Gender: <highlight>{$whois->gender}<end>\n";
			$list .= "Profession: <highlight>{$whois->profession} ({$whois->prof_title})<end>\n";
			$list .= "Level: <highlight>{$whois->level}<end>\n";
			$list .= "AI Level: <highlight>{$whois->ai_level} ({$whois->ai_rank})<end>\n";
			$list .= "Faction: <highlight>{$whois->faction}<end>\n";
			$list .= "Character ID: <highlight>{$whois->charid}<end>\n\n";
			
			$list .= "Source: $whois->source\n\n";
			
			$sql = "SELECT * FROM name_history WHERE charid = '{$uid}' AND dimension = <dim> ORDER BY dt DESC";
			$db->query($sql);
			$data = $db->fObject('all');

			$list .= "<pagebreak><header> :::::: Name History :::::: <end>\n\n";
			if (count($data) > 0) {
				forEach ($data as $row) {
					$list .= "<green>{$row->name}<end> " . gmdate("M j, Y, G:i", $row->dt) . "\n";
				}
			} else {
				$list .= "No name history available\n";
			}
			
			$list .= "\n<pagebreak><header> :::::: Character Options :::::: <end>\n\n";
			
	        $list .= "<a href='chatcmd:///tell <myname> history $name'>Check $name's History</a>\n";
	        $list .= "<a href='chatcmd:///tell <myname> is $name'>Check $name's online status</a>\n";
	        if ($whois->guild) {
		        $list .= "<a href='chatcmd:///tell <myname> whoisorg $whois->guild_id'>Show info about {$whois->guild}</a>\n";
				$list .= "<a href='chatcmd:///tell <myname> orglist $whois->guild_id'>Orglist for {$whois->guild}</a>\n";
			}
	        $list .= "<a href='chatcmd:///cc addbuddy $name'>Add to buddylist</a>\n";
	        $list .= "<a href='chatcmd:///cc rembuddy $name'>Remove from buddylist</a>";
			
	        $msg .= " :: " . Text::make_blob("More info", $list);

			$altInfo = Alts::get_alt_info($name);
			if (count($altInfo->alts) > 0) {
				$msg .= " :: " . $altInfo->get_alts_blob();
			}
	    }
    } else {
        $msg = "Player <highlight>{$name}<end> does not exist.";
	}

    $chatBot->send($msg, $sendto);
} else if (preg_match("/^whoisall (.+)$/i", $message, $arr)) {
    $name = ucfirst(strtolower($arr[1]));
    for ($i = 1; $i <= 2; $i ++) {
        if ($i == 1) {
            $server = "Atlantean";
        } else if ($i == 2) {
            $server = "Rimor";
		}

        $whois = Player::lookup($name, $i);
        if ($whois !== null) {
            $msg = Player::get_info($whois);

			$list = "<header> :::::: Detailed info for {$name} :::::: <end>\n\n";
	        $list .= "Name: <highlight>{$whois->firstname} \"{$name}\" {$whois->lastname}<end>\n";
			if ($whois->guild) {
				$list .= "Guild: <highlight>{$whois->guild} ({$whois->guild_id})<end>\n";
				$list .= "Guild Rank: <highlight>{$whois->guild_rank} ({$whois->guild_rank_id})<end>\n";
			}
			$list .= "Breed: <highlight>{$whois->breed}<end>\n";
			$list .= "Gender: <highlight>{$whois->gender}<end>\n";
			$list .= "Profession: <highlight>{$whois->profession} ({$whois->prof_title})<end>\n";
			$list .= "Level: <highlight>{$whois->level}<end>\n";
			$list .= "AI Level: <highlight>{$whois->ai_level} ({$whois->ai_rank})<end>\n";
			$list .= "Faction: <highlight>{$whois->faction}<end>\n\n";
			
			$list .= "Source: $whois->source\n\n";

			$sql = "SELECT * FROM name_history WHERE charid = '{$uid}' AND dimension = {$i} ORDER BY dt DESC";
			$db->query($sql);
			$data = $db->fObject('all');

			$list .= "<pagebreak><header> :::::: Name History :::::: <end>\n\n";
			if (count($data) > 0) {
				forEach ($data as $row) {
					$list .= "<green>{$row->name}<end> " . gmdate("M j, Y, G:i", $row->dt) . "\n";
				}
			} else {
				$list .= "No name history available\n";
			}

			$list .= "\n<pagebreak><header> :::::: Character Options :::::: <end>\n\n";

            $list .= "<a href='chatcmd:///tell <myname> history {$name} {$i}'>Show History</a>\n";
			
            $msg .= " :: ".Text::make_blob("More info", $list);
            $msg = "<highlight>Server $server:<end> ".$msg;
        } else {
            $msg = "Server $server: Player <highlight>{$name}<end> does not exist.";
		}

        $chatBot->send($msg, $sendto);
    }
} else {
	$syntax_error = true;
}

?>
