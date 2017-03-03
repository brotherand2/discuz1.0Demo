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

//$useraction字段在会话表,查看在线用户时用到
require "./header.php";
$tplnames = "css,header,footer";

if($action == "reg") {
	$cdbaction = "注册";
	$useraction = "注册成为新会员";

} elseif($action == "login") {
	$cdbaction = "登录";
	$useraction = "登录进入论坛";
} elseif($action == "logout") {
	$cdbaction = "退出";
	$useraction = "退出论坛登录";
} elseif($action == "online") {
	$cdbaction = "在线用户";
	$useraction = "查看在线用户";
} elseif($action == "list") {
	$cdbaction = "会员列表";
	$useraction = "查看会员列表";
} elseif($action == "viewpro") {
	$cdbaction = "查看个人资料";
	$useraction = "查看 $member 的用户资料";
}

$navigation = "&raquo; $cdbaction";
$navtitle .= " - $cdbaction";

if($loginsubmit)
{
	$referer = $referer ? $referer : "index.php";
	$errorlog = "$username\t$password\t$onlineip\t$timestamp\n";
	$password = encrypt($password);
	$query = $db->query("SELECT m.username as cdbuser, m.password as cdbpw, m.uid, m.charset, m.timeoffset, m.theme, m.tpp, m.ppp, m.credit,
		m.timeformat, m.dateformat, m.signature, m.avatar, m.lastvisit, m.newu2u, u.*, u.specifiedusers LIKE '%\t$username\t%' AS specifieduser
		FROM $table_members m LEFT JOIN $table_usergroups u ON u.specifiedusers LIKE '%\t$username\t%' OR (u.status=m.status
		AND ((u.creditshigher='0' AND u.creditslower='0' AND u.specifiedusers='') OR (m.credit>=u.creditshigher AND m.credit<u.creditslower)))
		WHERE username='$username' AND password='$password' ORDER BY specifieduser DESC");
	$member = $db->fetch_array($query);

	if(!$member[uid]) {
		@$fp = fopen("./datatemp/illegallog.php", "a");
		@flock($fp, 3);
		@fwrite($fp, $errorlog);
		@fclose($fp);
		showmessage("用户名无效或密码错误，现在将以游客身份转入原页面。", $referer);
	} else {
		$member[signature] = $member[signature] ? 1 : 0;
		$CDB_SESSION_VARS = array_merge($CDB_SESSION_VARS, $member);
		$CDB_SESSION_VARS[theme] = $member[theme] ? $member[theme] : $CDB_CACHE_VARS[settings][theme];
		$CDB_SESSION_VARS[themename] = "";

		$currtime = $timestamp + (86400 * 365);
		setcookie("cookietime", $cookie_time, $currtime, $cookiepath, $cookiedomain);
		$currtime = cookietime();
		$username = $CDB_SESSION_VARS[cdbuser];
		$CDB_SESSION_VARS[cdbuser] = addslashes($CDB_SESSION_VARS[cdbuser]);
		setcookie("_cdbuser", $CDB_SESSION_VARS[cdbuser], $currtime, $cookiepath, $cookiedomain);
		setcookie("_cdbpw", $CDB_SESSION_VARS[cdbpw], $currtime, $cookiepath, $cookiedomain);

		showmessage("欢迎您回来，{$username}。现在将转入登录前页面。", $referer);
	}
}

if($action == "reg")
{

	if(!$regstatus) {
		showmessage("对不起，目前论坛不允许注册新用户，请返回。");
	}

	$query = $db->query("SELECT censoruser, doublee, bbrules, bbrulestxt, welcommsg, welcommsgtxt FROM $table_settings");
	extract($db->fetch_array($query), EXTR_OVERWRITE);
//查找会员用户组
	$query = $db->query("SELECT allowcstatus, allowavatar FROM $table_usergroups WHERE creditshigher<=0 AND 0<creditslower");
	$groupinfo = $db->fetch_array($query);//没有积分,默认注册用户组信息,新手上路

	if(!$regsubmit)
	{

		preloader("member_reg,member_reg_avatar,member_reg_avatarlist,member_reg_changecode,member_reg_password");

		if($bbrules && !$rulesubmit) //注册时显示许可协议
		{
			$bbrulestxt = nl2br("\n$bbrulestxt\n\n");
			eval("\$page = \"".template("member_reg_rules")."\";");
			echo $page;
		}
		else {
			$themelist = "<select name=\"thememem\">\n<option value=\"\">--使用默认值--</option>";
			$query = $db->query("SELECT themename FROM $table_themes");
			while($themeinfo = $db->fetch_array($query)) {
				$themelist .= "<option value=\"$themeinfo[themename]\">$themeinfo[themename]</option>\n";
			}
			$themelist  .= "</select>";

			$dayselect = "<select name=\"day\">\n";
			$dayselect .= "<option value=\"\">&nbsp;</option>\n";
			for($num = 1; $num <= 31; $num++) {
				$dayselect .= "<option value=\"$num\">$num</option>\n";
			}
			$dayselect .= "</select>";

			if($maxsigsize) {
				$allowmaxsigsize = " ($maxsigsize 字符以内)";
			}
            //这些布尔变量是系统游客用户组值
			$bbcodeis = $allowsigbbcode ? "On" : "Off";
			$imgcodeis = $allowsigimgcode ? "On" : "Off";
			$cdb_charset == "big5" ? $bigcheck = "selected=\"selected\"" : $gbcheck = "selected=\"selected\"";
			$currdate = gmdate($timeformat);

			$dateformatorig = $dateformat;
			$dateformatorig = str_replace("n", "mm", $dateformatorig);
			$dateformatorig = str_replace("j", "dd", $dateformatorig);
			$dateformatorig = str_replace("y", "yy", $dateformatorig);
			$dateformatorig = str_replace("Y", "yyyy", $dateformatorig);

			if($groupinfo[allowcstatus]) {//自定义头衔
				eval("\$customstatus = \"".template("member_reg_cstatus")."\";");
			}

			if($groupinfo[allowavatar] == 1)
			{
				eval("\$avatarselect = \"".template("member_reg_avatarlist")."\";");
			} elseif($groupinfo[allowavatar] == 2) {
				eval("\$avatarselect = \"".template("member_reg_avatar")."\";");
			}

			if(!$emailcheck){
				eval("\$pwtd = \"".template("member_reg_password")."\";");
			}

			if($chcode){
				eval("\$changecode = \"".template("member_reg_changecode")."\";");
			}

			eval("\$page = \"".template("member_reg")."\";");
			echo $page;
		}

	}
	else {

		$referer = $referer ? $referer : "index.php";

		$email = trim($email);
		if(!$doublee && strstr($email, "@")) {
			$emailadd = "OR email='$email'";
		}

		$username = trim($username);

		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE username='$username' $emailadd");
		if($db->result($query, 0)) {
			showmessage("该用户名或 Email 地址已经被注册了，请返回重新填写。");
		}

		if(strlen($username) > 15) {
			showmessage("对不起，您的用户名超过 15 个字符，请返回输入一个较短的用户名。");
		}

		if($password != $password2) {
			showmessage("两次输入的密码不一致，请返回检查后重试。");
		}

		if(eregi(str_replace(",", "|", "^(".str_replace(" ", "", addslashes($censoruser)).")$"), $username)) {
			showmessage("用户名被系统屏蔽，请返回重新填写。");
		}

		if(preg_match("/^$|^c:\\con\\con$|[\s\t\<\>]|^游客/is", $username)) {
			showmessage("用户名空或包含敏感字符，请返回重新填写。");
		}

		if(!$emailcheck && (!$password || $password != addslashes($password))) {
			showmessage("密码空或包含非法字符，请返回重新填写。");
		}

		if(!strstr($email, "@") || $email != addslashes($email) || $email != htmlspecialchars($email)) {
			showmessage("Email 地址无效，请返回重新填写。");
		}

		if($maxsigsize && strlen($sig) > $maxsigsize) {
			showmessage("您的签名长度超过 $maxsigsize 字符的限制，请返回修改。");
		}

		if($allowavatar == 2 && $avatar)
		{
			if($maxavatarsize)
			{
				if(strstr($avatar, ","))
				{
					$avatarinfo = explode(",", $avatar);
					if(trim($avatarinfo[1]) > $maxavatarsize || trim($avatarinfo[2]) > $maxavatarsize) {
						showmessage("您设置的 Flash 头像超过了系统定义的宽 $maxavatarsize 像素，高 $maxavatarsize 像素，请返回重新填写。");
					}
				} else
				    {
					if($image_size = @getimagesize($avatar)) {
						if($image_size[0] > $maxavatarsize || $image_size[1] > $maxavatarsize) {
							showmessage("您的自定义头像超过了系统定义的宽 $maxavatarsize 象素，高 $maxavatarsize 象素，请返回重新填写。");
						}
					} else {
						showmessage("您的自定义头像无法打开，请返回确认头像链接是有效的。");
					}
				}
			}
		}
		else {
			$avatar = "";
		}

		if($emailcheck){
			$password2 = random(8);//邮件验证的密码由系统生成
			$password = encrypt($password2);
		} else {
			$password = encrypt($password);
		}

		if(!$groupinfo[allowcstatus]) {
			$cstatus = "";
		}

		if(!$tppnew) {
			$tppnew = $topicperpage;
		}

		if(!$pppnew) {
			$pppnew = $postperpage;
		}

		if(!$chcode || !$usercharset) {
			$usercharset = $cdb_charset;
		}

		$bday = "$year-$month-$day";

		if($month == "" || $day == "" || $year == "") {
			$bday = "";
		}

		$dateformatnew = str_replace("mm", "n", $dateformatnew);
		$dateformatnew = str_replace("dd", "j", $dateformatnew);
		$dateformatnew = str_replace("yyyy", "Y", $dateformatnew);
		$dateformatnew = str_replace("yy", "y", $dateformatnew);

		$avatar = cdbhtmlspecialchars($avatar);
		$locationnew = cdbhtmlspecialchars($locationnew);
		$icq = cdbhtmlspecialchars($icq);
		$yahoo = cdbhtmlspecialchars($yahoo);
		$oicq = cdbhtmlspecialchars($oicq);
		$email = cdbhtmlspecialchars($email);
		$site = cdbhtmlspecialchars($site);
		$bio = cdbhtmlspecialchars($bio);
		$bday = cdbhtmlspecialchars($bday);
		$cstatus = cdbhtmlspecialchars($cstatus);
		$timeformatnew = $timeformatnew == "24" ? "H:i" : "h:i A";

		if($welcommsg)
		{
			$welcomtitle = "欢迎您成为 $bbname 的一员！";
			if(!$welcommsgtxt) {
				$welcommsgtxt = "您好，感谢您在我们的论坛注册，成为 $bbname 的一分子，希望我们能为您提供一个和谐、融洽的交流场所。\n\n祝愿您在 $bbname 有个好心情！\n\n==============\n$bbname 管理员敬上";
			}
			$welcomtitle = addslashes($welcomtitle);
			$welcommsgtxt = addslashes($welcommsgtxt);
			$db->query("INSERT INTO $table_u2u (msgto, msgfrom, folder, new, subject, dateline, message)
				VALUES ('$username', '系统信息', 'inbox', '1', '$welcomtitle', '$timestamp','$welcommsgtxt')");
		}
		//$status = $emailcheck ? "等待验证" : "正式会员";
		$db->query("INSERT INTO $table_members (username, password, gender, status, regip, regdate, lastvisit, postnum, credit, charset, email, site, icq, oicq, yahoo, msn, location, bday, bio, avatar, signature, customstatus, tpp, ppp, theme, dateformat, timeformat, showemail, newsletter, timeoffset)
			VALUES ('$username', '$password', '$gendernew', '正式会员', '$onlineip', '$timestamp', '$timestamp', '0', '0', '$usercharset', '$email', '$site', '$icq', '$oicq', '$yahoo', '$msn', '$locationnew', '$bday', '$bio', '$avatar', '$sig', '$cstatus', '$tppnew', '$pppnew', '$thememem', '$dateformatnew', '$timeformatnew', '$showemail', '$newsletter', '$timeoffsetnew')");
		$db->query("UPDATE $table_settings SET lastmember='$username'");
		updatecache("settings");

		if($emailcheck)
		{

			sendmail($email, "[Discuz!] 您的账号已经开通",
					"您在 $bbname [ $boardurl ] 的申请已被接受，请用如下资料登录：\n\n".
					"用户名：$username\n密码：$password2\n\n您可以登录后修改此密码。\n".
					"非常感谢您对我们的信赖与支持，欢迎您光临 {$bbname}。",
				"From: $bbname <$adminemail>");
			showmessage("邮件已经发送，请用邮件中的账号信息登录。");

		}
		else {
			$query = $db->query("SELECT m.username as cdbuser, m.password as cdbpw, m.uid, m.charset, m.timeoffset, m.theme, m.tpp, m.ppp, m.credit,
				m.timeformat, m.dateformat, m.signature, m.avatar, m.lastvisit, m.newu2u, u.*, u.specifiedusers LIKE '%\t$username\t%' AS specifieduser
				FROM $table_members m LEFT JOIN $table_usergroups u ON u.specifiedusers LIKE '%\t$username\t%' OR (u.status=m.status
				AND ((u.creditshigher='0' AND u.creditslower='0' AND u.specifiedusers='') OR (m.credit>=u.creditshigher AND m.credit<u.creditslower)))
				WHERE username='$username' AND password='$password' ORDER BY specifieduser DESC");
			$member = $db->fetch_array($query);
			$member[signature] = $member[signature] ? 1 : 0;
			$CDB_SESSION_VARS = array_merge($CDB_SESSION_VARS, $member);
			$CDB_SESSION_VARS[theme] = $member[theme] ? $member[theme] : $CDB_CACHE_VARS[settings][theme];
			$CDB_SESSION_VARS[themename] = "";
			$CDB_SESSION_VARS[cdbuser] = addslashes($CDB_SESSION_VARS[cdbuser]);

			$cookietime = 2592001;
			setcookie("cookietime", "$cookietime", $timestamp + (86400 * 365), $cookiepath, $cookiedomain);
			$currtime = cookietime();
			setcookie("_cdbuser", $username, $currtime, $cookiepath, $cookiedomain);
			setcookie("_cdbpw", $password, $currtime, $cookiepath, $cookiedomain);

			showmessage("非常感谢您的注册，现在将以会员身份登录论坛。", $referer);
		}
	}

}
elseif($action == "logout")
{

	$currtime = $timestamp - (86400 * 365);
	setcookie("_cdbuser", "", $currtime, $cookiepath, $cookiedomain);
	setcookie("_cdbpw", "", $currtime, $cookiepath, $cookiedomain);
	$sessionexists = -1;

	showmessage("您已退出论坛，现在将以游客身份转入退出前页面。", $referer ? $referer : "index.php");

}
elseif($action == "login" && !$loginsubmit)
{

	preloader("member_login");

	$themelist = "<select name=\"style\">\n<option value=\"\">--使用默认值--</option>";
	$query = $db->query("SELECT themename FROM $table_themes");
	while($themeinfo = $db->fetch_array($query)) {
		$themelist .= "<option value=\"$themeinfo[name]\">$themeinfo[name]</option>\n";
	}
	$themelist  .= "</select>";

	if($cookietime == "31536000") {
		$year_checked = "checked";
	} elseif($cookietime == "86400") {
		$day_checked = "checked";
	} elseif($cookietime == "3600") {
		$hour_checked = "checked";
	} elseif($cookietime == " ") {
		$task_checked = "checked";
	} else {
		$month_checked = "checked";
	}

	eval("\$login = \"".template("member_login")."\";");
	echo $login;

}
elseif($action == "online")
{

	preloader($isadmin ? "member_online_row_admin,member_online_admin" : "member_online_row,member_online");

	$query = $db->query("SELECT s.*, f.name FROM $table_sessions s LEFT JOIN $table_forums f ON s.fid=f.fid ORDER BY time DESC");
	while($online = $db->fetch_array($query)){

		$online[time] = gmdate("$timeformat", $online[time] + ($timeoffset * 3600));
		$online[username] = $online[username] ? "<a href=\"member.php?action=viewpro&username=".rawurlencode($online[username])."\">$online[username]</a>" : "游客";
		$online[location] = "<a href=\"$online[location]\">$online[location]</a>";
		$online[forum] = $online[fid] ? "<a href=\"forumdisplay.php?fid=$online[fid]\">$online[name]</a>" : NULL;

		if($isadmin) {
			eval("\$onlinemembers .= \"".template("member_online_row_admin")."\";");
			eval("\$whosonline = \"".template("member_online_admin")."\";");
		} else {
			eval("\$onlinemembers .= \"".template("member_online_row")."\";");
			eval("\$whosonline = \"".template("member_online")."\";");
		}
	}
	echo $whosonline;

}
elseif($action == "list")
{

	if(!$memliststatus) {
		showmessage("对不起，目前不能使用会员列表功能。");
	}

	preloader("member_list_row_email,member_list_row_site,member_list_row,member_list");
    //排序条件
	if(!$order || ($order != "regdate" && $order != "username" && $order != "credit")) {
		$order = "regdate";//默认为按注册日期
	}

	$mpurl = "member.php?action=list&order=$order";
	if($desc) {
		$mpurl .= "&desc=$desc";
	}

	if($page) {
		$start_limit = ($page-1) * $memberperpage;
	}
	else {
		$start_limit = 0;
		$page = 1;
	}
	if(!$srchmem) {//查找的用户名
		$query = $db->query("SELECT COUNT(*) FROM $table_members");
	} else {
		$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE BINARY username LIKE '%$srchmem%' OR username='$srchmem'");
	}
	$num = $db->result($query,0);
	$multipage = multi($num, $memberperpage, $page, $mpurl);

	$order = $order == "username" ? "BINARY username" : $order;//binary 是按字节排序,区分大小写
	if(!$srchmem) {
		$querymem = $db->query("SELECT * FROM $table_members ORDER BY $order $desc LIMIT $start_limit, $memberperpage");
	} else {
		$querymem = $db->query("SELECT * FROM $table_members WHERE BINARY username LIKE '%$srchmem%' OR username='$srchmem' ORDER BY $order $desc LIMIT $start_limit, $memberperpage");
	}

	if($forumleaders == "010101") { // 级别头衔
		$querymem = $db->query("SELECT * FROM $table_members WHERE status = '论坛管理员' OR status = '超级版主' OR status = '版主' ORDER BY BINARY status DESC"); 
	}

	while ($member = $db->fetch_array($querymem)) {

		$member[regdate] = date("$dateformat", $member[regdate]);

		if($member[gender] == 1) {
			$member[gender] = "男";
		} elseif($member[gender] == 2) {
			$member[gender] = "女";
		} else {
			$member[gender] = "";
		}

		if($member[email] && $member[showemail]) {
			eval("\$email = \"".template("member_list_row_email")."\";");
		} else {
			$email = "&nbsp;";
		}

		$member[site] = str_replace("http://", "", $member[site]);
		$member[site] = "http://$member[site]";
		$member[site] == "http://" ? $site = "&nbsp;" : eval("\$site = \"".template("member_list_row_site")."\";");
		$member[location] = $member[location] ? $member[location] : "&nbsp;";

		$memurl = rawurlencode($member[username]);
		$member[lastvisit] = gmdate("$dateformat $timeformat", $member[lastvisit] + ($timeoffset * 3600));
		eval("\$members .= \"".template("member_list_row")."\";");
	}
	eval("\$memlist = \"".template("member_list")."\";");
	echo $memlist;

}
elseif($action == "viewpro")
{

	$query = $db->query("SELECT *, u.specifiedusers LIKE '%\t$username\t%' AS specifieduser FROM $table_members m
		LEFT JOIN $table_usergroups u ON u.specifiedusers LIKE '%\t$username\t%' OR (u.status=m.status AND
		((u.creditshigher='0' AND u.creditslower='0' AND u.specifiedusers='') OR (m.credit>=u.creditshigher
		AND m.credit<u.creditslower))) WHERE username='$username' ORDER BY specifieduser DESC");
	$memberinfo = $db->fetch_array($query);

	if(!$username) {
		showmessage("您没有选择用户名。");
	} elseif(!$memberinfo) {
		showmessage("您指定的用户不存在。");
	} else {
		preloader("member_viewpro_email,member_viewpro_regip,member_viewpro");

		$member = $memberinfo[username];
		$daysreg = ($timestamp - $memberinfo[regdate]) / (24 * 3600);
		$ppd = $memberinfo[postnum] / $daysreg;
		$ppd = round($ppd, 2);

		$memberinfo[regdate] = gmdate("$dateformat",$memberinfo[regdate]);
		$memberinfo[site] = str_replace("http://", "", $memberinfo[site]);
		$memberinfo[site] = "http://$memberinfo[site]";
		$memberinfo[site] = $memberinfo[site] != "http://" ? "$memberinfo[site]" : NULL;

		$email = $memberinfo[email] && $memberinfo[showemail] ? $memberinfo[email] : NULL;
		$avatar = $memberinfo[avatar] ? image($memberinfo[avatar]) : "<br><br><br>";

		$lastmembervisittext = gmdate("$dateformat $timeformat", $memberinfo[lastvisit] + ($timeoffset * 3600));

		$query = $db->query("SELECT COUNT(*) FROM $table_posts");
		$posts = $db->result($query, 0);
		@$percent = round($memberinfo[postnum] * 100 / $posts, 2);

		$stars = "";
		for($i = 0; $i < $memberinfo[stars]; $i++) {
			$stars .= "<img src=\"$imgdir/star.gif\">";
		}

		if($memberinfo[gender] == 1) {
			$memberinfo[gender] = "男";
		} elseif($memberinfo[gender] == 2) {
			$memberinfo[gender] = "女";
		} else {
			$memberinfo[gender] = "不告诉你";
		}

		$birthday = explode("-", $memberinfo[bday]);
		$memberinfo[bday] = $dateformat;
		$memberinfo[bday] = str_replace("n", $birthday[1], $memberinfo[bday]);
		$memberinfo[bday] = str_replace("j", $birthday[2], $memberinfo[bday]);
		$memberinfo[bday] = str_replace("Y", $birthday[0], $memberinfo[bday]);
		$memberinfo[bday] = str_replace("y", substr($birthday[0], 2, 4), $memberinfo[bday]);

		$memberinfo[bio] = nl2br($memberinfo[bio]);
		$memberinfo[signature] = postify($memberinfo[signature], "", "", "", 0, 0, $memberinfo[allowsigbbcode], $memberinfo[allowsigimgcode]);
		$encodeuser = rawurlencode(stripslashes($member));

		if($isadmin) {
			$ipaddr = $memberinfo[regip];
			$iplocation = convertip($memberinfo[regip]);
			eval("\$regip = \"".template("member_viewpro_regip")."\";");
			$edituser = " &nbsp; &nbsp; <a href=\"admincp.php?action=memberprofile&username=$encodeuser&showmsgtype=cdb_with_header\">[ 编辑用户 ]</a>";
		}
		if($memberinfo[showemail]) {
			eval("\$emailblock = \"".template("member_viewpro_email")."\";");
		}

		eval("\$profile = \"".template("member_viewpro")."\";");
		echo $profile;
	}

}
else {

	$useraction = "未定义操作 [MEMBER]";
	showmessage("未定义操作，请返回。");

}

gettotaltime();
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();
?>