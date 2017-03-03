<?php
//-----------------------------------------------------------------------------
//    Discuz! Board 1.0 Standard - Discuz! 中文论坛 (PHP & MySQL) 1.0 标准版
//-----------------------------------------------------------------------------
//    Copyright(C) Dai Zhikang, Crossday Studio, 2002. All rights reserved
//
//    Crossday 工作室 www.crossday.com    *Discuz! 技术支持 www.Discuz.net
//-----------------------------------------------------------------------------
//  请详细阅读 Discuz! 授权协议,查看或使用 Discuz! 的任何部分意味着完全同意
//  协议中的全部条款,请举手之劳支持国内软件事业,严禁一切违反协议的侵权行为.
//-----------------------------------------------------------------------------
// Discuz! 专注于提供高效强大的论坛解决方案,如用于商业用途,您必须购买使用授权!
//-----------------------------------------------------------------------------


require "header.php";
loadtemplates('css,buddylist_edit_buddy,buddylist_edit,buddylist_buddy_online,buddylist_buddy_offline,buddylist,buddylist_message');
eval("\$css = \"".template("css")."\";");

if(!$cdbuser) {
	blistmsg("您必须先登录或注册后才能使用短消息功能。", "");
}

if($action == "add") {

	if(!$buddy) {
		blistmsg("您还没有选择要添加进您的好友列表中的用户。");
	}
	$query = $db->query("SELECT * FROM $table_buddys WHERE username='$cdbuser' AND buddyname='$buddy'");
	if($db->fetch_array($query)) {
		blistmsg("$buddy 已经存在于您的好友列表中。");
	}
	$db->query("INSERT INTO $table_buddys VALUES ('$cdbuser', '$buddy')");
	blistmsg("$buddy 已添加进您的好友列表。", "buddy.php");

}
elseif($action == "edit") {

	if(!$editsubmit) {
		$query = $db->query("SELECT * FROM $table_buddys WHERE username='$cdbuser'");
		while($buddy = $db->fetch_array($query)) {
			eval("\$buddys .= \"".template("buddylist_edit_buddy")."\";");
		}
		eval("\$edit = \"".template("buddylist_edit")."\";");
		echo $edit;
	} else {
		if($newbuddy1) {
			$query = $db->query("SELECT b.*, m.username AS musername FROM $table_members m LEFT JOIN $table_buddys b ON b.username='$cdbuser' AND b.buddyname='$newbuddy1' WHERE m.username='$newbuddy1'");
			$newbuddy = $db->fetch_array($query);
			if(!$newbuddy[musername]) {
				blistmsg("您要添加的好友 $newbuddy1 不存在。");
			} elseif($newbuddy[buddyname]) {
				blistmsg("$newbuddy1 已经存在于您的好友列表中。");
			}
			$newbuddy[musername] = addslashes($newbuddy[musername]);
			$db->query("INSERT INTO $table_buddys VALUES ('$cdbuser', '$newbuddy[musername]')");
		}
		if($newbuddy2) {
			$query = $db->query("SELECT b.*, m.username AS musername FROM $table_members m LEFT JOIN $table_buddys b ON b.username='$cdbuser' AND b.buddyname='$newbuddy2' WHERE m.username='$newbuddy2'");
			$newbuddy = $db->fetch_array($query);
			if(!$newbuddy[musername]) {
				blistmsg("您要添加的好友 $newbuddy2 不存在。");
			} elseif($newbuddy[buddyname]) {
				blistmsg("$newbuddy2 已经存在于您的好友列表中。");
			}
			$newbuddy[musername] = addslashes($newbuddy[musername]);
			$db->query("INSERT INTO $table_buddys VALUES ('$cdbuser', '$newbuddy[musername]')");
		}

		$query = $db->query("SELECT * FROM $table_buddys WHERE username='$cdbuser'");
		while($buddy = $db->fetch_array($query)) {
			$delete = "delete$buddy[buddyname]";
			$delete = "${$delete}";

			if($delete != "") {
				$db->query("DELETE FROM $table_buddys WHERE buddyname='$delete'");
			}
		}
		blistmsg("您的好友列表已成功更新。");
	}

}
else {

	$query = $db->query("SELECT b.*, s.username AS wolusername FROM $table_buddys b LEFT JOIN $table_sessions s ON s.username=b.buddyname WHERE b.username='$cdbuser'");
	while($buddy = $db->fetch_array($query)) {
		$encodename = rawurlencode($buddy[buddyname]);
		if($buddy[wolusername]) {//如果好友在线
			eval("\$buddys[online] .= \"".template("buddylist_buddy_online")."\";");
		} else {
			eval("\$buddys[offline] .= \"".template("buddylist_buddy_offline")."\";");
		}
	}
	eval("\$buddylist = \"".template("buddylist")."\";");
	echo $buddylist;

}

cdb_output();

function blistmsg($message, $url_forward = "buddy.php") {
	global $bordercolor, $tablewidth, $borderwidth, $tablespace, $altbg1, $css, $bbname, $sid, $charset;
	if($url_forward) {
		$url_forward = url_rewriter($url_forward);
		$url_redirect = "<meta http-equiv=\"refresh\" content=\"2;url=$url_forward\">";
	}
	eval("\$blistmessage = \"".template("buddylist_message")."\";");
	echo $blistmessage;
	cdbexit();
}

?>