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
//备忘录

require "header.php";
$tplnames = "css,header,footer,memcp_navbar";

if(!$action) {
	$navigation = "&raquo; 备忘录";
	$navtitle = " - 备忘录";
} else {
	if($action == "address") {
		$cdbaction = "个人通讯录";
	} elseif($action == "notebook") {
		$cdbaction = "我的记事簿";
	} elseif($action == "collections") {
		$cdbaction = "网络收藏夹";
	}
	$navigation = "&raquo; <a href=\"memo.php\">备忘录</a> &raquo; $cdbaction";
	$navtitle = " - 备忘录 - $cdbaction";
}
$useraction = "使用备忘录";

if(!$cdbuser || !$cdbpw) {
	showmessage("您还没有登录，无法进入备忘录。");
} elseif(!$maxmemonum) {
	showmessage("对不起，您的级别〔{$grouptitle}〕无法使用备忘录。");
}

$rpp = 20;
if(!$page) {
	$page = 1;
}
$start = ($page - 1) * $rpp;

$searchadd = "";
if($addsubmit)
{
	$query = $db->query("SELECT COUNT(*) FROM $table_memo WHERE username='$cdbuser'");
	if($maxmemonum < $db->result($query, 0)) {
		showmessage("您的备忘录已满，请返回删除不必要的条目。");
	}
} elseif($searchsubmit) {
	$searchadd .= $searchfrom ? " AND dateline>$timestamp-$searchfrom" : NULL;
	$searchadd .= $keyword ? " AND (var1 LIKE '%$keyword%' OR var2 LIKE '%$keyword%' OR var3 LIKE '%$keyword%')" : NULL;
	if(!$searchadd) {
		showmessage("您没有输入搜索时间或关键字，请返回。");
	}
}

if($action == "address")
{

	if(!$deletesubmit && !$addsubmit) {

		preloader("memo_address,memo_address_row");

		$query = $db->query("SELECT COUNT(*) FROM $table_memo WHERE username='$cdbuser' AND type='address' $searchadd");
		$num = $db->result($query, 0);
		$multipage = multi($num, $rpp, $page, "memo.php?action=address");
		$multipage .= $searchadd ? " &nbsp; &nbsp; 共 $num 个结果" : NULL;

		$query = $db->query("SELECT * FROM $table_memo WHERE username='$cdbuser' AND type='address' $searchadd ORDER BY dateline DESC LIMIT $start, $rpp");
		while($memo = $db->fetch_array($query)) {
			list($memo_email, $memo_site) = explode("\t", $memo[var2]);
			list($memo_tel, $memo_addr) = explode("\t", $memo[var3]);
			eval("\$addressrow .= \"".template("memo_address_row")."\";");
		}
		eval("\$address = \"".template("memo_address")."\";");
		echo $address;		

	} elseif($deletesubmit) {

		if(is_array($delete)) {
			$ids = $comma = "";
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ", ";
			}
			$db->query("DELETE FROM $table_memo WHERE username='$cdbuser' AND type='address' AND id IN ($ids)");
			showmessage("指定的条目成功删除，现在将转入通讯录。", "memo.php?action=address");
		} else {
			showmessage("您没有选择要删除的条目，请返回。");
		}

	} elseif($addsubmit) {

		if(!$memo_name) {
			showmessage("您必须输入名字，请返回修改。");
		} else {
			$var1 = $memo_name;
			$var2 = "$memo_email\t$memo_site";
			$var3 = "$memo_tel\t$memo_address";
			$db->query("INSERT INTO $table_memo (username, type, dateline, var1, var2, var3)
				VALUES ('$cdbuser', 'address', '$timestamp', '$var1', '$var2', '$var3')");
			showmessage("条目成功添加，现在将转入通讯录。", "memo.php?action=address");
		}

	}

}
elseif($action == "notebook") {

	if(!$deletesubmit && !$addsubmit) {

		preloader("memo_notebook,memo_notebook_row");

		$query = $db->query("SELECT COUNT(*) FROM $table_memo WHERE username='$cdbuser' AND type='notebook' $searchadd");
		$num = $db->result($query, 0);
		$multipage = multi($num, $rpp, $page, "memo.php?action=notebook");
		$multipage .= $searchadd ? " &nbsp; &nbsp; 共 $num 个结果" : NULL;

		$query = $db->query("SELECT * FROM $table_memo WHERE username='$cdbuser' AND type='notebook' $searchadd ORDER BY dateline DESC LIMIT $start, $rpp");
		while($memo = $db->fetch_array($query)) {
			eval("\$notebookrow .= \"".template("memo_notebook_row")."\";");
		}
		eval("\$notebook = \"".template("memo_notebook")."\";");
		echo $notebook;

	} elseif($deletesubmit) {

		if(is_array($delete)) {
			$ids = $comma = "";
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ", ";
			}
			$db->query("DELETE FROM $table_memo WHERE username='$cdbuser' AND type='notebook' AND id IN ($ids)");
			showmessage("指定的条目成功删除，现在将转入记事簿。", "memo.php?action=notebook");
		} else {
			showmessage("您没有选择要删除的条目，请返回。");
		}

	} elseif($addsubmit) {
		
		if(!$memo_title) {
			showmessage("您必须输入标题，请返回修改。");
		} else {
			$var1 = $memo_type;
			$var2 = $memo_title;
			$var3 = $memo_text;
			$db->query("INSERT INTO $table_memo (username, type, dateline, var1, var2, var3)
				VALUES ('$cdbuser', 'notebook', '$timestamp', '$var1', '$var2', '$var3')");
			showmessage("条目成功添加，现在将转入记事簿。", "memo.php?action=notebook");
		}

	}

}
elseif($action == "collections")
{
	
	if(!$deletesubmit && !$addsubmit) {

		preloader("memo_collections,memo_collections_row");

		$query = $db->query("SELECT COUNT(*) FROM $table_memo WHERE username='$cdbuser' AND type='collections' $searchadd");
		$num = $db->result($query, 0);
		$multipage = multi($num, $rpp, $page, "memo.php?action=collections");
		$multipage .= $searchadd ? " &nbsp; &nbsp; 共 $num 个结果" : NULL;

		$query = $db->query("SELECT * FROM $table_memo WHERE username='$cdbuser' AND type='collections' $searchadd ORDER BY dateline DESC LIMIT $start, $rpp");
		while($memo = $db->fetch_array($query)) {
			$memo[var2] = "<a href=\"$memo[var2]\" target=\"_blank\">".wordscut($memo[var2], 35)."</a>";
			eval("\$collectionrow .= \"".template("memo_collections_row")."\";");
		}
		eval("\$collections = \"".template("memo_collections")."\";");
		echo $collections;

	} elseif($deletesubmit) {

		if(is_array($delete)) {
			$ids = $comma = "";
			foreach($delete as $id) {
				$ids .= "$comma'$id'";
				$comma = ", ";
			}
			$db->query("DELETE FROM $table_memo WHERE username='$cdbuser' AND type='collections' AND id IN ($ids)");
			showmessage("指定的条目成功删除，现在将转入网址收藏。", "memo.php?action=collections");
		} else {
			showmessage("您没有选择要删除的条目，请返回。");
		}

	} elseif($addsubmit) {

		if(!$memo_url) {
			showmessage("您必须输入网址，请返回修改。");
		} else {
			$var1 = $memo_title;
			$var2 = $memo_url;
			$var3 = $memo_comment;
			$db->query("INSERT INTO $table_memo (username, type, dateline, var1, var2, var3)
				VALUES ('$cdbuser', 'collections', '$timestamp', '$var1', '$var2', '$var3')");
			showmessage("条目成功添加，现在将转入网址收藏。", "memo.php?action=collections");
		}

	}

}
else {

	preloader("memo_home,memo_home_addr_row,memo_home_note_row,memo_home_coll_row,memo_home_addr_none,memo_home_note_none,memo_home_coll_none");

	$countaddr = $countnote = $countcoll = 0;
	$query = $db->query("SELECT * FROM $table_memo WHERE username='$cdbuser' ORDER BY dateline DESC LIMIT 0, 30");
	$currrows = $db->num_rows($query);
	while($memo = $db->fetch_array($query)) {
		switch($memo[type]) {
			case address:
				if(++$countaddr <= 5)
				{
					$addrdetail = "";
					$detail = array();
					list($detail["Email"], $detail["主页"]) = explode("\t", $memo[var2]);
					list($detail["电话"], $detail["地址"]) = explode("\t", $memo[var3]);
					foreach($detail as $type => $value) {
						if($value) {
							if($type == "Email") {
								$value = "<a href=\"mailto:$value\">$value</a>";
							} elseif($type == "主页") {
								$value = "<a href=\"$value\">$value</a>";
							}
							$addrdetail .= "<tr bgcolor=\"$altbg2\"><td class=\"bold\">$type</td><td>$value</td></tr>\n";
						}
					}
					eval("\$address .= \"".template("memo_home_addr_row")."\";");
				}
				break;
			case notebook:
				if(++$countnote <= 5) {
					eval("\$notebook .= \"".template("memo_home_note_row")."\";");
				}
				break;
			case collections:
				if(++$countcoll <= 5) {
					eval("\$collections .= \"".template("memo_home_coll_row")."\";");
				}
				break;
		}
	}

	$db->free_result($query);
	eval("\$memo = \"".template("memo_home")."\";");
	echo $memo;

}

gettotaltime();
eval("\$footer = \"".template("footer")."\";");
echo $footer;

cdb_output();

?>