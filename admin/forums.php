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


if(!defined("IN_CDB")) {
        die("Access Denied");
}

cpheader();

if($action == "forumadd") {

	if((!$catsubmit && !$forumsubmit)) {
		$groupselect = $forumselect = "<select name=\"fup\">\n<option value=\"0\" selected=\"selected\"> - 无 - </option>\n";
		$query = $db->query("SELECT fid, name, type FROM $table_forums WHERE type<>'sub' ORDER BY displayorder");
		while($fup = $db->fetch_array($query)) {
			if($fup[type] == "group") {
				$groupselect .= "<option value=\"$fup[fid]\">$fup[name]</option>\n";
			} else {
				$forumselect .= "<option value=\"$fup[fid]\">$fup[name]</option>\n";
			}
		}
		$groupselect .= "</select>";
		$forumselect .= "</select>";

?>
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>特别提示</td></tr>
<tr bgcolor="<?=$altbg1?>"><td>
<br><ul><li>论坛或分类的名称可包含并显示 html 代码。</ul>
</td></tr></table></td></tr></table>

<br><form method="post" action="admincp.php?action=forumadd&add=category">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="3">添加新分类</td></tr>
<tr align="center"><td bgcolor="<?=$altbg1?>" width="15%">分类名称：</td>
<td bgcolor="<?=$altbg2?>" width="70%"><input type="text" name="newcat" value="新分类名称" size="40"></td>
<td bgcolor="<?=$altbg1?>" width="15%"><input type="submit" name="catsubmit" value="添 加"></td></tr>
</table></td></tr></table></form>

<form method="post" action="admincp.php?action=forumadd&add=forum">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="5">添加新论坛</td></tr>
<tr align="center"><td bgcolor="<?=$altbg1?>" width="15%">论坛名称：</td>
<td bgcolor="<?=$altbg2?>" width="28%"><input type="text" name="newforum" value="新论坛名称" size="20"></td>
<td bgcolor="<?=$altbg1?>" width="15%">上级分类：</td>
<td bgcolor="<?=$altbg2?>" width="27%"><?=$groupselect?></td>
<td bgcolor="<?=$altbg1?>" width="15%"><input type="submit" name="forumsubmit" value="添 加"></td></tr>
</table></td></tr></table></form>

<form method="post" action="admincp.php?action=forumadd&add=forum">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="5">添加新子论坛</td></tr>
<tr align="center"><td bgcolor="<?=$altbg1?>" width="15%">子论坛名称：</td>
<td bgcolor="<?=$altbg2?>" width="28%"><input type="text" name="newforum" value="新论坛名称" size="20"></td>
<td bgcolor="<?=$altbg1?>" width="15%">上级论坛：</td>
<td bgcolor="<?=$altbg2?>" width="27%"><?=$forumselect?></td>
<td bgcolor="<?=$altbg1?>" width="15%"><input type="submit" name="forumsubmit" value="添 加"></td></tr>
</table></td></tr></table></form><br>
<?

	} elseif($catsubmit) {
		$db->query("INSERT INTO $table_forums (type, name, status)
			VALUES ('group', '$newcat', '1')");

		updatecache("forums");
		cpmsg("添加分类 <b>$newcat</b> 成功。");
	} elseif($forumsubmit) {
		$query = $db->query("SELECT type FROM $table_forums WHERE fid='$fup'");
		$type = $db->result($query, 0) == "forum" ? "sub" : "forum";
		$db->query("INSERT INTO $table_forums (fup, type, name, status)
			VALUES ('$fup', '$type', '$newforum', '1')");

		updatecache("forums");
		cpmsg("添加论坛 <b>$newforum</b> 成功。");
	}		

} elseif($action == "forumsedit") {

        if(!$editsubmit) {

?>
<table cellspacing="0" cellpadding="0" border="0" width="<?=$tablewidth?>" align="center">
<tr><td bgcolor="<?=$bordercolor?>">

<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td>论坛编辑 - 多个版主间请用半角逗号 "," 分割</td></tr>
<tr><td bgcolor="<?=$altbg1?>"><br>
<form method="post" action="admincp.php?action=forumsedit">
<?

                $modsorig = $comma = "";//原版主列表
                $query = $db->query("SELECT fid, type, status, name, fup, displayorder, moderator FROM $table_forums ORDER BY displayorder");
                while($forum = $db->fetch_array($query)) {
                        $forums[] = $forum;
                        $modsorig .= $comma.$forum[moderator];
                        $comma = ",";
                }

                for($i = 0; $i < count($forums); $i++)
                {
                        if($forums[$i][type] == "group") {
                                echo "<ul>";
                                showforum($forums[$i], 1, "group");
                                for($j = 0; $j < count($forums); $j++) {
                                        if($forums[$j][fup] == $forums[$i][fid] && $forums[$j][type] == "forum")
                                        {
                                                echo "<ul>";
                                                showforum($forums[$j], 2);
                                                for($k = 0; $k < count($forums); $k++) {
                                                        if($forums[$k][fup] == $forums[$j][fid] && $forums[$k][type] == "sub") {
                                                                echo "<ul>";
                                                                showforum($forums[$k], 3, "sub");
                                                                echo "</ul>";
                                                        }
                                                }
                                                echo "</ul>";
                                        }
                                }
                                echo "</ul>";
                        }
                        elseif(!$forums[$i][fup] && $forums[$i][type] == "forum")
                         {
                                echo "<ul>";
                                showforum($forums[$i], 1);
                                for($j = 0; $j < count($forums); $j++) {
                                        if($forums[$j][fup] == $forums[$i][fid] && $forums[$j][type] == "sub") {
                                                echo "<ul>";
                                                showforum($forums[$j], 2, "sub");
                                                echo "</ul>";
                                        }
                                }
                                echo "</ul>";
                        }
                }
                echo "<input type=\"hidden\" name=\"modsorig\" value=\"$modsorig\"><br><center>\n".
                        "<input type=\"submit\" name=\"editsubmit\" value=\"更新论坛设置\"></center><br></td></tr></table></td></tr></table>\n";

        } else {

                if(is_array($moderator))
                {
                        $modlist = $comma = "";//版主列表
                        foreach($moderator as $fid => $mod) {
                                $modlist .= $comma.$mod;
                                $comma = ",";
                                $db->query("UPDATE $table_forums SET moderator='$mod', displayorder='".$order[$fid]."' WHERE fid='$fid'");
                        }
                }

                updatecache("forums");
                $modsorig = "'".str_replace(",", "', '", str_replace(" ", "", $modsorig))."'";
                $modlist = "'".str_replace(",", "', '", str_replace(" ", "", $modlist))."'";
                $db->query("UPDATE $table_members SET status='正式会员' WHERE status<>'论坛管理员' AND status='版主' AND username IN ($modsorig)");//将原来的版主设为普通会员进行初始化,status<>'论坛管理员'此处多余,因为status='版主'时左边肯定成立
                $db->query("UPDATE $table_members SET status='版主' WHERE status<>'论坛管理员' AND status<>'超级版主' AND username IN ($modlist)");//设置版主列表

                cpmsg("论坛设置成功更新。");
        }

} elseif($action == "forumsmerge") {

	if(!$mergesubmit || $source == $target || !$source || !$target) {
		$forumselect = "<select name=\"%s\">\n<option value=\"0\" selected=\"selected\"> - 无 - </option>\n";
		$query = $db->query("SELECT fid, name FROM $table_forums WHERE type<>'group' ORDER BY displayorder");
		while($forum = $db->fetch_array($query)) {
			$forumselect .= "<option value=\"$forum[fid]\">$forum[name]</option>\n";
		}
		$forumselect .= "</select>";

?>
<br><br><br><br><br>
<form method="post" action="admincp.php?action=forumsmerge">
<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr class="header"><td colspan="3">合并论坛 - 源论坛的贴子全部转入目标论坛，同时删除源论坛</td></tr>
<tr align="center"><td bgcolor="<?=$altbg1?>" width="40%">源论坛：</td>
<td bgcolor="<?=$altbg2?>" width="60%"><?=sprintf($forumselect, "source")?></td></tr>
<tr align="center"><td bgcolor="<?=$altbg1?>" width="40%">目标论坛：</td>
<td bgcolor="<?=$altbg2?>" width="60%"><?=sprintf($forumselect, "target")?></td></tr>
</table></td></tr></table><br><center><input type="submit" name="mergesubmit" value="合并论坛"></center></form>
<?

	} else {

	        $query = $db->query("SELECT COUNT(*) FROM $table_forums WHERE fup='$source'");
	        if($db->result($query, 0)) {//不能有子论坛
        		cpmsg("源论坛下级论坛不为空，请先返回修改相关下级论坛的上级设置。");
        	}

		$db->query("UPDATE $table_threads SET fid='$target' WHERE fid='$source'");
		$db->query("UPDATE $table_posts SET fid='$target' WHERE fid='$source'");

		$query = $db->query("SELECT threads, posts FROM $table_forums WHERE fid='$source'");
		$sourceforum = $db->fetch_array($query);
		$db->query("UPDATE $table_forums SET threads=threads+$sourceforum[threads], posts=posts+$sourceforum[posts] WHERE fid='$target'");
		$db->query("DELETE FROM $table_forums WHERE fid='$source'");

		updatecache("forums");
		cpmsg("论坛合并成功。");
	}

}
else
    if($action == "forumdetail")
    {

	$perms = array("viewperm", "postperm", "getattachperm", "postattachperm");

        if(!$detailsubmit) {
        	$query = $db->query("SELECT * FROM $table_forums WHERE fid='$fid'");
        	$forum = $db->fetch_array($query);
        	$forum[name] = cdbhtmlspecialchars($forum[name]);

        	echo "<br><form method=\"post\" action=\"admincp.php?action=forumdetail&fid=$fid\">\n".
        		"<input type=\"hidden\" name=\"type\" value=\"$forum[type]\">\n";

        	if($forum[type] == "group") {

			showtype("分类名称设置 - $forum[name]", "top");
			showsetting("分类名称：", "namenew", $forum[name], "text", "");
			showtype("", "bottom");

        	} else {

			$fupselect = "<select name=\"fupnew\">\n<option value=\"0\" ".(!$forum[fup] ? "selected=\"selected\"" : NULL)."> - 无 - </option>\n";
			$query = $db->query("SELECT fid, name FROM $table_forums WHERE fid<>'$fid' AND type<>'sub' ORDER BY displayorder");
			while($fup = $db->fetch_array($query)) {
				$selected = $fup[fid] == $forum[fup] ? "selected=\"selected\"" : NULL;
				$fupselect .= "<option value=\"$fup[fid]\" $selected>$fup[name]</option>\n";
			}
			$fupselect .= "</select>";
			$query = $db->query("SELECT groupid, grouptitle FROM $table_usergroups");
			while($group = $db->fetch_array($query)) {
				$groups[] = $group;
			}
             //论坛权限
             foreach($perms as $perm) {
	                	$num = -1;
                		$$perm = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" align=\"center\"><tr>";
				foreach($groups as $group) {
					$num++;
					if($num && $num % 4 == 0) {
						$$perm .= "</tr><tr>";//一列显示4个
					}
	                        	$checked = strstr($forum[$perm], "\t$group[groupid]\t") ? "checked" : NULL;
                        		$$perm .= "<td><input type=\"checkbox\" name=\"{$perm}[]\" value=\"$group[groupid]\" $checked> $group[grouptitle]</td>\n";
				}
                		$$perm .= "</tr></table>";
                	}

			$forum[description] = str_replace("&lt;", "<", $forum[description]);
			$forum[description] = str_replace("&gt;", ">", $forum[description]);

			showtype("论坛详细设置 - $forum[name]", "top");
			showsetting("显示论坛：", "statusnew", $forum[status], "radio", "选择“否”将暂时将论坛隐藏不显示，但论坛内容仍将保留");
			showsetting("上级论坛：", "", "", $fupselect, "本论坛的上级论坛或分类");
			showsetting("论坛名称：", "namenew", $forum[name], "text");
			showsetting("论坛图标：", "iconnew", $forum[icon], "text", "论坛名称和简介左侧的小图标，可填写相对或绝对地址");
			showsetting("论坛简介：", "descriptionnew", $forum[description], "textarea", "将显示于论坛名称的下面，提供对本论坛的简短描述");
			
			showtype("贴子选项");
			showsetting("允许使用 html 代码：", "allowhtmlnew", $forum[allowhtml], "radio", "注意：选择“是”将不屏蔽贴子中的任何代码，有可能造成不安全因素，请慎用");
			showsetting("允许使用 Discuz! 代码：", "allowbbcodenew", $forum[allowbbcode], "radio", "Discuz! 代码是一种简化和安全的页面格式代码，可<a href=\"./faq.php?page=misc#1\" target=\"_blank\">点击这里查看本论坛提供的 Discuz! 代码</a>");
			showsetting("允许使用 [img] 代码：", "allowimgcodenew", $forum[allowimgcode], "radio", "允许 [img] 代码作者将可以在贴子插入其他网站的图片并显示");
			showsetting("允许使用 Smilies：", "allowsmiliesnew", $forum[allowsmilies], "radio", "Smilies 提供对表情符号，如“:)”的解析，使之作为图片显示");
			
			showtype("论坛权限 - 全不选则按照默认设置");
			showsetting("访问密码：", "passwordnew", $forum[password], "text", "", "15%");
			showsetting("浏览贴子许可", "", "", str_replace("cdb_groupname", "viewperm", $viewperm), "", "15%");
			showsetting("发帖许可", "", "", str_replace("cdb_groupname", "postperm", $postperm), "", "15%");
			showsetting("下载附件许可", "", "", str_replace("cdb_groupname", "getattachperm", $getattachperm), "", "15%");
			showsetting("上传附件许可", "", "", str_replace("cdb_groupname", "postattachperm", $postattachperm), "", "15%");
			showtype("", "bottom");

        	}

		echo "<br><br><center><input type=\"submit\" name=\"detailsubmit\" value=\"确认更改\"></form>";

	}
	    else
	    {

		if($type == "group") {

			if($namenew) {
				$db->query("UPDATE $table_forums SET name='$namenew' WHERE fid='$fid'");
				updatecache("forums");
				cpmsg("分类名称成功更新。");
			} else {
				cpmsg("您没有输入分类名称，请返回修改。");
			}
			
		} else {

			foreach($perms as $perm)
			{
				if(is_array($$perm)) {
					${$perm."new"} = "\t";
					foreach($$perm as $groupid) {
						${$perm."new"} .= "\t$groupid";
					}
					${$perm."new"} .= "\t\t";
				}
			}

			$query = $db->query("SELECT type FROM $table_forums WHERE fid='$fupnew'");
			$fuptype = $db->result($query, 0);
			$typenew = $fuptype == "forum" ? "sub" : "forum";
			$db->query("UPDATE $table_forums SET type='$typenew', status='$statusnew', fup='$fupnew', name='$namenew', icon='$iconnew',
				description='$descriptionnew', allowhtml='$allowhtmlnew', allowbbcode='$allowbbcodenew', allowimgcode='$allowimgcodenew',
				allowsmilies='$allowsmiliesnew', password='$passwordnew', viewperm='$viewpermnew', postperm='$postpermnew',
				getattachperm='$getattachpermnew', postattachperm='$postattachpermnew' WHERE fid='$fid'");

			updatecache("forums");
			cpmsg("论坛设置成功更新。");
		}

	}

}
elseif($action == "forumdelete")
{

        $query = $db->query("SELECT COUNT(*) FROM $table_forums WHERE fup='$fid'");
        if($db->result($query, 0)) {
        	cpmsg("下级论坛不为空，请先返回删除本分类或论坛的下级论坛。");
        }

        if(!$confirmed) {
		cpmsg("本操作不可恢复，您确定要删除该论坛，清除其中贴子<br>和附件并处理相关会员的发帖和积分数据吗？", "admincp.php?action=forumdelete&fid=$fid", "form");
        } else {
        	$query = $db->query("SELECT COUNT(*) AS postnum, author FROM $table_posts WHERE fid='$fid' GROUP BY author");
        	while($post = $db->fetch_array($query)) {
        		updatemember("-", $post[author], $post[postnum]);
        	}
//当前论坛的所有附件
        	$query = $db->query("SELECT pid FROM $table_posts WHERE aid<>'0' AND fid='$fid'");
        	$aid = $comma = "";
        	while($post = $db->fetch_array($query)) {
        		$aid .= "$comma'$post[aid]'";
        		$comma = ", ";
        	}

        	if($aid) {//删除当前论坛的所有附件
        		$query = $db->query("SELECT filename FROM $table_attachments WHERE aid IN ($aid)");
        		while($attach = $db->fetch_array($query)) {
        			@unlink("$attachdir/$attach[filename]");
        		}
			$db->query("DELETE FROM $table_attachments WHERE aid IN ($aid)");//删除当前论坛的所有记录
        	}

		$db->query("DELETE FROM $table_threads WHERE fid='$fid'");
		$db->query("DELETE FROM $table_posts WHERE fid='$fid'");
		$db->query("DELETE FROM $table_forums WHERE fid='$fid'");

		updatecache("forums");
		cpmsg("论坛成功删除。");
        }

}

?>