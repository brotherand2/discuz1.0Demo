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
$ismoderator = modcheck($cdbuser);

$tid = $tid ? $tid : $delete[0];
if($tid) {
	$query = $db->query("SELECT * FROM $table_threads WHERE tid='$tid'");
	$thread = $db->fetch_array($query);
	$threadname = stripslashes($thread[subject]);
	$threadname .= $action == "delthread" ? " .. 等" : NULL;
}

if($forum[type] == "forum") {
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> &raquo; <a href=\"viewthread.php?tid=$tid\">$threadname</a> ";
	$navtitle = " - $forum[name] - $threadname";
} else {
	$query = $db->query("SELECT name, fid FROM $table_forums WHERE fid='$forum[fup]'");
	$fup = $db->fetch_array($query);
	$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> &raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> &raquo; <a href=\"viewthread.php?tid=$tid\">$threadname</a> ";
	$navtitle = " - $fup[name] - $forum[name] - $threadname";
}

if($action == "getip") {
	$navigation .= "&raquo; 取得 IP";
	$navtitle .= " - 取得 IP";
	$useraction = "取得作者 IP";
	$modaction = "取得 IP";
} else {
	if($action == "delthread") {
		$cdbaction = "删除指定主题";
	} elseif($action == "delpost") {
		$cdbaction = "删除指定贴子";
	} elseif($action == "delete") {
		$cdbaction = "删除主题";
	} elseif($action == "digist") {
		$cdbaction = ($thread[digist] ? "取消" : "加入")."精华";
	} elseif($action == "top") {
		$cdbaction = $thread[topped] ? "解除置顶" : "主题置顶";
	} elseif($action == "close") {
		$cdbaction = ($thread[closed] ? "打开" : "关闭")."主题";
	} elseif($action == "move") {
		$cdbaction = "移动主题";
	} elseif($action == "karma") {
		$cdbaction = "主题评分";
	} elseif($action == "bump") {
		$cdbaction = "提升主题";
	} elseif($action == "report") {
		$cdbaction = "报告此贴";
	} elseif($action == "split") {
		$cdbaction = "分割主题";
	} elseif($action == "merge") {
		$cdbaction = "合并主题";
	} elseif($action == "votepoll") {
		$cdbaction = "投票";
	} elseif($action == "emailfriend") {
		$cdbaction = "推荐给朋友";
	}

	$navigation .= "&raquo; $cdbaction";
	$navtitle .= " - $cdbaction";
	$useraction = "$cdbaction『$threadname』";
	$modaction = $cdbaction;
}

if(!$forum[viewperm] && !$allowview) {
	showmessage("对不起，您的级别〔{$grouptitle}〕无法浏览贴子。");
} elseif($forum[viewperm] && !strstr($forum[viewperm], "\t$groupid\t")) {
	showmessage("对不起，本论坛只有特定用户可以浏览，请返回。");
} elseif($thread[creditsrequire] > $credit && !$ismoderator) {
	showmessage("对不起，本贴要求{$credittitle}高于 $thread[creditsrequire] {$creditunit}才可浏览，请返回。");
}


if((!$cdbuser || !$cdbpw) && $action != "votepoll") {
	showmessage("对不起，您尚未登录或没有权限进行此操作。");
} elseif($action == "votepoll" && !$allowvote) {
	showmessage("对不起，您的级别〔{$grouptitle}〕无法参与投票。");
} elseif($action != "votepoll" && $action != "karma" && $action != "report" && $action != "emailfriend" && !$ismoderator) {
		showmessage("对不起，您没有权限使用管理功能。");
}

$fupadd = $fup ? "OR (fid='$fup[fid]' && type<>'group')" : NULL;

if($action == "delthread") {

	if(!is_array($delete) && !count($delete)) {
		showmessage("您没有选择要删除的主题，请返回。");
	} else {
		if(!$delthreadsubmit) {

			preloader("topicadmin_delthread");

			$deleteid = "";
			foreach($delete as $id) {
				$deleteid .= "<input type=\"hidden\" name=\"delete[]\" value=\"$id\">\n";
			}

			eval("\$delthread = \"".template("topicadmin_delthread")."\";");
			echo $delthread;
				
		} else {

			$tids = $comma = "";
			foreach($delete as $id) {
				$tids .= "$comma'$id'";
				$comma = ", ";
			}

			$usernames = $comma = "";
			$query = $db->query("SELECT author FROM $table_posts WHERE tid IN ($tids)");
			while($result = $db->fetch_array($query)) {
				$author = addslashes($result[author]);
				$usernames .= "$comma$author";
				$comma = ",";
			}
			updatemember("-", $usernames);

			$query = $db->query("SELECT attachment FROM $table_attachments WHERE tid IN ($tids)");
			while($attach = $db->fetch_array($query)) {
				@unlink("$attachdir/$attach[attachment]");
			}

			$db->query("DELETE FROM $table_threads WHERE tid IN ($tids) OR closed='moved|$tid'");
			$db->query("DELETE FROM $table_posts WHERE tid IN ($tids)");
			$db->query("DELETE FROM $table_attachments WHERE tid IN ($tids)");
			updateforumcount($fid);

			showmessage("指定主题删除成功，现在将转入主题列表。", "forumdisplay.php?fid=$fid&page=$page");

		}
	}		

} elseif($action == "delpost") {

	if(!is_array($delete) && !count($delete)) {
		showmessage("您没有选择要删除的贴子，请返回。");
	} else {
		if(!$delpostsubmit) {

			$query = $db->query("SELECT COUNT(*) FROM $table_posts WHERE tid='$tid'");
			if(count($delete) < $db->result($query, 0)) {

				preloader("topicadmin_delpost");

				$deleteid = "";
				foreach($delete as $id) {
					$deleteid .= "<input type=\"hidden\" name=\"delete[]\" value=\"$id\">\n";
				}

				eval("\$delpost = \"".template("topicadmin_delpost")."\";");
				echo $delpost;
				
			} else {
				header("Location: {$boardurl}topicadmin.php?action=delete&fid=$fid&tid=$tid");
			}

		} else {

			$pids = $comma = "";
			foreach($delete as $id) {
				$pids .= "$comma'$id'";
				$comma = ", ";
			}

			$usernames = $comma = "";
			$query = $db->query("SELECT author FROM $table_posts WHERE pid IN ($pids)");
			while($result = $db->fetch_array($query)) {
				$author = addslashes($result[author]);
				$usernames .= "$comma$author";
				$comma = ",";
			}
			updatemember("-", $usernames);

			$attach_type = "";
			$query = $db->query("SELECT pid, attachment, filetype FROM $table_attachments WHERE tid='$tid'");
			while($attach = $db->fetch_array($query)) {
				if(in_array($attach[pid], $delete)) {
					@unlink("$attachdir/$attach[attachment]");
				} else {
					$attach_type = substr(strrchr($attach[attachment], "."), 1)."\t".$attach[filetype];
				}
			}

			if($attach_type) {
				$db->query("UPDATE $table_threads SET attachment='$attach_type' WHERE tid='$tid'");
			}

			$db->query("DELETE FROM $table_posts WHERE pid IN ($pids)");
			$db->query("DELETE FROM $table_attachments WHERE pid IN ($pids)");
			updatethreadcount($tid);
			updateforumcount($fid);

			showmessage("指定贴子删除成功，现在将转入主题页面。", "viewthread.php?tid=$tid&page=$page");

		}
	}		

} elseif($action == "digist") {

	if(!$digistsubmit) {

		preloader("topicadmin_digist");
		$thread[digist] ? $removedigist = "<input type=\"radio\" name=\"level\" value=\"0\" checked> 取消精华 &nbsp; &nbsp; " : $digistcheck = "checked";
		eval("\$digist = \"".template("topicadmin_digist")."\";");
		echo $digist;

	} else {

		$db->query("UPDATE $table_threads SET digist='$level' WHERE tid='$tid'");
		if($digistcredits) {
			$db->query("UPDATE $table_members SET credit=credit".($level == 0 ? "-" : "+")."($digistcredits) WHERE username='$thread[author]'");
		}
		showmessage("{$cdbaction}成功，现在将转入主题列表。", "forumdisplay.php?fid=$fid");

	}

} elseif($action == "delete") {

	if(!$deletesubmit) {

		preloader("topicadmin_delete");
		eval("\$delete = \"".template("topicadmin_delete")."\";");
		echo $delete;

	} else {

		$usernames = $comma = "";
		$query = $db->query("SELECT author FROM $table_posts WHERE tid='$tid'");
		while($result = $db->fetch_array($query)) {
			$author = addslashes($result[author]);
			$usernames .= "$comma$author";
			$comma = ",";
		}
		updatemember("-", $usernames);

		$db->query("DELETE FROM $table_threads WHERE tid='$tid' OR closed='moved|$tid'");
		$db->query("DELETE FROM $table_posts WHERE tid='$tid'");
		$query = $db->query("SELECT attachment FROM $table_attachments WHERE tid='$tid'");
		while($thread_attachment = $db->fetch_array($query)) {
			@unlink("$attachdir/$thread_attachment[attachment]");
		}
		$db->query("DELETE FROM $table_attachments WHERE tid='$tid'");
		updateforumcount($fid);
		if ($forum[type] == "sub") {
			updateforumcount($fup[fid]);
		}

		showmessage("主题删除成功，现在将转入主题列表。", "forumdisplay.php?fid=$fid");

	}

} elseif($action == "close") {

	if(!$closesubmit) {
		preloader("topicadmin_openclose");
		eval("\$close = \"".template("topicadmin_openclose")."\";");
		echo $close;
	} else {
		$openclose = $thread[closed] ? 0 : 1;
		$db->query("UPDATE $table_threads SET closed='$openclose' WHERE tid='$tid' AND fid='$fid'");
		showmessage("成功{$cdbaction}，现在将转入主题列表。", "forumdisplay.php?fid=$fid");
	}

} elseif($action == "move") {

	if(!$movesubmit) {
		preloader("topicadmin_move");
		$forumselect = "<select name=\"moveto\">\n";
		$forumselect .= forumselect();
		$forumselect .= "</select>\n";
		eval("\$move = \"".template("topicadmin_move")."\";");
		echo $move;
	} else {

		if(!$moveto) {
			showmessage("您没有选择要移动到哪个论坛，请返回后修改。");
		}

		if($type == "normal") {
			$db->query("UPDATE $table_threads SET fid='$moveto' WHERE tid='$tid' AND fid='$fid'");
			$db->query("UPDATE $table_posts SET fid='$moveto' WHERE tid='$tid' AND fid='$fid'");
		} else {
			$db->query("INSERT INTO $table_threads (tid, fid, creditsrequire, icon, author, subject, dateline, lastpost, lastposter, views, replies, topped, digist, closed, pollopts, attachment)
				VALUES ('', '$thread[fid]', '$thread[creditsrequire]', '$thread[icon]', '$thread[author]', '$thread[subject]', '$thread[dateline]', '$thread[lastpost]', '$thread[lastposter]', '-', '-', '$thread[topped]', '$thread[digist]', 'moved|$thread[tid]', '', '')");

			$db->query("UPDATE $table_threads SET fid='$moveto' WHERE tid='$tid' AND fid='$fid'");
			$db->query("UPDATE $table_posts SET fid='$moveto' WHERE tid='$tid' AND fid='$fid'");
		}

		if ($forum[type] == "sub") {
			$query= $db->query("SELECT fup FROM $table_forums WHERE fid='$fid' LIMIT 1");
			$fup = $db->result($query, 0);
			updateforumcount($fup);
		}

		updateforumcount($moveto);
		updateforumcount($fid);
		showmessage("主题已成功移动，现在将转入主题列表。", "forumdisplay.php?fid=$fid");
	}

} elseif($action == "top") {

	if(!$topsubmit) {

		preloader("topicadmin_topuntop");

		$thread[topped] ? $untop = "<input type=\"radio\" name=\"level\" value=\"0\" checked> 取消置顶 &nbsp; &nbsp; " : $topcheck = "checked";
		eval("\$top = \"".template("topicadmin_topuntop")."\";");
		echo $top;

	} else {

		$db->query("UPDATE $table_threads SET topped='$level' WHERE tid='$tid' AND fid='$fid'");
		showmessage("{$cdbaction}成功，现在将转到主题列表。", "forumdisplay.php?fid=$fid");

	}

} elseif($action == "getip") {

	preloader("topicadmin_getip");
	$query = $db->query("SELECT useip FROM $table_posts WHERE pid='$pid' AND tid='$tid'");
	$useip = $db->result($query, 0);
	$iplocation = convertip($useip);

	if($isadmin) {
		$ipnew = explode(".", $useip);
		$ipban = "<input type=\"hidden\" name=\"ip1new\" value=\"$ipnew[0]\"><input type=\"hidden\" name=\"ip2new\" value=\"$ipnew[1]\">\n".
			"<input type=\"hidden\" name=\"ip3new\" value=\"$ipnew[2]\"><input type=\"hidden\" name=\"ip4new\" value=\"$ipnew[3]\">\n".
			"<input type=\"submit\" name=\"ipbansubmit\" value=\"禁止此 IP\">";
	} else {
		$ipban = "<input type=\"button\" name=\"goback\" value=\"返回上一页\" onclick=\"history.go(-1)\">";
	}

	eval("\$getip = \"".template("topicadmin_getip")."\";");
	echo $getip;

} elseif($action == "bump") {

	if(!$bumpsubmit) {
		preloader("topicadmin_bump");
		eval("\$bump = \"".template("topicadmin_bump")."\";");
		echo $bump;
	} else {
		$query = $db->query("SELECT subject, lastposter, lastpost FROM $table_threads WHERE tid='$tid' LIMIT 0, 1");
		$thread = $db->fetch_array($query);
		$thread[lastposter] = addslashes($thread[lastposter]);
		$db->query("UPDATE $table_threads SET lastpost='$timestamp' WHERE tid='$tid' AND fid='$fid'");
		$db->query("UPDATE $table_forums SET lastpost='$thread[subject]\t$timestamp\t$thread[lastposter]' WHERE fid='$fid' $fupadd");

		showmessage("主题已成功提升，现在将转到主题列表。", "forumdisplay.php?fid=$fid");
	}

} elseif($action == "split") {

	if(!$splitsubmit) {

		preloader("topicadmin_split");

		if($replies <= 0) {
			showmessage("这个主题没有回复，无法分割，请返回。");
		}

		$replies = $thread[replies];

		$query = $db->query("SELECT * FROM $table_posts WHERE tid='$tid' ORDER BY dateline");
		while($post = $db->fetch_array($query)) {
			$bbcodeoff = $post[bbcodeoff];
			$smileyoff = $post[smileyoff];
			$post[subject] = censor($post[subject]);
			$post[message] = postify($post[message], $smileyoff, $bbcodeoff, $parseurloff, $fid, $bordercolor, "", "", $table_words, $table_forums, $table_smilies);
			eval("\$posts .= \"".template("topicadmin_split_row")."\";");

		}
		eval("\$split = \"".template("topicadmin_split")."\";");
		echo $split;

	} else {

		if($subject == "" || ereg("^ *$", $subject)) {
			showmessage("您没有输入标题，请返回填写。");
		}

		$query = $db->query("SELECT author, subject FROM $table_posts WHERE tid='$tid' ORDER BY dateline LIMIT 0,1");
		$fpost = $db->fetch_array($query);
		$db->query("INSERT INTO $table_threads VALUES ('', '$fid', '$subject', '', '$fpost[author]', '$fpost[dateline]', '0', '0', '$fpost[author]', '', '', '', '')");
		$newtid = $db->insert_id();

		$pids = "";
		$or = "";
		$query = $db->query("SELECT pid FROM $table_posts WHERE tid='$tid'");
		while($post = $db->fetch_array($query)) {
			$split = "split$post[pid]";
			$split = "${$split}";
			if($split) {
				$pids .= " $or pid='$split'";
				$or = "OR";
			}
		}
		if($pids) {
			$pids = " WHERE $pids";
			$db->query("UPDATE $table_posts SET tid='$newtid' $pids");
			$db->query("UPDATE $table_attachments SET tid='$newtid' $pids");
			updatethreadcount($tid);
			updatethreadcount($newtid);
			updateforumcount($fid);
			showmessage("主题已成功分割，现在将转入主题列表。", "forumdisplay.php?fid=$fid");
		} else {
			showmessage("您没有选择要分割入新主题的贴子，请返回检查。");
		}
	}

} elseif($action == "merge") {

	if(!$mergesubmit) {
		preloader("topicadmin_merge");
		eval("\$merge = \"".template("topicadmin_merge")."\";");
		echo $merge;
	} else {
		$query = $db->query("SELECT fid, views, replies FROM $table_threads WHERE tid='$othertid'");
		$other = $db->fetch_array($query);
		$other[replies]++;

		$db->query("UPDATE $table_posts SET tid='$tid' WHERE tid='$othertid'");
		$postsmerged = $db->affected_rows();

		$db->query("UPDATE $table_attachments SET tid='$tid' WHERE tid='$othertid'");
		$db->query("DELETE FROM $table_threads WHERE tid='$othertid' OR closed='moved|$othertid'");
		$db->query("UPDATE $table_threads SET views=views+$other[views], replies=replies+$other[replies] WHERE tid='$tid'");
		
		if($fid == $other[fid]) {
			$db->query("UPDATE $table_forums SET threads=threads-1 WHERE fid='$fid' $fupadd");
		} else {
			$db->query("UPDATE $table_forums SET threads=threads-1, posts=posts-$postsmerged WHERE fid='$other[fid]'");
			$db->query("UPDATE $table_forums SET posts=$posts+$postsmerged WHERE fid='$fid' $fupadd");
		}

		showmessage("主题已成功合并，现在将转入主题列表。", "forumdisplay.php?fid=$fid");
	}

} elseif($action == "karma") {

	if(!$allowkarma || !$maxkarmavote) {
		showmessage("对不起，您的级别〔{$grouptitle}〕无法参与评分。");
	}

	$offset = ceil($maxkarmavote / 6);
	$minkarmavote = $offset - $maxkarmavote;
	if($score < $minkarmavote || $score > $maxkarmavote) {
		showmessage("您的给分超过 $minkarmavote 到 $maxkarmavote 的范围限制。");
	}

	if($username == $cdbuser) {
		showmessage("对不起，您不能给自己发表的贴子评分。");
	}

	$lastkarmatime = $_COOKIE[lastkarmatime] ? $_COOKIE[lastkarmatime] : $CDB_SESSION_VARS[lastkarmatime];
	if($karmactrl && !$ismoderator && $timestamp - $lastkarmatime < $karmactrl) {
		showmessage("对不起，在 $karmactrl 秒内您只能进行一次评分，请返回。");
	}

	if(!$karmasubmit) {
		preloader("topicadmin_karma");
		$username = stripslashes($username);
		$encodename = rawurlencode($username);
		eval("\$karma = \"".template("topicadmin_karma")."\";");
		echo $karma;
	} else {
		$score = intval($score);
		if($score > 0) {
			$score1 = "+'".$score."'";
			$addscore = "增加";
		} else {
			$score = - $score;
			$score1 = "-'".$score."'";
			$addscore = "减少";
		}
		$db->query("UPDATE $table_members SET credit=credit$score1 WHERE username='$username'");

		setcookie("lastkarmatime", $timestamp, $timestamp + 86400, $cookiepath, $cookiedomain);
		$CDB_SESSION_VARS[lastkarmatime] = $timestamp;

		showmessage("感谢您的参与，".stripslashes($username)." 的$credittitle$addscore"."了 $score $creditunit"."。<br>现在将转入主题页面。", "viewthread.php?tid=$tid");
	}

} elseif($action == "report") {

	if(!$reportpost) {
		showmessage("对不起，管理员关闭了报告贴子功能，请返回。");
	}

	if(!$cdbuser) {
		showmessage("您还没有登录，无法使用报告贴子功能。");
	}

	if(!$reportsubmit) {
		preloader("topicadmin_report");
		eval("\$report = \"".template("topicadmin_report")."\";");
		echo $report;
	} else {
		if($pid) {
			$posturl = "{$boardurl}viewthread.php?tid=$tid#pid$pid";
		} else {
			$posturl = "{$boardurl}viewthread.php?tid=$tid";
		}

		$message = "有用户向您反映下面这个贴子，请查看：$posturl\n\n原因：$reason";

		$mods = explode(",", $forum[moderator]);
		foreach($mods as $moderator) {
			$moderator = trim($moderator);
			if($moderator) {
				$db->query("INSERT INTO $table_u2u VALUES('', '$moderator', '$cdbuser', '$timestamp', '报告贴子……', '$message', 'inbox', '1')");
			}
		}

		$query = $db->query("SELECT username FROM $table_members WHERE status='论坛管理员'");
		while($member = $db->fetch_array($query)) {
			if($member[username]) {
				$db->query("INSERT INTO $table_u2u VALUES('', '$member[username]', '$cdbuser', '$timestamp', '报告贴子……', '$message', 'inbox', '1')");
			}
		}

		showmessage("您的意见已经报告给版主和管理员，感谢您的参与。现在将转入主题页面。", "viewthread.php?tid=$tid");
	}

} elseif($action == "votepoll") {

	$pollops = explode("#|#", $currpoll);
	for($pnum = 0; $pnum < 10; $pnum++) {
		if(!strstr($pollops[$pnum], "||~|~||")) {
			$oldips .= $pollops[$pnum];
		}

		$thispoll = explode("||~|~|| ", $pollops[$pnum]);
		if($pnum == $postopnum) {
			$thispoll[1]++;
		}

		if($pollops[$pnum] != "" && substr($pollops[$pnum],0,1)!=" ") {
			$newvotecol .= "$thispoll[0]||~|~|| $thispoll[1]#|#";
			$thispoll = "";
		}
	}

	if($newvotecol && $cdbuser) {
		$newvotecol .= "$oldips $cdbuser";
		$db->query("UPDATE $table_threads SET pollopts='$newvotecol' WHERE fid='$fid' AND tid='$tid'");
		showmessage("投票已经提交，感谢您的参与，现在将转入主题页。", "viewthread.php?tid=$tid");
	} else {
		showmessage("对不起，您还没有登录。");
	}

} elseif($action == "emailfriend" && $tid) {

	if(!$sendsubmit) {
		preloader("topicadmin_emailfriend");
		$threadurl = "{$boardurl}viewthread.php?tid=$tid";

		if(!$cdbuser) {
			$query = $db->query("SELECT * FROM $table_members WHERE username='$cdbuser'");
			$username = $db->fetch_array($query);
			$email = $username[email];
		}
		eval("\$emailfriend = \"".template("topicadmin_emailfriend")."\";");
		echo $emailfriend;
	} else {
		if($message == "") {
			$message = "你好！我在 $bbname [ $boardurl ]\n看到了这篇贴子，认为很有价值，特推荐给你。\n\n地址 $threadurl\n\n希望你能喜欢。";
		}
		if($subject == "") {
			$subject = "推荐：$thread[subject]";
		}
		if(!$fromname) {
			showmessage("您没有输入您的名字，请返回后填写完整。");
		}
		if(!$fromemail) {
			showmessage("您没有输入您的 Email 地址，请返回后填写完整。");
		}
		if(!$sendtoname) {
			showmessage("您没有输入接收人姓名，请返回后填写完整。");
		}
		if(!$sendtoemail) {
			showmessage("您没有输入接收人 Email，请返回后填写完整。");
		}

		sendmail($sendtoemail, $subject, $message, "From: $fromname <$fromemail>");

		showmessage("您的推荐已经通过 Email 发给朋友，现在将转入原贴。", "viewthread.php?tid=$tid");
	}

} else {

	$useraction = "未定义操作 [TOPICADMIN]";
	showmessage("未定义操作，请返回。");

}

if($action != "votepoll" && $action != "karma" && $action != "report" && $action != "emailfriend") {
	@$fp = fopen("./datatemp/modslog.php", "a");
	@flock($fp, 3);
	@fwrite($fp, "$cdbuser\t$status\t$onlineip\t$timestamp\t$fid\t$forum[name]\t$tid\t$threadname\t$modaction\n");
	@fclose($fp);
}

gettotaltime();
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();
?>