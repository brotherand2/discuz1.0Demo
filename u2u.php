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

//消息列表
require "./header.php";

loadtemplates("css,u2u_header,u2u_footer,u2u_message");
eval("\$css = \"".template("css")."\";");
eval("\$u2uheader = \"".template("u2u_header")."\";");
eval("\$u2ufooter = \"".template("u2u_footer")."\";");

if(!$cdbuser) {
	u2umsg("您必须先登录或注册才能使用短消息功能。");
}

if(!$action)
{

	loadtemplates("u2u_row,u2u");
	if($page) {
		$start_limit = ($page - 1) * $tpp;
	} else {
		$start_limit = 0;
		$page = 1;
	}

	$query = $db->query("SELECT COUNT(*) FROM $table_u2u WHERE (msgfrom='$cdbuser' AND folder='outbox') OR (msgto='$cdbuser' AND folder='inbox')");
	$u2uusednum = $db->result($query, 0);
	if(!$folder || $folder == "inbox")
	{
		$folder = "inbox";
		$inboxoutbox = "收件箱";
		$tofrom = "发信人";
		$query = $db->query("SELECT COUNT(*) FROM $table_u2u WHERE msgto='$cdbuser' AND folder='$folder'");
		$u2unum = $db->result($query, 0);
		$query = $db->query("SELECT * FROM $table_u2u WHERE msgto='$cdbuser' AND folder='$folder' ORDER BY dateline DESC LIMIT $start_limit, $tpp");
	}
	else {
		$folder = "outbox";
		$inboxoutbox = "发件箱";
        $query = $db->query("SELECT COUNT(*) FROM $table_u2u WHERE msgfrom='$cdbuser' AND folder='$folder'");
        $u2unum = $db->result($query, 0);
        $query = $db->query("SELECT * FROM $table_u2u WHERE msgfrom='$cdbuser' AND folder='$folder' ORDER BY dateline DESC LIMIT $start_limit, $tpp");
    }
		$tofrom = "收信人";

	$mpurl = "u2u.php?folder=$folder";
	$multipage = multi($u2unum, $tpp, $page, $mpurl);
	$multipage .= " &nbsp; [信箱容量 $maxu2unum / 已用 $u2uusednum]";

	while($message = $db->fetch_array($query)) {
		$senton = gmdate("$dateformat $timeformat",$message[dateline] + ($timeoffset * 3600));

		if($message[subject] == "") {
			$message[subject] = "&lt;无标题&gt;";
		}
		if ($folder == "outbox") {
			$message[msgfrom]=$message[msgto];
		}
		
		if($message['new']) {
			$message[subject] = "<b>$message[subject]</b>";
		}

		eval("\$messages .= \"".template("u2u_row")."\";");
	}
	eval("\$u2u = \"".template("u2u")."\";");
	echo $u2u;

}
elseif($action == "send")
{

	if(!$u2usubmit)
	{

		$query = $db->query("SELECT buddyname FROM $table_buddys WHERE username='$cdbuser'");
		if($db->num_rows($query)) {
			$msgtob = "<tr>";
			$listed_friends = 0;
			while($buddys = $db->fetch_array($query)) {
				$msgtob .= "<td width=\"33%\"><input type=\"checkbox\" name=\"msgtob[]\" value=\"$buddys[buddyname]\"> $buddys[buddyname]</td>\n";
				if($listed_friend == 2) {
					$msgtob .= "</tr><tr>";
					$listed_friend = 0;
				} else {
					$listed_friend++;
				}
			}
			eval("\$buddylist = \"".template("u2u_send_buddylist")."\";");
		}
		if($u2uid) {
			$query = $db->query("SELECT * FROM $table_u2u WHERE u2uid='$u2uid' AND msgto='$cdbuser'");
			$u2u = $db->fetch_array($query);

			$u2u[subject] = $message = str_replace("回复：", "",$u2u[subject]);
			$u2u[subject] = $message = str_replace("转发：", "",$u2u[subject]);
			$username = $u2u[msgfrom];

			if($do == "reply") {
				$subject = "回复：$u2u[subject]";
				$u2u[message] = trim(preg_replace("/(\[quote])(.*)(\[\/quote])/siU", "", $u2u[message]));
				$message = "[quote]$u2u[message][/quote]";
				$touser = $u2u[msgfrom];
			}
			if($do == "forward") {
				$subject = "转发：$u2u[subject]";
				$message = "[quote]$u2u[message][/quote]";
				$touser = "$u2u[msgfrom]";
			}
		}
		$touser = stripslashes($username);//之前谁发给我$u2u[msgfrom],现发回给给谁
		eval("\$u2usend = \"".template("u2u_send")."\";");
		echo $u2usend;

	}
	else
	    {

		if(!$msgtoa && ! $msgtob) {
			u2umsg("您没有填写短消息收件人，请返回修改。");
		}

		if(!trim($subject) && !trim($message)) {
			u2umsg("您标题和内容都未填写，请返回并至少填写其中一项。");
		}
		$users = $or = "";
		$num = 0;
		if($msgtoa) {
			$users = "username='$msgtoa'";
			$or = " OR ";
			$num++;
		}
		for($i = 0; $i < count($msgtob); $i++) {
			$users .= $or."username='".$msgtob[$i]."'";
			$or = " OR ";
			$num++;
		}

		$query = $db->query("SELECT username FROM $table_members WHERE $users");
		$numusers = $db->num_rows($query);
		if($numusers != $num) {
			$nullusers = $num - $numusers;
			u2umsg("收件人中 $nullusers 人重复或不存在，请返回修改后发送。", "javascript: history.go(-1);");
		}

		$ignorenum = 0;
		$queryignore = $db->query("SELECT ignoreu2u FROM $table_members WHERE $users");
		while($list = $db->fetch_array($queryignore)) {
			if(eregi("$cdbuser"."(,|$)", $list[ignoreu2u])) {
				$ignorenum++;
			}
		}
		if($ignorenum) {
			u2umsg("对不起，收件人中 $ignorenum 人设定拒绝接受您的短消息，发送不成功。");
		}

		$subject = cdbhtmlspecialchars($subject);

		for($i = 0; $i < $numusers; $i++) {
			$msgto = addslashes($db->result($query, $i));
			$db->query("INSERT INTO $table_u2u (u2uid, msgto, msgfrom, folder, new, subject, dateline, message)
				VALUES('', '$msgto', '$cdbuser', 'inbox', '1', '$subject', '$timestamp', '$message')");
		}
		$db->query("UPDATE $table_members SET newu2u='1' WHERE $users");
		$db->query("DELETE FROM $table_sessions WHERE $users");
		//$db->query("UPDATE $table_sessions SET sessionvars=REPLACE(sessionvars, 's:6:\"newu2u\";i:0;', 's:6:\"newu2u\";i:1;') WHERE $users");

		if($saveoutbox) {
			if($msgtob) {
				$msgtoa = "好友群发";
			}
			$db->query("INSERT INTO $table_u2u (u2uid, msgto, msgfrom, folder, new, subject, dateline, message)
				VALUES('', '$msgtoa', '$cdbuser', 'outbox', '1', '$subject', '$timestamp', '$message')");
		}
		u2umsg("短消息发送成功，现在将转入消息列表。", "u2u.php");
	}

}
elseif($action == "delete")
{

	if($folder == "outbox") {
		$msg_field = "msgfrom";
	} else {
		$msg_field = "msgto";
	}
	if(!$u2uid) {
		$query = $db->query("SELECT * FROM $table_u2u WHERE ".$msg_field."='$cdbuser' AND folder='$folder' ORDER BY dateline DESC");
		while($u2u = $db->fetch_array($query)) {
			$delete = "delete$u2u[u2uid]";
			$delete = "${$delete}";
			$db->query("DELETE FROM $table_u2u WHERE ".$msg_field."='$cdbuser' AND u2uid='$delete'");
		}
	} else {
		$db->query("DELETE FROM $table_u2u WHERE ".$msg_field."='$cdbuser' AND u2uid='$u2uid'");
	}
	if($folder=="outbox") {
		u2umsg("发件箱已成功更新，现在将转入消息列表。", "u2u.php?folder=outbox");
	} else {
		u2umsg("收件箱已成功更新，现在将转入消息列表。", "u2u.php");
	}

}
elseif($action == "ignore")
{

	$query = $db->query("SELECT ignoreu2u FROM $table_members WHERE username='$cdbuser'");
	$mem = $db->fetch_array($query);
	eval("\$u2uignore = \"".template("u2u_ignore")."\";");
	echo $u2uignore;

}
elseif($action == "ignoresubmit")
{

	$db->query("UPDATE $table_members SET ignoreu2u='$ignorelist' WHERE username='$cdbuser'");
	u2umsg("忽略列表已成功更新，现在将转入消息列表。", "u2u.php");

}
elseif($action == "view")
{

	$codecount = 0;
	$query = $db->query("SELECT COUNT(*) FROM $table_u2u WHERE (msgfrom='$cdbuser' AND folder='outbox') OR (msgto='$cdbuser' AND folder='inbox')");
	$u2unum = $db->result($query, 0);

	if($u2unum > $maxu2unum) {
		u2umsg("您的信箱已满，在阅读短消息前必须删除一些不用的信息。", "u2u.php");
	} else {
		$query = $db->query("SELECT * FROM $table_u2u WHERE u2uid='$u2uid' AND (msgto='$cdbuser' OR msgfrom='$cdbuser')");
		$u2u = $db->fetch_array($query);
		if($u2u) {
			if($u2u["new"]) {
				$db->query("UPDATE $table_u2u SET new='0' WHERE u2uid='$u2uid'");
			}
			$u2u[subject] = censor($u2u[subject]);
			$dateline = gmdate("$dateformat $timeformat", $u2u[dateline] + ($timeoffset * 3600));
			$u2u[subject] = "主题：$u2u[subject]";
			$thisbg = $altbg1;
			$u2u[message] = postify($u2u[message], 0, 0, 0, 1, 0, 1, 1);
			if($u2u[msgfrom] != $cdbuser)
			{
				eval("\$refwdlinks = \"".template("u2u_view_refwdlinks")."\";");
			}
			eval("\$view = \"".template("u2u_view")."\";");
			echo $view;
		} else {
			u2umsg("对不起，您无权查看此条短消息。");
		}
	}

}

cdb_output();

function u2umsg($message, $url_forward = "") {
	global $bordercolor, $tablewidth, $borderwidth, $tablespace, $altbg1, $css, $charset, $version, $bbname, $u2uheader, $u2ufooter, $sid;
	if($url_forward) {
		$url_redirect = "<meta http-equiv=\"refresh\" content=\"2;url=$url_forward\">";
		eval("\$u2uheader = \"".template("u2u_header")."\";");
	}
	eval("\$msg = \"".template("u2u_message")."\";");
	echo $msg;
	cdbexit();
}
?>