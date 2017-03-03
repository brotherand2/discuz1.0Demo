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


if(!defined("IN_CDB"))
{
        die("Access Denied");
}

cpheader();

if($action == "prune")
{

	if(!$prunesubmit) {

?>
<br><br><form method="post" action="admincp.php?action=prune">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">

<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">

<tr>
<td class="header" colspan="2">批量删贴 [此操作不可恢复 请慎重使用!]</td>
</tr>

<tr>
<td bgcolor="<?=$altbg1?>">删除多少天以前的贴子(不限制时间请输入 0)：</td>
<td align="right" bgcolor="<?=$altbg2?>"><input type="text" name="days" size="7"></td>
</tr>

<tr>
<td bgcolor="<?=$altbg1?>">请选择要批量删除的论坛：</td>
<td align="right" bgcolor="<?=$altbg2?>"><select name="forums">
<option value="all">&nbsp;&nbsp;> 全部论坛</option><option value="">&nbsp;</option>
<?=forumselect()?></select></td>
</tr>

<tr>
<td bgcolor="<?=$altbg1?>">按用户名删除(多用户中间请用半角逗号 "," 分割)：</td>
<td align="right" bgcolor="<?=$altbg2?>"><input type="text" name="users" size="40"></td>
</tr>

<tr>
<td bgcolor="<?=$altbg1?>">包含关键字(多关键字中间请用半角逗号 "," 分割)：</td>
<td align="right" bgcolor="<?=$altbg2?>"><input type="text" name="keywords" size="40"></td>
</tr>

</table></td></tr></table><br>
<center><input type="submit" name="prunesubmit" value="执 行"></center>
</form>
<?

	} else
		{

		if($days == "" || !$forums)
		{
			cpmsg("您没有选择时间范围或论坛名称。");
		} else {
			$sql = "SELECT fid, tid, pid, author FROM $table_posts WHERE 1";

			if($forums != "all") {
				$sql .= " AND fid='$forums'";
			}
			if($days != "0") {
				$prunedate = $timestamp - (86400 * $days);
				$sql .= " AND dateline<='$prunedate'";
			}
			if(trim($keywords))
			{
				$sqlkeywords = "";
				$or = "";
				$keywords = explode(",", str_replace(" ", "",$keywords));
				for($i = 0; $i < count($keywords); $i++) {
					$sqlkeywords .= " $or subject LIKE '%".$keywords[$i]."%' OR message LIKE '%".$keywords[$i]."%'";
					$or = "OR";
				}
				$sql .= " AND ($sqlkeywords)";
			}
			if(trim($users)) {
				$sql .= " AND author IN ('".str_replace(",", "', '", str_replace(" ", "", $users))."')";
			}

			$prune = array();
			$tids = $comma1 = $pids = $comma2 = "";
			$query = $db->query($sql);
			while($post = $db->fetch_array($query)) {
				$prune[forumposts][$post[fid]]++;//要删除的贴子
				$prune[thread][$post[tid]]++;//当前主题有多少贴子要删除
				$prune[user][addslashes($post[author])]++;//每个用户有哪些贴子要删除

				$tids .= "$comma1'$post[tid]'";//哪些主题有贴子要删除
				$comma1 = ", ";

				$pids .= "$comma2'$post[pid]'";//有符合条件的要删除的贴子
				$comma2 = ", ";
			}

			if($pids) //有符合条件的要删除的贴子
			{
				$tidsdelete = $comma = "";
				$query = $db->query("SELECT fid, tid, replies FROM $table_threads WHERE tid IN ($tids)");
				while($thread = $db->fetch_array($query)) {
					if($thread[replies] + 1 <= $prune[thread][$thread[tid]]) //该主题下所有贴子都要删除
					{
						$tidsdelete .= "$comma'$thread[tid]'";//则删除相应的主题
						$comma = ", ";
						$prune[forumthreads][$thread[fid]]++;//每个论坛要删除的主题
					}
				}
				if($tidsdelete)
				{//删除没有贴子的主题
					$db->query("DELETE FROM $table_threads WHERE tid IN ($tidsdelete)");
				}

				$query = $db->query("SELECT attachment FROM $table_attachments WHERE pid IN ($pids)");
				while($attach = $db->fetch_array($query))
				{
					@unlink("$attachdir/$attach[attachment]");//先删除贴子中的附件
				}

				$query = $db->query("SELECT fid FROM $table_forums");
				while($forum = $db->fetch_array($query))
				{
					if($prune[forumthreads][$forum[fid]] || $prune[forumposts][$forum[fid]])
					{//如果这个论坛有主题或贴子要删除
						$prune[forumthreads][$forum[fid]] = intval($prune[forumthreads][$forum[fid]]);
						$prune[forumposts][$forum[fid]] = intval($prune[forumposts][$forum[fid]]);
						$querythd = $db->query("SELECT subject, lastpost, lastposter FROM $table_threads WHERE fid='$forum[fid]' GROUP BY fid ORDER BY tid DESC LIMIT 0, 1");//当前论坛最新的贴子
						$thread = $db->fetch_array($querythd);
						$thread[subject] = addslashes($thread[subject]);
						$thread[lastposter] = addslashes($thread[lastposter]);
						$db->query("UPDATE $table_forums SET threads=threads-".$prune[forumthreads][$forum[fid]].", posts=posts-".$prune[forumposts][$forum[fid]].", lastpost='$thread[subject]\t$thread[lastpost]\t[$thread[lastposter]' WHERE fid='$forum[fid]'");//更新论坛的主题数,贴子数,最新发表贴子信息
					}
				}

				foreach($prune[thread] as $tid => $decrease) //更新主题的回复数
				{
					$db->query("UPDATE $table_threads SET replies=replies-$decrease WHERE tid='$tid'");
				}
				foreach($prune[user] as $username => $decrease) //更新会员贴子数,积分
				{
					$db->query("UPDATE $table_members SET postnum=postnum-$decrease, credit=credit-$decrease*$postcredits WHERE username='$username'");
				}

				$db->query("DELETE FROM $table_attachments WHERE pid IN ($pids)");//删除附件表
				$db->query("DELETE FROM $table_posts WHERE pid IN ($pids)");//最后删除贴子

				$num = $db->affected_rows();
			}

			$num = intval($num);
			cpmsg("符合条件的 $num 篇贴子被删除，相关数据成功更新。");
		}

	}

} elseif($action == "u2uprune") {

	if(!$prunesubmit)
	{

?>
<br><br><br><form method="post" action="admincp.php?action=u2uprune">
<table cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<tr><td class="header" colspan="2">短消息清理 [此操作不可恢复 请慎重使用!]</td></tr>

<tr><td bgcolor="<?=$altbg1?>">不删除未读信息：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="checkbox" name="ignorenew" value="1"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">删除多少天以前的短消息(不限制时间请输入 0)：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="days" size="7"></td></tr>

<tr><td bgcolor="<?=$altbg1?>">按用户名清理(用户名间用半角逗号 "," 分割)：</td>
<td bgcolor="<?=$altbg2?>" align="right"><input type="text" name="users" size="40"></td></tr>

</table></td></tr></table><br>
<center><input type="submit" name="prunesubmit" value="执 行"></center>
</form>
<?

	} else {

		if($days == "") {
			cpmsg("您没有输入要删除短消息的时间范围，请返回修改。");
		} else {
			$pruneuser = " AND (";
			$prunenew = "";
			$or = "";

			$prunedate = $timestamp - (86400 * $days);
			$arruser = explode(",", str_replace(" ", "", $users));
			for($i = 0; $i < count($arruser); $i++) {
				$arruser[$i] = trim($arruser[$i]);
				if($arruser[$i]) {
					$pruneuser .= $or."msgto='$arruser[$i]";
					$or = " OR ";
				}
			}
			if($pruneuser = " AND (") {
				$pruneuser = "";
			} else {
				$pruneuser .= ")";
			}
			if($ignorenew) {
				$prunenew = "AND new='0'";//new=0是已读,2是双方未读,1是1方读了
			}

			$db->query("DELETE FROM $table_u2u WHERE dateline<='$prunedate' $pruneuser $prunenew");
			$num = $db->affected_rows();

			cpmsg("符合条件的 $num 条短消息成功删除。");
		}
	}

}

?>