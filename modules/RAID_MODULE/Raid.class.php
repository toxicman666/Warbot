<?php

class Raid {
	public static function get_current_loot_list() {
		global $chatBot;
		global $loot;
		global $raidloot;
	
		if ($chatBot->vars["raid_status"] == "") {
			if (is_array($loot)) {
				$list = "<header>::::: Loot List :::::<end>\n\n";
				forEach ($loot as $key => $item) {
					$add = Text::make_link("Add", "/tell <myname> add $key", "chatcmd");
					$rem = Text::make_link("Remove", "/tell <myname> add 0", "chatcmd");
					$added_players = count($item["users"]);
		
					$list .= "<u>Slot #<font color='#FF00AA'>$key</font></u>\n";
					if ($item["icon"] != "") {
						$list .= "<img src=rdb://{$item["icon"]}>\n";
					}

					if ($item["multiloot"] > 1) {
						$ml = " <yellow>(x".$item["multiloot"].")<end>";
					} else {
						$ml = "";
					}
					
					if ($item["linky"]) {
						$itmnm = $item["linky"];
					} else {
						$itmnm = $item["name"];
					}
		
					$list .= "Item: <orange>$itmnm<end>".$ml."\n";
					if ($item["minlvl"] != "") {
						$list .= "MinLvl set to <highlight>{$item["minlvl"]}<end>\n";
					}
									
					$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
					$list .= "Players added:";
					if (count($item["users"]) > 0) {
						forEach ($item["users"] as $key => $value) {
							$list .= " [<yellow>$key<end>]";
						}
					} else {
						$list .= " None added yet.";
					}
					
					$list .= "\n\n";
				}
				$msg = Text::make_link("Loot List", $list);
			} else {
				$msg = "No List exists yet.";
			}
		} else if ($chatBot->vars["raid_status"] != "" && $chatBot->vars["raid_loot_pts"] == 0) {
			if (is_array($raidloot)) {
				$list = "<header>::::: Raidloot List :::::<end>\n\n";
				forEach ($raidloot as $key => $item) {
					$add = Text::make_link("Add", "/tell <myname> add $key", "chatcmd");
					$rem = Text::make_link("Remove", "/tell <myname> add 0", "chatcmd");
					$added_players = count($item["users"]);
		
					$list .= "<u>Slot #$key</u>\n";
					if ($item["icon"] != "") {
						$list .= "<img src=rdb://{$item["icon"]}>\n";
					}
		
					$list .= "Item: <highlight>{$item["name"]}<end>\n";
					if ($item["minlvl"] != "") {
						$list .= "MinLvl set to <highlight>{$item["minlvl"]}<end>\n";
					}
					$list .= "<highlight>$added_players<end> Total ($add/$rem)\n";
					$list .= "Players added:";
					if (count($item["users"]) > 0) {
						forEach ($item["users"] as $key => $value) {
							$list .= " [<highlight>$key<end>]";
						}
					} else {
						$list .= " None added yet.";
					}
					
					$list .= "\n\n";
				}
				$msg = Text::make_link("Raidloot List", $list);
			} else {
				$msg = "No List exists yet.";
			}
		} else {
			$msg = "No list available!";
		}
		
		return $msg;
	}
	
	public static function add_raid_to_loot_list($raid, $category) {
		global $loot;
		$db = DB::get_instance();

		// clear current loot list
		$loot = array();
		$count = 1;

		$sql = "SELECT * FROM raid_loot WHERE raid = '$raid' AND category = '$category'";
		$db->query($sql);

		if ($db->numrows() == 0) {
			return false;
		}

		$data = $db->fObject('all');
		forEach ($data as $row) {
			$loot[$count]["name"] = $row->name;
			$loot[$count]["linky"] = Text::make_item($row->lowid, $row->highid, $row->ql, $row->name);
			$loot[$count]["icon"] = $row->imageid;
			$count++;
		}

		return true;
	}
	
	public static function find_raid_loot($raid, $category) {
		$db = DB::get_instance();

		$sql = "SELECT * FROM raid_loot WHERE raid = '$raid' AND category = '$category'";
		$db->query($sql);

		if ($db->numrows() == 0) {
			return null;
		}

		$data = $db->fObject('all');

		$blob = "<header>::::: $raid $category Loot :::::<end>\n\n\n";
		forEach ($data as $row) {
			$blob .= Text::make_item($row->lowid, $row->highid, $row->ql, "<img src=rdb://{$row->imageid}>");  // image
			$blob .= "\nItem: <highlight>{$row->name}<end>\n"; // name
			$blob .= Text::make_link("Add to Loot List", "/tell <myname> loot $row->id", "chatcmd");  // add link
			$blob .= "\n\n";
		}

		return $blob;
	}
}

?>