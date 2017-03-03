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
$ismoderator = modcheck($cdbuser);

if($action == "attachment" && $aid)
{
	$query = $db->query("SELECT a.*, t.fid FROM $table_attachments a LEFT JOIN $table_threads t ON a.tid=t.tid WHERE aid='$aid'");
	$attach = $db->fetch_array($query);
	if($allowgetattach && $attach[creditsrequire] && $attach[creditsrequire] > $credit && !$ismoderator) {
		showmessage("对不起，本附件要求{$credittitle}高于 $attach[creditsrequire] {$creditunit}才可下载，请返回。");
	}
	$query = $db->query("SELECT * FROM $table_forums WHERE fid='$attach[fid]'");
	$forum = $db->fetch_array($query);
	if(!$forum[getattachperm] && !$allowgetattach) {
		showmessage("对不起，您的级别〔{$grouptitle}〕无法下载附件。");
	} elseif($forum[getattachperm] && !strstr($forum[getattachperm], "\t$groupid\t")) {
		showmessage("对不起，只有特定用户可以下载本论坛的附件，请返回。");
	}

	$db->query("UPDATE $table_attachments SET downloads=downloads+1 WHERE aid='$aid'");
	$filename = "$attachdir/$attach[attachment]";
	$filesize = filesize($filename);
	if(is_readable($filename) && $attach[attachment]) {
		header("Content-Disposition: filename=$attach[filename]");
		header("Content-Length: $filesize");
		header("Content-Type: $attach[filetype]");
		header("Pragma: no-cache");
		header("Expires: 0");
		@$fp = fopen($filename, "rb");
		@flock($fp, 3);
		$attachment = @fread($fp, $filesize);
		@fclose($fp);
		echo $attachment;
		ob_end_flush();
	} else {
		showmessage("附件文件不存在或无法读入，请与管理员联系。");
	}
	cdbexit();
}

if($goto == "lastpost")
{
	if($highlight) {
		$highlight = "&highlight=".rawurlencode($highlight);
	}
	if($tid) {
		$query = $db->query("SELECT p.pid, p.dateline, t.tid, t.replies FROM $table_threads t, $table_posts p WHERE p.tid=t.tid AND t.tid='$tid' ORDER BY p.dateline DESC LIMIT 0,1");
		if($post = $db->fetch_array($query)) {
			if(($post[replies] + 1) > $ppp) {
				$page = "&page=".ceil(($post[replies] + 1) / $ppp);
			}
			header("Location: {$boardurl}viewthread.php?tid=$post[tid]&pid=$post[pid]$page&sid=$sid#pid$post[pid]$highlight");
			cdbexit();
		}
	}
	if($fid) {
		$query = $db->query("SELECT p.pid, p.dateline, t.tid, t.replies FROM $table_threads t, $table_posts p WHERE p.tid=t.tid AND t.fid='$fid' ORDER BY p.dateline DESC LIMIT 0,1");
		if($post = $db->fetch_array($query)) {
			if(($post[replies] + 1) > $ppp) {
				$page = "&page=".ceil(($post[replies] + 1) / $ppp);
			}
			header("Location: {$boardurl}viewthread.php?tid=$post[tid]&pid=$post[pid]$page&sid=$sid#pid$post[pid]");
			cdbexit();
		}
	}
}
elseif($goto == "nextnewset")
{
	if($fid && $tid) {
		$query = $db->query("SELECT lastpost FROM $table_threads WHERE tid='$tid'");
		$this_lastpost = $db->result($query, 0);
		$query = $db->query("SELECT tid FROM $table_threads WHERE fid='$fid' AND lastpost>'$this_lastpost' ORDER BY lastpost ASC LIMIT 0, 1");
		if($next = $db->fetch_array($query)) {
			header("Location: {$boardurl}viewthread.php?tid=$next[tid]&sid=$sid");
			cdbexit();
		} else {
			showmessage("没有比当前更新的主题，请返回。");
		}
	} else {
		showmessage("您没有指定当前主题及板块的 id，无法转入下一主题。");
	}
}
elseif($goto == "nextoldset")
{
	if($fid && $tid) {
		$query = $db->query("SELECT lastpost FROM $table_threads WHERE tid='$tid'");
		$this_lastpost = $db->result($query, 0);
		$query = $db->query("SELECT tid FROM $table_threads WHERE fid='$fid' AND lastpost<'$this_lastpost' ORDER BY lastpost DESC LIMIT 0, 1");
		if($last = $db->fetch_array($query)) {
			header("Location: {$boardurl}viewthread.php?tid=$last[tid]&sid=$sid");
			cdbexit();
		} else {
			showmessage("没有比当前更早的主题，请返回。");
		}
	} else {
		showmessage("您没有指定当前主题及板块的 id，无法转入上一主题。");
	}
}

$codecount = 0;
$oldtopics = $_COOKIE[oldtopics];
if(!strstr($oldtopics, "\t$tid\t")) {
	$oldtopics .= $oldtopics ? "$tid\t" : "\t$tid\t";
	setcookie("oldtopics", $oldtopics, $timestamp + 86400, $cookiepath, $cookiedomain);
}

$query = $db->query("SELECT * FROM $table_threads WHERE tid='$tid'");
if(!$thread = $db->fetch_array($query)) {
	showmessage("指定的主题不存在或已被删除，请返回。");
}
$thread[subject] = censor($thread[subject]);

$useraction = "浏览贴子『$thread[subject]』";
if($forum[type] == "forum") {
	$navigation .= "&raquo; <a href=\"forumdisplay.php?fid=$fid\"> $forum[name]</a> &raquo; $thread[subject]";
	$navtitle .= " - $forum[name] - $thread[subject]";
} else {
	$query = $db->query("SELECT fid, name FROM $table_forums WHERE fid='$forum[fup]'");
	$fup = $db->fetch_array($query);
	$navigation .= "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> &raquo; <a href=\"forumdisplay.php?fid=$fid\"> $forum[name]</a> &raquo; $thread[subject]";
	$navtitle .= " - $fup[name] - $forum[name] - $thread[subject]";
}

if(!$forum[viewperm] && !$allowview) {
	showmessage("对不起，您的级别〔{$grouptitle}〕无法浏览贴子。");
} elseif($forum[viewperm] && !strstr($forum[viewperm], "\t$groupid\t")) {
	showmessage("对不起，本论坛只有特定用户可以浏览，请返回。");
} elseif($thread[creditsrequire] && $thread[creditsrequire] > $credit && !$ismoderator) {
	showmessage("对不起，本贴要求{$credittitle}高于 $thread[creditsrequire] {$creditunit}才可浏览，请返回。");
}

if($forum[password] != $_COOKIE["fidpw$fid"] && $forum[password] != $CDB_SESSION_VARS["fidpw$fid"] && $forum[password]) {
	header("Location: {$boardurl}forumdisplay.php?fid=$fid&sid=$sid");
	cdbexit();
}

if(!$action && $tid) {

	$tplnames = "css,header,footer,viewthread_post,viewthread_modoptions,viewthread_post_sig,viewthread_forumjump,viewthread";
	preloader($fastpost ? ",viewthread_fastpost" : NULL);

	$newpolllink = $allowpostpoll ? "&nbsp;<a href=\"post.php?action=newthread&fid=$fid&poll=yes\"><img src=\"$imgdir/poll.gif\" border=\"0\"></a>" : NULL;
	$replylink = "&nbsp;<a href=\"post.php?action=reply&fid=$fid&tid=$tid\"><img src=\"$imgdir/reply.gif\" border=\"0\"></a>";
	$nohllink = str_replace("+", "", $highlight) ? "<a href=\"viewthread.php?tid=$tid&page=$page\" style=\"color: $headertext;font-weight: normal\">取消高亮</a> " : NULL;
	$setdigist = $thread[digist] ? "取消精华" : "加入精华";
	$topuntop = $thread[topped] ? "解除置顶" : "主题置顶";

	if($thread[closed]) {
		if(!$ismoderator) {
			$replylink = "";
		}
		$closeopen = "打开主题";
	} else {
		$closeopen = "关闭主题";
	}

	if($allowkarma && $maxkarmavote) {
		$offset = ceil($maxkarmavote / 6);
		$karmabox = "&nbsp;<select name=\\\"fid\\\" id=\\\"fid\\\" onchange=\\\"if(this.options[this.selectedIndex].value != '') {\n"
			."window.location=('topicadmin.php?action=karma&tid=\$tid&username=\$encodename&score='+this.options[this.selectedIndex].value+'&sid=$sid') }\\\" align=\\\"absmiddle\\\">\n"
			."<option value=\\\"\\\">评分</option>\n"
			."<option value=\\\"\\\">----</option>\n";
		for($vote = - $maxkarmavote + $offset; $vote <= $maxkarmavote; $vote += $offset) {
			$votenum = $vote > 0 ? "+$vote" : $vote;
			$karmabox .= $vote ? "<option value=\\\"$vote\\\">$votenum</option>\n" : NULL;
		}
		$karmabox .= "</select>\n";
	}

	if($page) {
		$start_limit = ($page-1) * $ppp;
	} else {
		$start_limit = 0;
		$page = 1;
	}

	$db->query("UPDATE $table_threads SET views=views+1 WHERE tid='$tid'");
	$num = $thread[replies] + 1;

	$mpurl = "viewthread.php?tid=$tid&highlight=".rawurlencode($highlight);
	$multipage = multi($num, $ppp, $page, $mpurl);
	$creditsrequire = $thread[creditsrequire] ? " &nbsp; 浏览本贴需{$credittitle} <span class=\"bold\">$thread[creditsrequire]</span> $creditunit" : NULL;

	if($thread[pollopts]) {
		loadtemplates("viewthread_poll_options_view,viewthread_poll_options,viewthread_poll_submitbutton,viewthread_poll");
		$thread[pollopts] = stripslashes(censor($thread[pollopts]));
		$thread[pollopts] = str_replace("\n", "", $thread[pollopts]);
		$pollops = explode("#|#", $thread[pollopts]);

		if(strstr($thread[pollopts]." ", " $cdbuser ")) {
			for($pnum = 0; $pnum < 10; $pnum++) {
				if($pollops[$pnum] != "" && substr($pollops[$pnum],0,1)!=" ") {
					$thispollnum = eregi_replace(".*\|\|~\|~\|\| ", "", $pollops[$pnum]);
					$totpollvotes += $thispollnum;
				}
			}
			for($pnum = 0; $pnum < 10; $pnum++) {
				if($pollops[$pnum] != "" && substr($pollops[$pnum], 0, 1) != " ") {
					$thispoll = explode("||~|~|| ", $pollops[$pnum]);

					if($totpollvotes != 0) {
						$thisnum = $thispoll[1] * 100 / $totpollvotes;
					} else {
						$thisnum = "0";
					}

					if($thisnum != "0") {
						$thisnum = round($thisnum, 2);
						$pollimgnum = round($thisnum) / 3;
						for($num = 0; $num < $pollimgnum; $num++) {
							$pollbar .= "<img src=\"$imgdir/pollbar.gif\">";
						}
					}

					$thisnum .= "%";

					if($thisnum == "0%") {
						$pollbar = "";
					}
					eval("\$pollhtml .= \"".template("viewthread_poll_options_view")."\";");
					$pollbar = "";
				}
			}
		} else {
			$checked = "checked";
			for($pnum = 0; $pnum < 10; $pnum++) {
				if($pollops[$pnum] != "" && substr($pollops[$pnum], 0, 1)!=" ") {
					$thispoll = explode("||~|~|| ", $pollops[$pnum]);
					eval("\$pollhtml .= \"".template("viewthread_poll_options")."\";");
					$checked = "";
				}
			}
		}

		if(strstr($thread[pollopts]." ", " $cdbuser ")) {
			$buttoncode = "";
		} else {
			$buttoncode = "<br><center><input type=\"submit\" value=\"提交投票\"></center>";
		}
		eval("\$poll = \"".template("viewthread_poll")."\";");
	}

	$thisbg = $altbg2;
	$attachments = $comma = "";
	$querypost = $db->query("SELECT p.*, m.* FROM $table_posts p LEFT JOIN $table_members m ON m.username=p.author WHERE p.tid='$tid' ORDER BY dateline LIMIT $start_limit, $ppp");
	while($post = $db->fetch_array($querypost)) {
		$poston = gmdate("$dateformat $timeformat", $post[dateline] + ($timeoffset * 3600));
		$post[icon] = $post[icon] ? "<img src=\"$smdir/$post[icon]\" align=\"absmiddle\">" : "<img src=\"$imgdir/lastpost.gif\" align=\"absmiddle\">";
		if($post[author] != "游客" && $post[username]) {
			$email = $post[showemail] ? "<a href=\"mailto:$post[email]\"><img src=\"$imgdir/email.gif\" border=\"0\" alt=\"发送邮件\"></a>&nbsp;" : "";
			$personstatus = $vtonlinestatus ? $timestamp - $post[lastvisit] <= $onlinehold ? "状态 <b>Online</b><br>" : "状态 Offline<br>" : "";
			if($post[site] == "") {
				$site = "";
			} else {
				$post[site] = "http://".str_replace("http://", "", $post[site]);
				$site = "<a href=\"$post[site]\" target=\"_blank\"><img src=\"$imgdir/site.gif\" border=\"0\" alt=\"访问主页\"></a>&nbsp;";
			}
			$encodename = rawurlencode($post[author]);

			$icq = $post[icq] ? "<a href=http://wwp.icq.com/scripts/search.dll?to=$post[icq]><img src=\"http://web.icq.com/whitepages/online?icq=$post[icq]&img=5\" alt=\"$post[author] 的 ICQ 状态\" border=\"0\"></a>&nbsp;" : "";
			$oicq = $post[oicq] ? "<a href=http://search.tencent.com/cgi-bin/friend/user_show_info?ln=$post[oicq]><img src=\"$imgdir/oicq.gif\" alt=\"$post[author] 的 OICQ\" border=\"0\"></a>&nbsp;" : "";
			$yahoo = $post[yahoo] ? "<a href=http://edit.yahoo.com/config/send_webmesg?.target=$post[yahoo]&.src=pg><img src=\"$imgdir/yahoo.gif\" alt=\"$post[author] 的 Yahoo\" border=\"0\"></a>&nbsp;" : "";

			$search = "<a href=\"misc.php?action=search&srchuname=$encodename&srchfid=all&srchfrom=0&searchsubmit=yes\"><img src=\"$imgdir/find.gif\" border=\"0\" alt=\"搜索该用户的全部贴子\"></a>&nbsp;";
			$profile = "<a href=\"member.php?action=viewpro&username=$encodename\"><img src=\"$imgdir/profile.gif\" border=\"0\" alt=\"查看资料\"></a>&nbsp;";
			$u2u = "<a href=\"###\" onclick=\"Popup('u2u.php?action=send&username=$encodename&sid=$sid', 'Window', 600, 500);\"><img src=\"$imgdir/u2u.gif\" border=\"0\" alt=\"发短消息\"></a>&nbsp;";

			unset($groupinfo, $groupstars, $stars);
			foreach($CDB_CACHE_VARS[usergroups] as $usergroup) {
				if((stristr($usergroup[specifiedusers], "\t".addslashes($post[author])."\t") || ($post[status] == $usergroup[status] && $usergroup[status] != "正式会员")) && !$usergroup[creditshigher] && !$usergroup[creditslower]) {
					if($groupstars < $usergroup[stars]) {
						$groupstars = $usergroup[stars];
					}
					$groupinfo = $usergroup;
				} elseif($post[credit] >= $usergroup[creditshigher] && $post[credit] < $usergroup[creditslower]) {
					if($post[status] == $usergroup[status] && !$groupinfo) {
						$groupstars = $usergroup[stars];
						$groupinfo = $usergroup;
					} elseif($groupstars < $usergroup[stars]) {
						$groupstars = $usergroup[stars];
					}
					if($groupinfo) {
						break;
					}
				}
			}

			$showtitle = $post[customstatus] ? $post[customstatus] : $groupinfo[grouptitle];
			for($i = 0; $i < $groupstars; $i++) {
				$stars .= "<img src=\"$imgdir/star.gif\">";
			}

			$tharegdate = gmdate("$dateformat", $post[regdate] + ($timeoffset * 3600));
			$stars .= "<br>";

			$avatar = "<br>";
			if($groupinfo[allowavatar]) {
				if($groupinfo[groupavatar]) {
					$avatar = $avatar = "<table width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"table-layout: fixed\">\n<tr><td align=\"center\">".image($groupinfo[groupavatar])."</td></tr></table>";
				} elseif($post[avatar]) {
					$avatar = "<table width=\"95%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"table-layout: fixed\">\n<tr><td align=\"center\">".image($post[avatar])."</td></tr></table>";
				}
			}

			$location = $post[location] ? "<br>来自 $post[location]" : "";
		} else {
			if($post[author] == "游客") {
				$showtitle = "未注册<br>";
			} elseif(!$post[username]) {
				$showtitle = "该用户已被删除";
			}
			$post[postnum] = $post[credit] = $tharegdate = "N/A";
			$personstatus = $stars = $avatar = $email = $site = $icq
				= $oicq = $yahoo = $profile = $search = $u2u = $location = "";
		}

		if($ismoderator) {
			$ip = "<a href=\"topicadmin.php?action=getip&fid=$fid&tid=$tid&pid=$post[pid]\"><img src=\"$imgdir/ip.gif\" border=\"0\" align=\"right\" alt=\"获取 IP\"></a>";
			$delete = "<input type=\"checkbox\" name=\"delete[]\" value=\"$post[pid]\">";
		} else {
			$ip = $delete = "";
		}

		$repquote = !$thread[closed] || $ismoderator ? "&nbsp;<a href=\"post.php?action=reply&fid=$fid&tid=$tid&repquote=$post[pid]\"><img src=\"$imgdir/quote.gif\" border=\"0\" alt=\"引用回复\"></a>" : "";
		$reportlink = ($cdbuser && $reportpost) ? "&nbsp;<a href=\"topicadmin.php?action=report&fid=$fid&tid=$tid&pid=$post[pid]\"><img src=\"$imgdir/report.gif\" border=\"0\" alt=\"向版主反应这个贴子\"></a>" : "";
		$edit = "&nbsp;<a href=\"post.php?action=edit&fid=$fid&tid=$tid&pid=$post[pid]&page=$page\"><img src=\"$imgdir/edit.gif\" border=\"0\" alt=\"编辑贴子\"></a>";

		if($allowkarma && $maxkarmavote) {
			eval("\$karma = \"$karmabox\";");
		}

		if($post[subject]) {
			$post[subject] = "$post[subject]<br><br>";
		}

		$post[subject] = censor($post[subject]);
		$post[message] = postify($post[message], $post[smileyoff], $post[bbcodeoff], $post[parseurloff], $forum[allowsmilies], $forum[allowhtml], $forum[allowbbcode], $forum[allowimgcode]);

		if($post[aid]) {
			$post[message] .= "\tCDB_ATTACHMENT_LACK_$post[aid]\t";
			$attachments .= "$comma'$post[aid]'";
			$comma = ", ";
		}

		if($thisbg == $altbg2) {
			$thisbg = $altbg1;
			$sigline = $altbg2;
		} else {
			$thisbg = $altbg2;
			$sigline = $altbg1;
		}

		if($post[usesig] && $post[signature]) {
			$post[signature] = postify($post[signature], "", "", "", 0, 0, $groupinfo[allowsigbbcode], $groupinfo[allowsigimgcode]);
			eval("\$post[message] .= \"".template("viewthread_post_sig")."\";");
		}

		eval("\$posts .= \"".template("viewthread_post")."\";");
	}

	if($attachments) {
		loadtemplates("viewthread_post_attachmentimage,viewthread_post_attachmentswf,viewthread_post_attachment");
		$query = $db->query("SELECT * FROM $table_attachments WHERE aid IN ($attachments)");
		while($postattach = $db->fetch_array($query)) {
			$extension = strtolower(substr(strrchr($postattach[filename],"."),1));
			$attachicon = attachicon(substr(strrchr($postattach[attachment], "."), 1)."\t".$postattach[filetype]);
			if($attachimgpost && ($extension == "jpg" || $extension == "jpeg" || $extension == "jpe" || $extension == "gif" || $extension == "png" || $extension == "bmp")) {
				eval("\$attacharray[$postattach[aid]] = \"".template("viewthread_post_attachmentimage")."\";");
			} elseif($attachimgpost && $extension == "swf") {
				eval("\$attacharray[$postattach[aid]] = \"".template("viewthread_post_attachmentswf")."\";");
			} else {
				$attachsize = sizecount($postattach[filesize]);
				$downloadcount = $postattach[downloads];
				$creditrequire = $postattach[creditsrequire] ? "，下载需$credittitle $postattach[creditsrequire] $creditunit" : NULL;
				eval("\$attacharray[$postattach[aid]] = \"".template("viewthread_post_attachment")."\";");
			}
		}
		if(is_array($attacharray)) {
			foreach($attacharray as $aid => $attachment) {
				$posts = str_replace("\tCDB_ATTACHMENT_LACK_".$aid."\t", $attachment, $posts);
			}
		}		
	}

	if($ismoderator) {
		eval("\$modoptions = \"".template("viewthread_modoptions")."\";");
	} else {
		$modoptions = "";
	}

	$forumselect = forumselect();
	eval("\$forumjump = \"".template("viewthread_forumjump")."\";");

	if($fastpost && (!$thread[closed] || $ismoderator) && ((!$forum[postperm] && $allowpost) || ($forum[postperm] && strstr($forum[postperm], "\t$groupid\t")))) {
		$usesigcheck = $signature ? "checked" : NULL;
		eval("\$fastpost_viewthread = \"".template("viewthread_fastpost")."\";");
	}
	eval("\$viewthread = \"".template("viewthread")."\";");
	echo $viewthread;

	gettotaltime();
	eval("\$footer = \"".template("footer")."\";");
	echo $footer;

} elseif($action == "printable" && $tid) {

	loadtemplates("viewthread_printable_attachmentimage,viewthread_printable_attachment,viewthread_printable_row,viewthread_printable");
	$querypost = $db->query("SELECT * FROM $table_posts WHERE fid='$fid' AND tid='$tid' ORDER BY dateline");
	while($post = $db->fetch_array($querypost)) {

		$poston = gmdate("$dateformat $timeformat", $post[dateline] + ($timeoffset * 3600));

		$thisbg = "#FFFFFF";
		$post[subject] = censor($post[subject]);
		$post[message] = postify($post[message], $post[smileyoff], $post[bbcodeoff], $post[parseurloff], $forum[allowsmilies], $forum[allowhtml], $forum[allowbbcode], $forum[allowimgcode]);

		if($post[aid]) {
			$query = $db->query("SELECT * FROM $table_attachments WHERE aid='$post[aid]'");
			$postattach = $db->fetch_array($query);
			$extension = strtolower(substr(strrchr($postattach[filename],"."),1));
			if($attachimgpost && ($extension == "jpg" || $extension == "jpeg" || $extension == "jpe" || $extension == "gif" || $extension == "png" || $extension == "bmp")) {
				$attachicon = attachicon(substr(strrchr($postattach[attachment], "."), 1)."\t".$postattach[filetype]);
				eval("\$post[message] .= \"".template("viewthread_printable_attachmentimage")."\";");
			} else {
				$attachsize = sizecount($postattach[filesize]);
				$attachicon = attachicon(substr(strrchr($postattach[attachment], "."), 1)."\t".$postattach[filetype]);
				$creditsrequire = $postattach[creditsrequire] ? " / $credittitle $postattach[creditsrequire] $creditunit" : NULL;
				eval("\$post[message] .= \"".template("viewthread_printable_attachment")."\";");
			}
		}

		eval("\$posts .= \"".template("viewthread_printable_row")."\";");
	}
	eval("\$printable = \"".template("viewthread_printable")."\";");
	echo $printable;

} else {

	showmessage("未定义操作，请返回。");

}
cdb_output();
?>