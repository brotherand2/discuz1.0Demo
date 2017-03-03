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
$tplnames = "css,header,footer,memcp_navbar";

if($action == "profile") {
        $navigation = "&raquo; <a href=\"memcp.php\">控制面板</a> &raquo; 编辑个人资料";
        $navtitle = " - 控制面板 - 编辑个人资料";
        $useraction = "编辑个人资料";
} elseif($action == "subscriptions") {
        $navigation = "&raquo; <a href=\"memcp.php\">控制面板</a> &raquo; 订阅列表";
        $navtitle = " - 控制面板 - 订阅列表";
        $useraction = "编辑订阅列表";
} elseif($action == "favorites") {
        $navigation = "&raquo; <a href=\"memcp.php\">控制面板</a> &raquo; 收藏夹";
        $navtitle = " - 控制面板 - 收藏夹";
        $useraction = "编辑个人收藏夹";
} elseif($action == "viewavatars") {
        $navigation = "&raquo; <a href=\"memcp.php\">控制面板</a> &raquo; 论坛头像列表";
        $navtitle = " - 控制面板 - 论坛头像列表";
        $useraction = "选择论坛头像";
} else {
        $navigation = "&raquo; 控制面板";
        $navtitle = " - 控制面板";
        $useraction = "用户控制面板";
}

if(!$cdbuser || !$cdbpw) {
	showmessage("您还没有登录，无法进入控制面板。");
}

if($action == "profile") {

	if(!$editsubmit) {

		preloader("memcp_profile_cstatus,memcp_profile,memcp_profile_avatar,memcp_profile_changecode");
		makenav($action);

		$query = $db->query("SELECT * FROM $table_members WHERE username='$cdbuser'");
		$member = $db->fetch_array($query);

		$emailcheckmsg = $emailcheck ? "<b>!如更改地址，系统将修改您的密码并重新验证其有效性，请慎用</b>" : NULL;
		$emailchecked = $member[showemail] ? "checked=\"checked\"" : NULL;
		$newschecked = $member[newsletter] ? "checked=\"checked\"" : NULL;
		$currdate = gmdate($timeformat);

		if($member[gender] == 1) {
			$checkmale = "checked";
		} elseif($member[gender] == 2) {
			$checkfemale = "checked";
		} else {
			$checkunknown = "checked";
		}

		$themelist = "<select name=\"themenew\">\n<option value=\"\">--使用默认值--</option>";
		$query = $db->query("SELECT themename FROM $table_themes");
		while($theme = $db->fetch_array($query)) {
			if($theme[themename] == $member[theme]) {
				$themelist .= "<option value=\"$theme[themename]\" selected=\"selected\">$theme[themename]</option>\n";
			} else {
				$themelist .= "<option value=\"$theme[themename]\">$theme[themename]</option>\n";
			}
		}
		$themelist  .= "</select>";

		$bday = explode("-", $member[bday]);
		$bday[0] = $bday[0] == "0000" ? "" : $bday[0];
		$month = array(intval($bday[1]) => "selected=\"selected\"");

		$dayselect = "<select name=\"day\">\n";
		$dayselect .= "<option value=\"\">&nbsp;</option>\n";
		for($num = 1; $num <= 31; $num++) {
			if($bday[2] == $num) {
				$dayselect .= "<option value=\"$num\" selected=\"selected\">$num</option>\n";
			} else {
				$dayselect .= "<option value=\"$num\">$num</option>\n";
			}
		}
		$dayselect .= "</select>";

		$member[dateformat] = str_replace("n", "mm", $member[dateformat]);
		$member[dateformat] = str_replace("j", "dd", $member[dateformat]);
		$member[dateformat] = str_replace("y", "yy", $member[dateformat]);
		$member[dateformat] = str_replace("Y", "yyyy", $member[dateformat]);

		$member[timeformat] == "H:i" ? $check24 = "checked=\"checked\"" : $check12 = "checked=\"checked\"";
		$allowmaxsigsize = $maxsigsize ? " ($maxsigsize 字符以内)" : NULL;
		$imgcodeis = $allowsigimgcode ? "On" : "Off";
		$bbcodeis = $allowsigbbcode ? "On" : "Off";
		$allowcstatus ? eval("\$customuserstatus = \"".template("memcp_profile_cstatus")."\";") : $customuserstatus = "";
		if($allowavatar == 1) {
			eval("\$avatarselect = \"".template("memcp_profile_avatarlist")."\";");
		} elseif($allowavatar == 2) {
			eval("\$avatarselect = \"".template("memcp_profile_avatar")."\";");
		}

		if($chcode) {
			$member[charset] == "big5" ? $bigcheck = "selected=\"selected\"" : $gbcheck = "selected=\"selected\"";
			eval("\$changecode = \"".template("memcp_profile_changecode")."\";");
		}

		eval("\$profile = \"".template("memcp_profile")."\";");
		echo $profile;
	}

	if($editsubmit) {

		if($newpassword) {
			if(encrypt($oldpassword) != $cdbpw) {
				showmessage("原密码不正确，您不能修改密码！");
			} elseif(ereg('"', $newpassword) || ereg("'", $newpassword)) {
				showmessage("用户密码包含非法字符，请返回重新填写。");
			}
			$newpassword = encrypt($newpassword);
			$newpasswdadd = ", password='$newpassword'";
		} else {
			$newpassword = $cdbpw;
			$newpasswdadd = "";
		}

		if($maxsigsize && strlen($signew) > $maxsigsize) {
			showmessage("您的签名长度超过 $maxsigsize 字符的限制，请返回修改。");
		}
		if($allowavatar == 2 && $avatarnew) {
			if($maxavatarsize) {
				if(strstr($avatarnew, ",")) {
					$avatarinfo = explode(",", $avatarnew);
					if(trim($avatarinfo[1]) > $maxavatarsize || trim($avatarinfo[2]) > $maxavatarsize) {
						showmessage("您设置的 Flash 头像超过了系统定义的宽 $maxavatarsize 像素，高 $maxavatarsize 像素，请返回重新填写。");
					}
				} else {
					if($image_size = @getimagesize($avatarnew)) {
						if($image_size[0] > $maxavatarsize || $image_size[1] > $maxavatarsize) {
							showmessage("您的自定义头像超过了系统定义的宽 $maxavatarsize 像素，高 $maxavatarsize 像素，请返回重新填写。");
						}
					} else {
						showmessage("您的自定义头像无法打开，请返回确认头像链接是有效的。");
					}
				}
			}
			$avatarnew = cdbhtmlspecialchars($avatarnew);
			$avataradd = ", avatar='$avatarnew'";
		} else {
			$avataradd = "";
		}

		$locationnew = cdbhtmlspecialchars($locationnew);
		$icqnew = cdbhtmlspecialchars($icqnew);
		$yahoonew = cdbhtmlspecialchars($yahoonew);
		$oicqnew = cdbhtmlspecialchars($oicqnew);
		$emailnew = cdbhtmlspecialchars($emailnew);
		$sitenew = cdbhtmlspecialchars($sitenew);
		$bionew = cdbhtmlspecialchars($bionew);
		$bdaynew = cdbhtmlspecialchars($bdaynew);
		$cstatusnew = cdbhtmlspecialchars($cstatusnew);
		$timeformatnew = $timeformatnew == "12" ? "h:i A" : "H:i";

		$bdaynew = ($month && $day && $year) ? "$year-$month-$day" : "";
		$cstatusadd = $allowcstatus ? ", customstatus='$cstatusnew'" : "";

		$dateformatnew = str_replace("mm", "n", $dateformatnew);
		$dateformatnew = str_replace("dd", "j", $dateformatnew);
		$dateformatnew = str_replace("yyyy", "Y", $dateformatnew);
		$dateformatnew = str_replace("yy", "y", $dateformatnew);

		$charsetnew = $charsetnew == "big5" ? "big5" : "gb2312";

		if($emailcheck) {
			$query = $db->query("SELECT email FROM $table_members WHERE username='$cdbuser'");
			if($emailnew != $db->result($query, 0)) {
				if(!$doublee) {
					$query = $db->query("SELECT COUNT(*) FROM $table_members WHERE email='$emailnew'");
					if($db->result($query, 0)) {
						showmessage("该 Email 地址已经被注册了，请返回重新填写。");
					}
				}					
				$newpassword = random(8);
				$newpasswdadd = ", password='".encrypt($newpassword)."'";
				sendmail($emailnew, "[Discuz!] Email 地址确认邮件",
						"您在 $bbname [ $boardurl ] 修改了注册 Email 地址，\n".
						"这是系统发送的确认邮件，请用如下资料登录：\n\n".
						"用户名：$cdbuser\n".
						"密码：$newpassword\n\n".
						"您可以登录后修改此密码。\n".
						"非常感谢您对我们的信赖与支持，欢迎您光临 {$bbname}。",
					"From: $bbname <$adminemail>");
			}
		}

		$db->query("UPDATE $table_members SET gender='$gendernew', email='$emailnew', site='$sitenew', oicq='$oicqnew', location='$locationnew',
			bio='$bionew', signature='$signew', showemail='$showemailnew', timeoffset='$timeoffsetnew', icq='$icqnew',
			yahoo='$yahoonew', theme='$themenew', charset='$charsetnew', bday='$bdaynew', tpp='$tppnew',
			ppp='$pppnew', newsletter='$newsletternew', timeformat='$timeformatnew', msn='$msnnew', dateformat='$dateformatnew',
			pwdrecover='', pwdrcvtime='' $avataradd $cstatusadd $newpasswdadd WHERE username='$cdbuser'");

		$CDB_SESSION_VARS[cdbpw] = $newpassword;
		$CDB_SESSION_VARS[charset] = $charsetnew;
		$CDB_SESSION_VARS[timeoffset] = $timeoffsetnew;
		$CDB_SESSION_VARS[theme] = $themenew ? $themenew : $CDB_CACHE_VARS[settings][theme];
		$CDB_SESSION_VARS[themename] = "";
		$CDB_SESSION_VARS[tpp] = $tppnew;
		$CDB_SESSION_VARS[ppp] = $pppnew;
		$CDB_SESSION_VARS[timeformat] = $timeformatnew;
		$CDB_SESSION_VARS[dateformat] = $dateformatnew;
		$CDB_SESSION_VARS[avatar] = $avatarnew;
		$CDB_SESSION_VARS[signature] = $signew ? 1 : 0;
		$CDB_SESSION_VARS[cstatus] = $allowcstatus ? $cstatusnew : "";
		
		if($emailcheck && $email != $oldemail) {
			showmessage("您修改了 Email 地址，请用确认邮件中的密码登录论坛。");
		} else {
			showmessage("您已经成功保存个人资料，现在将转入控制面板首页。", "memcp.php");
		}
	}

} elseif($action == "favorites") {

	if($favadd && !$favsubmit) {

		$query = $db->query("SELECT tid FROM $table_favorites WHERE tid='$favadd' AND username='$cdbuser'");
		if($db->num_rows($query)) {
			showmessage("您过去已经收藏过这个主题，请返回。"); 
		} else {
			$db->query("INSERT INTO $table_favorites (tid, username)
				VALUES ('$favadd', '$cdbuser')");
			showmessage("您选择的主题已成功添加到收藏夹中，现在将回到上一页。", $referer);
		}

	} elseif(!$favadd && !$favsubmit) {

		preloader("memcp_favs_none,memcp_favs,memcp_favs_row");
		makenav($action);

		$query = $db->query("SELECT t.*, f.name FROM $table_favorites fav, $table_threads t, $table_forums f WHERE fav.tid=t.tid AND fav.username='$cdbuser' AND t.fid=f.fid ORDER BY t.lastpost DESC");
		if($db->num_rows($query)) {
			while($fav = $db->fetch_array($query)) {
				$fav[icon] = $fav[icon] ? "<img src=\"$smdir/$fav[icon]\">" : "&nbsp;";
				$lastposter = $fav[lastposter] != "游客" ? "<a href=\"member.php?action=viewpro&username=".rawurlencode($fav[lastposter])."\">$fav[lastposter]</a>" : "游客";
				$lastreplytime = gmdate("$dateformat $timeformat", $fav[lastpost] + ($timeoffset * 3600));
				$lastpost = "$lastreplytime by $lastposter";

				eval("\$favs .= \"".template("memcp_favs_row")."\";");
			}
		} else {
			eval("\$favs = \"".template("memcp_favs_none")."\";");
		}

		eval("\$favorites = \"".template("memcp_favs")."\";");
		echo $favorites;
	}
	if(!$favadd && $favsubmit) {
		$ids =$comma = "";
		if(is_array($delete)) {
			foreach($delete as $deleteid) {
				$ids .= "$comma$deleteid";
				$comma = ", ";
			}
		}

		if($ids) {
			$db->query("DELETE FROM $table_favorites WHERE username='$cdbuser' AND tid IN ($ids)");
		}
		showmessage("收藏夹已成功更新，现在将转入更新后的收藏夹。", $referer);
	}
} elseif($action == "subscriptions") {

	if($subadd && !$subsubmit) {

		$query = $db->query("SELECT tid FROM $table_subscriptions WHERE tid='$subadd' AND username='$cdbuser'");
		if($db->num_rows($query)) {
			showmessage("您过去已经订阅过这个主题，请返回。");
		} else {
			$db->query("INSERT INTO $table_subscriptions (username, email, tid, lastnotify)
				VALUES ('$cdbuser', '$email', '$subadd', '')");
			showmessage("您选择的主题已经成功订阅，现在将回到上一页。", $referer);
		}

	} elseif(!$subadd && !$subsubmit) {

		preloader("memcp_subs_none,memcp_subs,memcp_subs_row");
		makenav($action);

		$query = $db->query("SELECT t.*, f.name FROM $table_subscriptions s, $table_threads t, $table_forums f WHERE t.tid=s.tid AND f.fid=t.fid AND s.username='$cdbuser' ORDER BY t.lastpost DESC");
		if($db->num_rows($query)) {
			while($sub = $db->fetch_array($query)) {
				$lastposter = $sub[lastposter] != "游客" ? "<a href=\"member.php?action=viewpro&username=".rawurlencode($sub[lastposter])."\">$sub[lastposter]</a>" : "游客";
				$lastreplytime = gmdate("$dateformat $timeformat", $sub[lastpost] + ($timeoffset * 3600));
				$lastpost = "$lastreplytime by $lastposter";
				$sub[icon] = $sub[icon] ? "<img src=\"$smdir/$sub[icon]\">" : "&nbsp;";

				eval("\$subs .= \"".template("memcp_subs_row")."\";");
			}
		} else {
			eval("\$subs = \"".template("memcp_subs_none")."\";");
		}

		eval("\$page = \"".template("memcp_subs")."\";");
		echo $page;

	} elseif(!$subadd && $subsubmit) {

		$ids =$comma = "";
		if(is_array($delete)) {
			foreach($delete as $deleteid) {
				$ids .= "$comma$deleteid";
				$comma = ", ";
			}
		}

		if($ids) {
			$db->query("DELETE FROM $table_subscriptions WHERE username='$cdbuser' AND tid IN ($ids)");
		}
		showmessage("订阅列表已经成功更新，现在将转入更新后的订阅列表。", $referer);
	}


} elseif($action == "viewavatars") {

	if(!$avasubmit) {

		preloader("memcp_profile_viewavatars");
		$app = 16;
		$avatarsdir = "images/avatars";
		if(!$page) {
			$page = 1;
		}

		$query = $db->query("SELECT avatar FROM $table_members WHERE username='$cdbuser'");
		$member = $db->fetch_array($query);
		$avatarlist = "";
		$num = 1;
		$adir = dir($avatarsdir);
		while($entry = $adir->read()) {
			if ($entry != "." && $entry != "..") {
				if (is_file("$avatarsdir/$entry")) {
					$avatars[$num] = "$entry";
					$num ++;
				}
			}
		}
		$adir->close();
		$num--;

		$start = ($page - 1) * $app;
		$end = ($start + $app > $num) ? ($num - 1) : ($start + $app - 1);

		$multipage = multi($num, $app, $page, "memcp.php?action=viewavatars");
		$multipage .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;共 $num 张头像";
		for($i = $start; $i <= $end; $i += 4) {
			$avatarlist .= "<tr>\n";
			for($j = 0; $j < 4; $j++) {
				$thisbg = ($thisbg == $altbg1) ? $altbg2 : $altbg1;
				$avatarlist .= "<td bgcolor=\"$thisbg\" width=\"25%\" align=\"center\">";
				if($avatars[$i + $j] && ($i + $j)) {
					$avatarlist .= "<img src=\"images/avatars/".$avatars[$i + $j]."\"></td>\n";
				} else {
					$avatarlist .= "&nbsp;</td>\n";
				}
			}
			$avatarlist .= "</tr><tr>\n";
			for($j = 0; $j < 4; $j++) {
				$avatarlist .= "<td bgcolor=\"$thisbg\" width=\"25%\" align=\"center\">";
				if($avatars[$i + $j] && ($i + $j)) {
					if(strpos($member[avatar], $avatars[$i + $j])) {
						$checked = "checked";
					} else {
						$checked = "";
					}
					$avatarlist .= "<input type=\"radio\" value=\"images/avatars/".$avatars[$i + $j]."\" name=\"avatarnew\" $checked>".$avatars[$i + $j]."\n";
				} elseif($i + $j == 0) {
					if(!$member[avatar]) {
						$checked = "checked";
					}
					$avatarlist .= "<input type=\"radio\" value=\"\" name=\"avatarnew\" $checked><span class=\"bold\">不使用头像</span>\n";
				} else {
					$avatarlist .= "&nbsp;</td>\n";
				}
				$thisbg = ($thisbg == $altbg1) ? $altbg2 : $altbg1;
			}
			$avatarlist .= "</tr><tr><td bgcolor=\"$altbg1\" colspan=\"4\" height=\"1\"></td></tr>\n\n";
		}

		eval("\$avatarselect = \"".template("memcp_profile_viewavatars")."\";");
		echo $avatarselect;

	} elseif($avasubmit) {

		$CDB_SESSION_VARS[avatar] = $avatarnew;
		$db->query("UPDATE $table_members SET avatar='$avatarnew' WHERE username='$cdbuser'");
		showmessage("您头像设置已成功更新，现在将转入个人资料页", "memcp.php?action=profile");

	}

} elseif($action == "credits") {

	$credits = base64_decode(strip_tags(template("cdb_credits")));
	eval("\$credits = \"$credits\";");
	showmessage($credits, "", 1);

} else {

	preloader("memcp_home_u2u_none,memcp_home_subs_none,memcp_home,buddylist_buddy_offline,buddylist_buddy_online,memcp_home_u2u_row,forumdisplay_thread_lastpost,memcp_home_subs_row");
	makenav($action);

	$query = $db->query("SELECT b.*, s.username AS onlineuser FROM $table_buddys b LEFT JOIN $table_sessions s ON s.username=b.buddyname WHERE b.username='$cdbuser'");
	while($buddy = $db->fetch_array($query)) {
		$encodename = rawurlencode($buddy[buddyname]);
		if($buddy[onlineuser]) {
			eval("\$buddys[online] .= \"".template("buddylist_buddy_online")."\";");
		} else {
			eval("\$buddys[offline] .= \"".template("buddylist_buddy_offline")."\";");
		}
	}

	$avatar = $avatar ? image($avatar) : "&nbsp;";

	$query = $db->query("SELECT * FROM $table_u2u WHERE msgto='$cdbuser' AND folder='inbox' ORDER BY dateline DESC LIMIT 0, 5");
	if($db->num_rows($query)) {
		while($message = $db->fetch_array($query)) {
			$senton = gmdate("$dateformat $timeformat", $message[dateline] + ($timeoffset * 3600));
			$message[subject] = $message[subject] ? $message[subject] : "&lt;无标题&gt;";
			$message[subject] = $message['new'] ? "<b>$message[subject]</b>" : $message[subject];

			eval("\$messages .= \"".template("memcp_home_u2u_row")."\";");
		}
	} else {
		eval("\$messages = \"".template("memcp_home_u2u_none")."\";");
	}

	$query = $db->query("SELECT t.*, f.name FROM $table_subscriptions s, $table_threads t, $table_forums f WHERE t.tid=s.tid AND f.fid=t.fid AND s.username='$cdbuser' ORDER BY t.lastpost DESC LIMIT 0, 5");
	if($db->num_rows($query)) {
		while($sub = $db->fetch_array($query)) {
			$lastposter = $sub[lastposter] != "游客" ? "<a href=\"member.php?action=viewpro&username=".rawurlencode($sub[lastposter])."\">$sub[lastposter]</a>" : "游客";
			$lastreplytime = gmdate("$dateformat $timeformat", $sub[lastpost] + $timeoffset * 3600);
			$lastpost = "$lastreplytime<br>by $lastposter";
			$sub[icon] = $sub[icon] ? "<img src=\"$smdir/$sub[icon]\">" : "&nbsp;";
			eval("\$lastpost = \"".template("forumdisplay_thread_lastpost")."\";");
			eval("\$subs .= \"".template("memcp_home_subs_row")."\";");
		}
	} else {
		eval("\$subs .= \"".template("memcp_home_subs_none")."\";");
	}

	eval("\$home = \"".template("memcp_home")."\";");
	echo $home;
}

gettotaltime();
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();

function makenav($action) {
	global $bordercolor, $borderwidth, $tablewidth, $tablespace, $borderwidth, $tablespacing, $altbg1, $altbg2, $navbar, $sid;
	$action = $action ? $action : "home";
	$navcolor[home] = $navcolor[profile] = $navcolor[subscriptions] = $navcolor[favorites] = $altbg2;

	$navlink[home] = "<a href=\"memcp.php\">控制面板首页</a>";
	$navlink[profile] = "<a href=\"memcp.php?action=profile\">编辑个人资料</a>";
	$navlink[subscriptions] = "<a href=\"memcp.php?action=subscriptions\">订阅列表</a>";
	$navlink[favorites] = "<a href=\"memcp.php?action=favorites\">收 藏 夹</a>";

	switch($action) {
		case home: $navcolor[home] = $altbg1; $navlink[home] = "控制面板首页"; break;
		case profile: $navcolor[profile] = $altbg1; $navlink[profile] = "编辑个人资料"; break;
		case subscriptions: $navcolor[subscriptions] = $altbg1; $navlink[subscriptions] = "订阅列表"; break;
		case favorites: $navcolor[favorites] = $altbg1; $navlink[favorites] = "收 藏 夹"; break;
	}
	eval("\$navbar = \"".template("memcp_navbar")."\";");
}
?>