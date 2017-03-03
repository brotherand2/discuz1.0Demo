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
$aid = 0;

if($action) {
	if($tid && $fid) {
		$query = $db->query("SELECT * FROM $table_threads WHERE tid='$tid'");
		$thread = $db->fetch_array($query);
		$threadname = $thread[subject];
		$fid = $thread[fid];
	}

	if($tid) {
		if($forum[type] == "forum") {
			$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> &raquo; <a href=\"viewthread.php?tid=$tid\">$threadname</a>";
			$navtitle = " - $forum[name] - $threadname";
		} else {
			$query = $db->query("SELECT name, fid FROM $table_forums WHERE fid='$forum[fup]'");
			$fup = $db->fetch_array($query);
			$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> &raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a> &raquo; <a href=\"viewthread.php?tid=$tid\">$threadname</a>";
			$navtitle = " - $fup[name] - $forum[name] - $threadname";
		}
		if($action == "reply") {
			$navigation .= " &raquo; 回复主题";
			$navtitle .= " - 回复主题";
			$tplnames .= ($replysubmit && !$previewpost) ? NULL : ",post_reply_review_toolong,post_reply_review_post,post_attachmentbox,post_reply";
			$useraction = "回复主题『{$threadname}』";
		} elseif($action == "edit") {
			$navigation .= " &raquo; 编辑贴子";
			$navtitle .= " - 编辑贴子";
			$tplnames .= ($editsubmit && !$previewpost) ? NULL : ",post_viewpermission,post_edit_attachmentbox_edit,post_edit_attachmentbox,post_edit";
			$useraction = "编辑主题『{$threadname}』下的贴子";
		}
	} else {
		if($action != "edit" && !$tid) {
			if($forum[type] == "forum") {
				$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a>";
				$navtitle = " - $forum[name]";
			} else {
				$query = $db->query("SELECT name, fid FROM $table_forums WHERE fid='$forum[fup]'");
				$fup = $db->fetch_array($query);
				$navigation = "&raquo; <a href=\"forumdisplay.php?fid=$fup[fid]\">$fup[name]</a> &raquo; <a href=\"forumdisplay.php?fid=$fid\">$forum[name]</a>";
				$navtitle = " - $fup[name] - $forum[name]";
			}
		}
		if($action == "newthread") {
			if($poll) {
				$navigation .= " &raquo; 发起投票";
				$navtitle .= " - 发起投票";
				$tplnames .= ($topicsubmit && !$previewpost) ? NULL : ",post_attachmentbox,post_viewpermission,post_newpoll";
				$useraction = "在『$forum[name]』发起投票";
			} else {
				$navigation .= " &raquo; 发新话题";
				$navtitle .= " - 发新话题";
				$tplnames .= ($topicsubmit && !$previewpost) ? NULL : ",post_attachmentbox,post_viewpermission,post_newthread";
				$useraction = "在『$forum[name]』发新话题";
			}
		}
	}
	$fupadd = $fup ? "OR (fid='$fup[fid]' && type<>'group')" : NULL;

	if(!$forum[viewperm] && !$allowview) {
		showmessage("对不起，您的级别〔{$grouptitle}〕无法浏览贴子。");
	} elseif($forum[viewperm] && !strstr($forum[viewperm], "\t$groupid\t")) {
		showmessage("对不起，本论坛只有特定用户可以浏览，请返回。");
	} elseif($thread[creditsrequire] > $credit && !$ismoderator && !$issupermod) {
		showmessage("对不起，本贴要求{$credittitle}高于 $thread[creditsrequire] {$creditunit}才可浏览，请返回。");
	}

	if(!$ismoderator && preg_match("/\[hide=?\d*\].+?\[\/hide\]/is", $message)) {
		showmessage("对不起，只有版主才能使用 [hide] 代码，请返回修改。");
	}

	if($previewpost || (!$previewpost && !$topicsubmit && !$replysubmit && !$editsubmit)) {
		$tplnames .= $previewpost ? ",post_preview" : NULL;
		$tplnames .= $cdbuser ? ",post_loggedin" : ",post_notloggedin";
		$tplnames .= ",post_bbcodeinsert,post_smilieinsert";
		loadtemplates($tplnames);

		if(!$cdbuser) {
			eval("\$loggedin = \"".template("post_notloggedin")."\";");
		} else {
			eval("\$loggedin = \"".template("post_loggedin")."\";");
		}

		$enctype = (!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postattachperm], "\t$groupid\t")) ? "enctype=\"multipart/form-data\"" : NULL;
		if($bbinsert) {
			eval("\$bbcodeinsert = \"".template("post_bbcodeinsert")."\";");
		}

		if($smileyinsert && is_array($CDB_CACHE_VARS[smilies])) {
			$listed_smilies = 0;
			$smcols = $smcols ? $smcols : 3;

			$smilies .= "<tr>";
			foreach($CDB_CACHE_VARS[smilies] as $smiley) {
				$smilies .= "<td align=\"center\" valign=\"top\"><img src=\"$smdir/$smiley[url]\" border=\"0\" onmouseover=\"this.style.cursor='hand';\" onclick=\"javascript: AddText('$smiley[code]');\"></td>\n";
				$listed_smilies++;
				if($listed_smilies == $smcols) {
					$smilies .= "</tr>";
					$listed_smilies = 0;
				}
			}
			eval("\$smilieinsert .= \"".template("post_smilieinsert")."\";");
		}

		if(is_array($CDB_CACHE_VARS[picons])) {
			$listed_icons = 0; 
			foreach($CDB_CACHE_VARS[picons] as $picon) {
				$icons .= " <input type=\"radio\" name=\"posticon\" value=\"$picon[url]\"><img src=\"$smdir/$picon[url]\">";
				$listed_icons++;
				if($listed_icons == 9) {
					$icons .= "<br>";
					$listed_icons = 0;
				}
			}
		}

		if((!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postattachperm], "\t$groupid\t"))) {
			@$maxattachsize_kb = $maxattachsize / 1000;
			if($attachextensions) {
				$allowattachextensions = "\n<br><br>允许扩展名：$attachextensions";
				$valign = "valign=\"top\"";
			}
		}

		$allowimgcode = $forum[allowimgcode] ? "On" : "Off";
		$allowhtml = $forum[allowhtml] ? "On" : "Off";
		$allowsmilies = $forum[allowsmilies] ? "On" : "Off";
		$allowbbcode = $forum[allowbbcode] ? "On" : "Off";

		if($cdbuser && $signature && !$usesigcheck) {
			$usesigcheck = "checked";
		}
	} else {
		$tplnames .= ",showmessage";
		loadtemplates($tplnames);

		if(!$cdbuser) {
			$password = encrypt($password);
			$query = $db->query("SELECT m.username as cdbuser, m.password as cdbpw, m.uid, m.charset, m.timeoffset,	m.theme, m.tpp, m.ppp, m.credit,
				m.timeformat, m.dateformat, m.signature, m.avatar, m.lastvisit,	m.newu2u, u.*, u.specifiedusers LIKE '%\t$username\t%' AS specifieduser
				FROM $table_members m LEFT JOIN $table_usergroups u ON u.specifiedusers LIKE '%\t$username\t%' OR (u.status=m.status
				AND ((u.creditshigher='0' AND u.creditslower='0' AND u.specifiedusers='') OR (m.credit>=u.creditshigher AND m.credit<u.creditslower)))
				WHERE username='$username' AND password='$password' ORDER BY specifieduser DESC");
			$member = $db->fetch_array($query);
			if(!$member[uid]) {
				showmessage("用户名无效或密码错误，现在将以游客身份转入论坛首页。", "index.php");
			}
			$member[signature] = $member[signature] ? 1 : 0;
			$CDB_SESSION_VARS = array_merge($CDB_SESSION_VARS, $member);
			$username = addslashes($username);
			extract($member, EXTR_OVERWRITE);
		} elseif($cdbuser) {
			$username = $cdbuser;
			$password = $cdbpw;
		} else {
			$username = "游客";
		}

		if(!$forum[postperm] && !$allowpost) {
			showmessage("对不起，您的级别〔{$grouptitle}〕无法法发表贴子。");
		} elseif($forum[postperm] && !strstr($forum[postperm], "\t$groupid\t")) {
			showmessage("对不起，本论坛只有特定用户可以发帖，请返回。");
		}

		$subject = trim($subject);
		$message = trim($message);
	}

	eval("\$css = \"".template("css")."\";");
	if($forum[password] != $_COOKIE["fidpw$fid"] && $forum[password] != $CDB_SESSION_VARS["fidpw$fid"] && $forum[password]) {
		header("Location: {$boardurl}forumdisplay.php?fid=$fid&sid=$sid");
		cdbexit();
	}

	if($previewpost) {
		$poston = "发表于 ".gmdate("$dateformat $timeformat", $timestamp + ($timeoffset * 3600));
		$thisbg = $altbg2;
		$subject = stripslashes($subject);
		$message = stripslashes($message);
		$dissubject = $subject;
		$message1 = postify($message, $smileyoff, $bbcodeoff, $parseurloff, $forum[allowsmilies], $forum[allowhtml], $forum[allowbbcode], $forum[allowimgcode]);

		if($parseurloff) {
			$urloffcheck = "checked\"checked\"";
		}
	
		if($usesig) {
			$usesigcheck = "checked=\"checked\"";
		}

		if($smileyoff) {
			$smileoffcheck = "checked=\"checked\"";
		}

		if($bbcodeoff) {
			$codeoffcheck = "checked=\"checked\"";
		}

		eval("\$preview = \"".template("post_preview")."\";");
		$topicsubmit = $replysubmit = $editsubmit = "";
	}

	if($action == "edit") {

		$query = $db->query("SELECT pid FROM $table_posts WHERE tid='$tid' ORDER BY dateline LIMIT 0, 1");
		$isfirstpost = $db->result($query, 0) == $pid ? 1 : 0;

		$query = $db->query("SELECT author, dateline FROM $table_posts WHERE pid='$pid' AND tid='$tid' AND fid='$fid'");
		$orig = $db->fetch_array($query);
		$orig[author] = addslashes($orig[author]);

		if(!$ismoderator && $cdbuser != $orig[author]) {
			showmessage("对不起，您没有权力编辑或删除这个贴子，请返回。");
		}

		if(!$editsubmit) {

			eval("\$header = \"".template("header")."\";");
			echo $header;
			$query = $db->query("SELECT * FROM $table_posts WHERE pid='$pid' AND tid='$tid' AND fid='$fid'");
			$postinfo = $db->fetch_array($query);

			$usesigcheck = $postinfo[usesig] ? "checked=\"checked\"" : NULL;
			$urloffcheck = $postinfo[parseurloff] ? "checked=\"checked\"" : NULL;
			$smileyoffcheck = $postinfo[smileyoff] ? "checked=\"checked\"" : NULL;
			$codeoffcheck = $postinfo[bbcodeoff] ? "checked=\"checked\"" : NULL;

			if($allowsetviewperm && $isfirstpost) {
				$currcredits = $thread[creditsrequire];
				eval("\$viewpermission = \"".template("post_viewpermission")."\";");
			}

			if((!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postperm], "\t$groupid\t"))) {
				if($postinfo[aid]) {
					$query = $db->query("SELECT * FROM $table_attachments WHERE aid='$postinfo[aid]'");
					$postattach = $db->fetch_array($query);
					$attachsize = sizecount($postattach[filesize]);
					$attachicon = attachicon(substr(strrchr($postattach[attachment], "."), 1)."\t".$postattach[filetype]);
					$setorigattachperm = $allowsetattachperm ? "所需$credittitle <input type=\"text\" name=\"origattachperm\" value=\"$postattach[creditsrequire]\" size=\"5\">" : NULL;
					$setattachperm = $allowsetattachperm ? "所需$credittitle <input type=\"text\" name=\"attachperm\" value=\"0\" size=\"5\" disabled>&nbsp;" : NULL;
					eval("\$attachfile = \"".template("post_edit_attachmentbox_edit")."\";");
				} else {
					$setattachperm = $allowsetattachperm ? "所需$credittitle <input type=\"text\" name=\"attachperm\" value=\"0\" size=\"5\">&nbsp;" : NULL;
					eval("\$attachfile = \"".template("post_edit_attachmentbox")."\";");
				}
			}

			$postinfo[subject] = str_replace('"', "&quot;", $postinfo[subject]);
			$postinfo[message] = cdbhtmlspecialchars($postinfo[message]);
			$postinfo[message] = preg_replace("/\n{2}\[ 本贴由.+?于.+?最后编辑 \]$/is", "", $postinfo[message]);
			if($previewpost) {
				$postinfo[message] = $message;
			}
			eval("\$edit = \"".template("post_edit")."\";");
			echo $edit;

		} else {

			if(!$delete) {
				if(strlen($subject) > 100) {
					showmessage("对不起，您的标题超过 100 个字符，请返回修改标题长度。");
				}

				if(!$issupermod && $maxpostsize && strlen($message) > $maxpostsize) {
					showmessage("对不起，您的贴子超过 $maxpostsize 个字符的限制，请返回修改。");
				}

				$viewpermadd = ($allowsetviewperm && $isfirstpost) ? "creditsrequire='$viewperm'" : NULL;
				$attachpermadd = $allowsetattachperm ? "creditsrequire='$origattachperm'" : NULL;

				$subject = cdbhtmlspecialchars($subject);

				if($isfirstpost) {
					$db->query("UPDATE $table_threads SET icon='$posticon', subject='$subject' WHERE tid='$tid'");
				}

				if ($editedby && ($timestamp - $orig[dateline]) > 60 && !$isadmin){
					$time = gmdate($CDB_CACHE_VARS[settings][dateformat]." ".$CDB_CACHE_VARS[settings][timeformat], $timestamp + ($timeoffset * 3600));
					$message .= "\n\n[ 本贴由 $cdbuser 于 $time 最后编辑 ]";
				}

				if(($attachedit == "delete" || ($attachedit == "new" && attach_upload())) && ((!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postperm], "\t$groupid\t")))) {
					$query = $db->query("SELECT attachment FROM $table_attachments WHERE pid='$pid'");
					$thread_attachment = $db->result($query, 0);
					@unlink("$attachdir/$thread_attachment");
					$db->query("DELETE FROM $table_attachments WHERE pid='$pid'");

					if($attachedit == "new") {
						$attachperm = $allowsetattachperm ? $attachperm : 0;
						$db->query("INSERT INTO $table_attachments (tid, pid, creditsrequire, filename, filetype, filesize, attachment, downloads)
							VALUES ('$tid', '$pid', '$attachperm', '$attach_name', '$attach_type', '$attach_size', '$attach_fname', '0')");
						$aid = $db->insert_id();
					} else {
						$query = $db->query("SELECT attachment, filetype FROM $table_attachments WHERE tid='$tid' ORDER BY pid DESC LIMIT 0, 1");
						if($thread_attachment = $db->fetch_array($query)) {
							$attach_type = substr(strrchr($thread_attachment[attachment], "."), 1)."\t".$thread_attachment[filetype];
						} else {
							$attach_type = "";
						}
					}
					if($viewpermadd) {
						$viewpermadd = ", $viewpermadd";
					}
					$db->query("UPDATE $table_posts SET aid='$aid', message='$message', usesig='$usesig', bbcodeoff='$bbcodeoff',
						smileyoff='$smileyoff', parseurloff='$parseurloff', icon='$posticon', subject='$subject' WHERE pid='$pid'");
					$db->query("UPDATE $table_threads SET attachment='$attach_type' $viewpermadd WHERE tid='$tid'");
				} else {
					$db->query("UPDATE $table_posts SET message='$message', usesig='$usesig', bbcodeoff='$bbcodeoff',
						smileyoff='$smileyoff', parseurloff='$parseurloff', icon='$posticon', subject='$subject' WHERE pid='$pid'");
					if($attachpermadd) {
						$db->query("UPDATE $table_attachments SET $attachpermadd WHERE pid='$pid'");
					}
					if($viewpermadd) {
						$db->query("UPDATE $table_threads SET $viewpermadd WHERE tid='$tid'");
					}
				}
				$modaction = "编辑贴子";
			} elseif($delete && !$isfirstpost) {
				updatemember("-", $orig[author]);

				$query = $db->query("SELECT pid, filetype, attachment FROM $table_attachments WHERE tid='$tid'");
				$attach_type = "";
				while($thread_attachment = $db->fetch_array($query)) {
					if($thread_attachment[filetype]) {
						$attach_type = substr(strrchr($thread_attachment[attachment], "."), 1)."\t".$thread_attachment[filetype];
					}
					if($thread_attachment[pid] == $pid) {
						@unlink("$attachdir/$thread_attachment[attachment]");
					}
				}
				$db->query("UPDATE $table_threads SET attachment='$attach_type' WHERE tid='$tid'");
				$db->query("DELETE FROM $table_attachments WHERE pid='$pid'");
               			$db->query("DELETE FROM $table_posts WHERE pid='$pid'");
               			updateforumcount($fid);
				updatethreadcount($tid);
				$modaction = "删除贴子";
			} elseif($delete && $isfirstpost) {
				$query = $db->query("SELECT author FROM $table_posts WHERE tid='$tid'");
				while($result = $db->fetch_array($query)) {
					updatemember("-", addslashes($result[author]));
				}
				$db->query("DELETE FROM $table_threads WHERE tid='$tid' OR closed='moved|$tid'");

				$query = $db->query("SELECT attachment FROM $table_attachments WHERE tid='$tid'");
				while($thread_attachment = $db->fetch_array($query)) {
					@unlink("$attachdir/$thread_attachment[attachment]");
				}

				$db->query("DELETE FROM $table_attachments WHERE tid='$tid'");
				$db->query("DELETE FROM $table_posts WHERE tid='$tid'");
				updateforumcount($fid);
				$modaction = "删除主题";
				showmessage("主题删除成功，现在将转入主题列表。", "forumdisplay.php?fid=$fid");
			}

			if($cdbuser != $orig[author]) {
				@$fp = fopen("./datatemp/modslog.php", "a");
				@flock($fp, 3);
				@fwrite($fp, "$cdbuser\t$status\t$onlineip\t$timestamp\t$fid\t$forum[name]\t$tid\t$threadname\t$modaction\n");
				@fclose($fp);
			}

			showmessage("您的帖子编辑成功，现在将转入主题页。<br><br><a href=\"forumdisplay.php?fid=$fid\">[ 需要转入主题列表请点击这里 ]</a>", "viewthread.php?tid=$tid&page=$page#pid$pid");
		}

	} elseif($action == "newthread") {

		if($forum[type] == "group") {
			showmessage("无法在栏目中发帖，请返回选择相应的论坛。");
		}

		if(!$topicsubmit) {
			eval("\$header = \"".template("header")."\";");
			echo $header;

			$topoption = "";
			if($ismoderator) {
				$modoptions .= "<br><input type=\"checkbox\" name=\"toptopic\" value=\"1\"> 主题置顶\n".
					"<br><input type=\"checkbox\" name=\"addtodigist\" value=\"1\"> 精华贴子\n";
			}

			if((!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postperm], "\t$groupid\t"))) {
				$setattachperm = $allowsetattachperm ? "所需$credittitle <input type=\"text\" name=\"attachperm\" value=\"0\" size=\"5\">&nbsp;" : NULL;
				eval("\$attachfile = \"".template("post_attachmentbox")."\";");
			}

			if($allowsetviewperm) {
				$currcredits = 0;
				eval("\$viewpermission = \"".template("post_viewpermission")."\";");
			}

			if($poll && $allowpostpoll) {
				eval("\$postform = \"".template("post_newpoll")."\";");
				echo $postform;
			} else {
				eval("\$postform = \"".template("post_newthread")."\";");
				echo $postform;
			}
		}
		if($topicsubmit) {
			if(!$subject) {
				showmessage("您没有输入标题，请返回填写。");
			}
			if(strlen($subject) > 100) {
				showmessage("对不起，您的标题超过 100 个字符，请返回修改标题长度。");
			}

			if(!$issupermod && $maxpostsize && strlen($message) > $maxpostsize) {
				showmessage("对不起，您的贴子超过 $maxpostsize 个字符的限制，请返回修改。");
			}

			if(!$isadmin && !$issupermod && $forum[lastpost]) {
				$lastpost = explode("\t", $forum[lastpost]);
				if(($timestamp - $floodctrl) <= $lastpost[1] && $username == $lastpost[2]) {
					showmessage("两次发表间隔少于 $floodctrl 秒，请不要灌水！<a href=\"forumdisplay.php?fid=$fid\">点击这里</a> 继续。");
				}
			}

			$subject = cdbhtmlspecialchars($subject);
			$topped = ($ismoderator && $toptopic) ? 1 : 0;
			$digist = ($ismoderator && $addtodigist) ? 1 : 0;
			$viewperm = $allowsetviewperm ? $viewperm : 0;

			if($allowpostpoll) {
				$pollops = explode("\n", $pollanswers);
				$pollanswers = "";
				for($pnum = 0; $pnum < 10; $pnum++) {
					if($pollops[$pnum] != "") {
						$pollanswers .= "$pollops[$pnum]||~|~|| 0#|#";
					}
				}

				$pollanswers = str_replace("\n", "", $pollanswers);
			}

			if(attach_upload() && ((!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postperm], "\t$groupid\t")))) {
				$attachperm = $allowsetattachperm ? $attachperm : 0;
				$db->query("INSERT INTO $table_attachments (creditsrequire, filename, filetype, filesize, attachment, downloads)
					VALUES ('$attachperm', '$attach_name', '$attach_type', '$attach_size', '$attach_fname', '0')");
				$aid = $db->insert_id();
				$attach_type = substr(strrchr($attach_name, "."), 1)."\t".$attach_type;
			} else {
				$attach_type = "";
				$aid = "";
			}

			$db->query("INSERT INTO $table_threads (fid, creditsrequire, icon, author, subject, dateline, lastpost, lastposter, topped, digist, pollopts, attachment)
				VALUES ('$fid', '$viewperm', '$posticon', '$username', '$subject', '$timestamp', '$timestamp', '$username', '$topped', '$digist', '$pollanswers', '$attach_type')");
			$tid = $db->insert_id();
			$db->query("INSERT INTO $table_posts  (fid, tid, aid, icon, author, subject, dateline, message, useip, usesig, bbcodeoff, smileyoff, parseurloff)
				VALUES ('$fid', '$tid', '$aid', '$posticon', '$username', '$subject', '$timestamp', '$message', '$onlineip', '$usesig', '$bbcodeoff', '$smileyoff', '$parseurloff')");
			$pid = $db->insert_id();
			if($aid) {
				$db->query("UPDATE $table_attachments SET tid='$tid', pid='$pid' WHERE aid='$aid'");
			}
			updatemember("+", $username);
			$db->query("UPDATE $table_forums SET lastpost='$subject\t$timestamp\t$username', threads=threads+1, posts=posts+1 WHERE fid='$fid' $fupadd");

			if($emailnotify && $username != "游客") {
				$query = $db->query("SELECT tid FROM $table_subscriptions WHERE tid='$tid' AND username='$username'");
				if(!$db->result($query, 0)) {
					$db->query("INSERT INTO $table_subscriptions (username, email, tid)
						VALUES ('$username', '$email', '$tid')");
				}
			}

			if(!$cdbuser || !$cdbpw) {
				$currtime = cookietime();
				setcookie("_cdbuser", $username, $currtime, $cookiepath, $cookiedomain);
				setcookie("_cdbpw", $password, $currtime, $cookiepath, $cookiedomain);
				$CDB_SESSION_VARS[cdbuser] = $username;
				$CDB_SESSION_VARS[cdbpw] = $password;
			}
			showmessage("非常感谢，您的帖子已经发布，现在将转入主题页。<br><br><a href=\"forumdisplay.php?fid=$fid\">[ 需要转入主题列表请点击这里 ]</a>", "viewthread.php?tid=$tid");
		}

	} elseif($action == "reply") {

		if(!$replysubmit) {
			eval("\$header = \"".template("header")."\";");
			echo $header;
			if($repquote) {
				$query = $db->query("SELECT message, fid, author, dateline FROM $table_posts WHERE pid='$repquote'");
				$thaquote = $db->fetch_array($query);
				$quotefid = $thaquote[fid];
				$message = $thaquote[message];

				$time = gmdate("$dateformat $timeformat", $thaquote[dateline] + ($timeoffset * 3600));
				$message = trim(preg_replace("/(\[quote])(.*)(\[\/quote])/siU", "", $message));
				$message = wordscut(cdbhtmlspecialchars($message), 200);

				$message = preg_replace("/\[hide=?\d*\](.+?)\[\/hide\]/is", "[b]**** 原作者隐藏信息 请参考原始贴子 *****[/b]", $message);
				$message = preg_replace("/\n{2}\[ 本贴由.+?于.+?最后编辑 \]$/is", "", $message);
				$message = "[quote][i]$thaquote[author][/i] 于 $time 写道：\n$message [/quote]\n";
			}

			if($thread[replies] >= $ppp) {
				$threadlink = "viewthread.php?fid=$fid&tid=$tid";
				eval("\$posts .= \"".template("post_reply_review_toolong")."\";");
			} else {
				$thisbg = $altbg1;
				$query = $db->query("SELECT * FROM $table_posts WHERE tid='$tid' ORDER BY dateline DESC");
				while($post = $db->fetch_array($query)) {
					$poston = "发表于 ".gmdate("$dateformat $timeformat", $post[dateline] + ($timeoffset * 3600));;
					if($post[icon] != "") {
						$post[icon] = "<img src=\"$smdir/$post[icon]\" align=\"absmiddle\">";
					}
					$post[message] = preg_replace("/\[hide=?\d*\](.+?)\[\/hide\]/is","[b]**** 原作者隐藏信息 请参考原始贴子 *****[/b]", $post[message]);
					$post[message] = postify($post[message], $post[smileyoff], $post[bbcodeoff], $post[parseurloff], $forum[allowsmilies], $forum[allowhtml], $forum[allowbbcode], $forum[allowimgcode]);
					if($thisbg == $altbg2) {
						$thisbg = $altbg1;
					} else {
						$thisbg = $altbg2;
					}
					eval("\$posts .= \"".template("post_reply_review_post")."\";");
				}
			}
			if((!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postperm], "\t$groupid\t"))) {
				$setattachperm = $allowsetattachperm ? "所需$credittitle <input type=\"text\" name=\"attachperm\" value=\"0\" size=\"5\">&nbsp;" : NULL;
				eval("\$attachfile = \"".template("post_attachmentbox")."\";");
			}
			eval("\$postform = \"".template("post_reply")."\";");
			echo $postform;
		}
		if($replysubmit) {
			if(($subject == "" || ereg("^ *$", $subject)) && $message == "") {
				showmessage("您标题和内容都未填写，请返回并至少填写其中一项。");
			}

			if(strlen($subject) > 100) {
				showmessage("对不起，您的标题超过 100 个字符，请返回修改标题长度。");
			}

			if(!$issupermod && $maxpostsize && strlen($message) > $maxpostsize) {
				showmessage("对不起，您的贴子超过 $maxpostsize 个字符的限制，请返回修改。");
			}

			if(!$isadmin && !$issupermod && $forum[lastpost]) {
				$lastpost = explode("\t", $forum[lastpost]);
				if(($timestamp - $floodctrl) <= $lastpost[1] && $username == $lastpost[2]) {
					showmessage("两次发表间隔少于 $floodctrl 秒，请不要灌水！<a href=\"forumdisplay.php?fid=$fid\">点击这里</a> 继续。");
				}
			}

			$subject = cdbhtmlspecialchars($subject);
			if($thread[closed] && !$ismoderator) {
				showmessage("对不起，本主题已关闭，不再接受回复。");
			} else {
				$emails = $comma = "";
				$notifytime = $timestamp - 43200;
				$query = $db->query("SELECT email FROM $table_subscriptions WHERE username<>'$username' AND tid='$tid' AND lastnotify<'$notifytime'");
				while($subs = $db->fetch_array($query)) {
					$emails .= "$comma$subs[email]";
					$comma = ", ";
				}
				if($emails) {
					@mail("nobody@localhost", "[Discuz!] $threadname 已有新回复",
							"您好，$username 刚刚回复了您在 $bbname 所订阅的主题已有新的回复，详情请访问：\n\n".
							"{$boardurl}viewthread.php?tid=$tid\n\n".
							"12 小时之内我们将不再向您发送本贴的回复消息\n\n".
							"欢迎您光临 $bbname\n".
							"$boardurl",
						"Bcc: $emails\r\nFrom: $bbname <$adminemail>");
				}
				if($emailnotify && $username != "游客") {
					$query = $db->query("SELECT tid FROM $table_subscriptions WHERE tid='$tid' AND username='$username'");
					if(!$db->result($query, 0)) {
						$db->query("INSERT INTO $table_subscriptions (username, email, tid)
							VALUES ('$username', '$email', '$tid')");
					}
				}
				if(attach_upload() && ((!$forum[postattachperm] && $allowpostattach) || ($forum[postattachperm] && strstr($forum[postperm], "\t$groupid\t")))) {
					$attachperm = $allowsetattachperm ? $attachperm : 0;
					$db->query("INSERT INTO $table_attachments (tid, pid, creditsrequire, filename, filetype, filesize, attachment)
						VALUES ('$tid', '', '$attachperm', '$attach_name', '$attach_type', '$attach_size', '$attach_fname')");
					$aid = $db->insert_id();
					$attach_type = substr(strrchr($attach_name, "."), 1)."\t".$attach_type;
				} else {
					$attach_type = "";
					$aid = "";
				}

				$db->query("INSERT INTO $table_posts  (fid, tid, aid, icon, author, subject, dateline, message, useip, usesig, bbcodeoff, smileyoff, parseurloff)
					VALUES ('$fid', '$tid', '$aid', '$posticon', '$username', '$subject', '$timestamp', '$message', '$onlineip', '$usesig', '$bbcodeoff', '$smileyoff', '$parseurloff')");
				$pid = $db->insert_id();
				if($aid) {
					$db->query("UPDATE $table_attachments SET pid='$pid' WHERE aid='$aid'");
					$db->query("UPDATE $table_threads SET lastposter='$username', lastpost='$timestamp', replies=replies+1, attachment='$attach_type' WHERE tid='$tid' AND fid='$fid'");
				} else {
					$db->query("UPDATE $table_threads SET lastposter='$username', lastpost='$timestamp', replies=replies+1 WHERE tid='$tid' AND fid='$fid'");
				}
				updatemember("+", $username);
				$db->query("UPDATE $table_forums SET lastpost='".addslashes($thread[subject])."\t$timestamp\t$username', posts=posts+1 WHERE fid='$fid' $fupadd");
			}

			if(!$cdbuser || !$cdbpw) {
				$currtime = cookietime();
				setcookie("_cdbuser", $username, $currtime, $cookiepath, $cookiedomain);
				setcookie("_cdbpw", $password, $currtime, $cookiepath, $cookiedomain);
				$CDB_SESSION_VARS[cdbuser] = $username;
				$CDB_SESSION_VARS[cdbpw] = $password;
			}
			@$topicpages = ceil(($thread[replies] + 2) / $ppp);
			showmessage("非常感谢，您的回复已经发布，现在将转入主题页。<br><br><a href=\"forumdisplay.php?fid=$fid\">[ 需要转入主题列表请点击这里 ]</a>", "viewthread.php?tid=$tid&pid=$pid&page=$topicpages#pid$pid");
		}

	}

} else {

	$useraction = "未定义操作 [POST]";
	showmessage("未定义操作，请返回。");

}

gettotaltime();
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();

?>