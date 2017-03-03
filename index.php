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


require "./header.php";
$tplnames = "css,header,footer";

if($showoldetails == "no") {
	setcookie("onlinedetail", 0, $timestamp + (86400 * 365), $cookiepath, $cookiedomain);
	$CDB_SESSION_VARS[onlinedetail] = 0;
} elseif($showoldetails == "yes") {
	setcookie("onlinedetail", 1, $timestamp - (86400 * 365), $cookiepath, $cookiedomain);
	$CDB_SESSION_VARS[onlinedetail] = 1;
}

if($gid) {
	$whosonlinestatus = 0;
	$query = $db->query("SELECT name FROM $table_forums WHERE fid='$gid' AND type='group'");
	$cat = $db->fetch_array($query);
	$navigation ="&raquo; $cat[name]";
	$navtitle = " - $cat[name]";
}

$query = $db->query("SELECT COUNT(*) FROM $table_members");
$members = $db->result($query, 0);

$encodemember = rawurlencode($lastmember);
$memhtml = $members ? "<a href=\"member.php?action=viewpro&username=$encodemember\"><span class=\"bold\">$lastmember</span></a>" : "<span class=\"bold\">未知</span>";

if($status == "论坛管理员") {
	$statusicon = "online_admin.gif";
} elseif($online[status] == "超级版主" || $online[status] == "版主") {
	$statusicon = "online_moderator.gif";
} elseif($online[status]) {
	$statusicon = "online_member.gif";
} else {
	$statusicon = "online_guest.gif";
}

if($CDB_CACHE_VARS[announcements]) //有通告
{
	$announcements = $space = "";
	foreach($CDB_CACHE_VARS[announcements] as $announcement) {
		if($timestamp >= $announcement[starttime] && ($timestamp <= $announcement[endtime] || !$announcement[endtime]))
		{
			$announcements .= "$space<a href=\"misc.php?action=announcements&id=$announcement[id]#$announcement[id]\"><span class=\"bold\">$announcement[subject]</span> ".
				"(".gmdate("$dateformat", $announcement[starttime] + $timeoffset * 3600).")</a>";
			$space = "&nbsp; &nbsp; &nbsp; ";
		}
	}
	if(strlen($announcements) > 200) {//长度太长,让公告从右到左移动
		$announcements = "<marquee width=\"100%\" scrollamount=\"3\" onMouseOver=\"this.stop();\" onMouseOut=\"this.start();\">$announcements</marquee>";
	}
} else {
	$announcements = "截止到 ".gmdate("$dateformat $timeformat", $timestamp + $timeoffset * 3600)." 没有公告";
}

if($cdbuser && $newu2u) //短消息
{
	$query = $db->query("SELECT u2uid, msgfrom, subject, message FROM $table_u2u WHERE msgto='$cdbuser' AND folder='inbox' AND new='1'");
	$newu2unum = $db->num_rows($query);
	if($newu2unum)
	{
		$count = 0;
		$u2udetail = "";
		while($count < 2 && $u2u = $db->fetch_array($query))
        {
			$count++;
			$u2u[subject] = wordscut($u2u[subject], 20);
			$u2u[message] = wordscut($u2u[message], 80);
			$u2udetail .= "<tr><td valign=\"top\"><li></li></td><td class=\"bold\" nowrap>来自：</td><td><a href=\"member.php?action=viewpro&username=".rawurlencode($u2u[msgfrom])."\" target=\"_blank\">$u2u[msgfrom]</a></td>\n"
				."<td align=\"right\" class=\"bold\">标题：</td><td><a href=\"###\" onclick=\"Popup('u2u.php?action=view&u2uid=$u2u[u2uid]&sid=$sid', 'Window', 550, 450);\">$u2u[subject]</a></td></tr>\n"
				."<tr><td></td><td valign=\"top\" class=\"bold\" nowrap>内容：</td><td colspan=\"3\" valign=\"top\">$u2u[message]</td></tr>\n";
		}
		$db->query("UPDATE $table_u2u SET new='2' WHERE msgto='$cdbuser' AND folder='inbox' AND new='1'");
		$db->query("UPDATE $table_members SET newu2u='0' WHERE username='$cdbuser'");
		$CDB_SESSION_VARS[newu2u] = 0;
		$newu2umsg = "<td width=\"40%\"><marquee scrollamount=\"3\" size=\"10\"><a href=\"###\" onclick=\"Popup('u2u.php?sid=$sid', 'Window', 580, 450);\"><font class=\"mediumtxt\">您有 $newu2unum 条未被提示过的新消息，请注意查收</font></a></marquee></td>\n";
		eval("\$newu2upopup = \"".template("index_newu2upopup")."\";");
	}
}

if(!$gid)
{

	preloader("index_news,index_forumlink_row,index_forumlink_noimg_row,index_forumlink,index_whosonline,index_forum,index_forum_lastpost,index_category,index");
	$useraction = "浏览『{$bbname}』首页";

	$newthreads = $timestamp - $lastvisit;
	if($cdbuser) {
		$mytopicslink = "| <a href=\"misc.php?action=search&srchuname=".rawurlencode("$cdbuser")."&srchfrom=604800&searchsubmit=1\">我的话题</a> ";
	}
	if(is_array($CDB_CACHE_VARS[forumlinks]))//联盟论坛
	{
		$forumlink_row = $forumlink_tight_row1 = $forumlink_tight_row2 = "";
		$flinkdisplay = 0;
		foreach($CDB_CACHE_VARS[forumlinks] as $flink)
		{
			if($flink[note] && $flink[logo]) {
				eval("\$forumlink_row .= \"".template("index_forumlink_row")."\";");
			} elseif($flink[note]) {
				$forumlink_content = "<a href=\"$flink[url]\" target=\"_blank\"><span class=\"bold\">$flink[name]</span></a><br>$flink[note]";
				eval("\$forumlink_row .= \"".template("index_forumlink_noimg_row")."\";");
			} elseif(!$flink[note] && $flink[logo]) {
				$forumlink_tight_row1 .= "<a href=\"$flink[url]\" target=\"_blank\"><img src=\"$flink[logo]\" border=\"0\" alt=\"$flink[name]\"></a> &nbsp; ";
			} elseif(!$flink[note] && !$flink[logo]) {
				$forumlink_tight_row2 .= "<a href=\"$flink[url]\" target=\"_blank\">[$flink[name]]</a> ";
			}
		}
		if($forumlink_tight_row1 || $forumlink_tight_row2) {
			$forumlink_tight_row1 = $forumlink_tight_row1 ? $forumlink_tight_row1."<br>" : "";
			$forumlink_content = $forumlink_tight_row1.$forumlink_tight_row2;
			eval("\$forumlink_row .= \"".template("index_forumlink_noimg_row")."\";");
		}
		if($forumlink_row) {
			eval("\$forumlink = \"".template("index_forumlink")."\";");
		}
	}

	$forumlist = "";
	$threads = $posts = 0;
	$query = $db->query("SELECT * FROM $table_forums WHERE status='1' ORDER BY displayorder");//查找所有论坛
	while($forum = $db->fetch_array($query)) {
		$forums[] = $forum;
		$forumname[$forum[fid]] = strip_tags($forum[name]);
		if($forum[type] != "group") {
			$threads += $forum[threads];
			$posts += $forum[posts];
		}
	}
    //论坛界面
	foreach($forums as $groupfid => $group)
	{
		if($group[type] == "group") {
			eval("\$forumlist .= \"".template("index_category")."\";");//组论坛界面
			foreach($forums as $forumfid => $forum) {
				if($forum[fup] == $group[fid] && $forum[type] == "forum") {
					$forumlist .= forum($forum, "index_forum");//二级论坛界面
				}
			}
		} elseif(!$group[fup] && $group[type] == "forum")
        {
			$forumlist .= forum($group, "index_forum");
		}
	}
    //在线用户
	if($whosonlinestatus) //在首页和论坛列表页显示在线会员列表
	{
		$membercount = $guestcount = 0;

		$onlineinfo = explode("\t", $onlinerecord);//在线人数\t 时间
		if(!isset($_COOKIE[onlinedetail]) && !isset($CDB_SESSION_VARS[onlinedetail]))
		{
			if($onlineinfo[0] > 500) {
				setcookie("onlinedetail", 0, $timestamp + (86400 * 365), $cookiepath, $cookiedomain);
				$CDB_SESSION_VARS[onlinedetail] = 0;
			} else {
				setcookie("onlinedetail", 1, $timestamp + (86400 * 365), $cookiepath, $cookiedomain);
				$CDB_SESSION_VARS[onlinedetail] = 1;
			}
		}

		if(($_COOKIE[onlinedetail] || $CDB_SESSION_VARS[onlinedetail] || $showoldetails == "yes") && $showoldetails != "no")
		{
			$oldetaillink = "<a href=\"index.php?showoldetails=no\" style=\"color: $cattext\">[关闭详细列表]</a>";
			$memtally = $table = "";
			$online = array("username" => $cdbuserss, "time" => $timestamp, "fid" => 0, "action" => "浏览『{$bbname}』首页", "status" => $status);
			$query = $db->query("SELECT username, status, time, fid, action, username<>'' AS guests FROM $table_sessions WHERE !(username='$cdbuser' AND ip='$onlineip') ORDER BY guests DESC");
			do {
				$online[username] ? $membercount++ : $guestcount++;
				$online[time] = gmdate("$timeformat", $online[time] + ($timeoffset * 3600));
				$onlinedetail = "时间：$online[time]";
				$onlinedetail .= $online[fid] ? "\n论坛：".$forumname[$online[fid]] : NULL;
				$onlinedetail .= "\n动作：$online[action]";

				if($online[status] == "论坛管理员")
				{
					$memtally .= "$table<img src=\"$imgdir/online_admin.gif\" align=\"absmiddle\" alt=\"$onlinedetail\"> <a href=\"member.php?action=viewpro&username=".rawurlencode($online[username])."\" title=\"$onlinedetail\"><b><i>$online[username]</i></b></a>";
				}
				elseif($online[status] == "超级版主" || $online[status] == "版主")
                {
					$memtally .= "$table<img src=\"$imgdir/online_moderator.gif\" align=\"absmiddle\" alt=\"$onlinedetail\"> <a href=\"member.php?action=viewpro&username=".rawurlencode($online[username])."\" title=\"$onlinedetail\"><b>$online[username]</b></a>";
				}
				elseif($online[status] == "正式会员")
                {
					$memtally .= "$table<img src=\"$imgdir/online_member.gif\" align=\"absmiddle\" alt=\"$onlinedetail\"> <a href=\"member.php?action=viewpro&username=".rawurlencode($online[username])."\" title=\"$onlinedetail\">$online[username]</a>";
				}
				else {
					$memtally .= "$table<img src=\"$imgdir/online_guest.gif\" align=\"absmiddle\" alt=\"$onlinedetail\"> <span title=\"$onlinedetail\">游客</span>";
				}

				$table = ($membercount + $guestcount) % 7 == 0 ? "</td></tr><tr><td nowrap>" : "</td><td nowrap>";
			} while($online = $db->fetch_array($query));
			$onlinenum = $membercount + $guestcount;
			$memberlist = "<tr><td colspan=\"7\"><hr noshade size=\"0\" width=\"100%\" color=\"$bordercolor\" align=\"center\"></td></tr>\n<tr><td nowrap>$memtally</td></tr>";
			$memonmsg = "<font style=\"color: $cattext\">&nbsp;<span class=\"bold\">$membercount</span> 位会员，<span class=\"bold\">$guestcount</span> 位游客，共 <span class=\"bold\">$onlinenum</span> 人 | 最高记录是 <span class=\"bold\">".gmdate("$dateformat $timeformat", $onlineinfo[1] + ($timeoffset * 3600))."</span> 共 <span class=\"bold\">$onlineinfo[0]</span> 人在线</font>";
		} else
		    {
			$oldetaillink = "<a href=\"index.php?showoldetails=yes\" style=\"color: $cattext\">[打开详细列表]</a>";
			$query = $db->query("SELECT COUNT(*) FROM $table_sessions");
			$onlinenum = $db->result($query, 0);
			$memonmsg = "<font style=\"color: $cattext\">&nbsp;共 <span class=\"bold\">$onlinenum</span> 人在线 | 最高记录是 <span class=\"bold\">".gmdate("$dateformat $timeformat", $onlineinfo[1] + ($timeoffset * 3600))."</span> 共 <span class=\"bold\">$onlineinfo[0]</span> 人在线</font>";
		}

		if($onlinenum > $onlineinfo[0]) {
			$db->query("UPDATE $table_settings SET onlinerecord='$onlinenum\t$timestamp'");
			updatecache("settings", $table_settings, "", "vars");
			$onlineinfo[0] = $onlinenum;
			$onlineinfo[1] = $timestamp;
		}

		eval("\$whosonline = \"".template("index_whosonline")."\";");
	}

	if(is_array($CDB_CACHE_VARS[news])) {
		$num = 0;
		$index_news_row = "";
		foreach($CDB_CACHE_VARS[news] as $news) {
			$news[subject] = htmlspecialchars($news[subject]);
			$news[link] = htmlspecialchars($news[link]);
			eval("\$news[subject] = addslashes(\"$news[subject]\");");
			eval("\$news[link] = addslashes(\"$news[link]\");");
			if($news[link]) {
				$index_news_row .= "fcontent[$num] = \"<a id='newslink' href='$news[link]'>$news[subject]</a>\";\n";
			} else {
				$index_news_row .= "fcontent[$num] = \"$news[subject]\";\n";
			}
			$num++;
		}
		if($index_news_row) {
			eval("\$index_news = \"".template("index_news")."\";");
		}
	}

}
else
    {

	preloader("index_category,index_forum_lastpost,index_forum,index");

	$forumlist = "";
	$threads = $posts = 0;
	$queryg = $db->query("SELECT type, fid, name, lastpost FROM $table_forums WHERE type='group' AND fid='$gid' AND status='1' ORDER BY displayorder");
	$group = $db->fetch_array($queryg);
	eval("\$forumlist .= \"".template("index_category")."\";");
	$query = $db->query("SELECT * FROM $table_forums WHERE type='forum' AND status='1' AND fup='$group[fid]' ORDER BY displayorder");
	while($forum = $db->fetch_array($query)) {
		$forumlist .= forum($forum, "index_forum");
		$threads += $forum[threads];
		$posts += $forum[posts];
	}
	$useraction = "浏览『$group[name]』";
}

eval("\$index = \"".template("index")."\";");
echo $index;

gettotaltime();
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();

?>