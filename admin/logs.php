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

$logs = array();
$logdir = "./datatemp";
$maxlogrows = 500;
$lpp = 30;

if($action == "illegallog") {
	$filename = "$logdir/illegallog.php";
} elseif($action == "modslog") {
	$filename = "$logdir/modslog.php";
} elseif($action == "cplog") {
	$filename = "$logdir/cplog.php";
}

@$logfile = file($filename);
@$fp = fopen($filename, "w");
@flock($fp, 3);
@fwrite($fp, "<?PHP exit(\"Access Denied\"); ?>\n");

for($i = count($logfile) - $maxlogrows; $i < count($logfile); $i++) {
	if(strpos($logfile[$i], "\t")) {
		$logfile[$i] = trim($logfile[$i]);
		$logs[] = $logfile[$i];
		@fwrite($fp, "$logfile[$i]\n");
	}
}
@fclose($fp);

if(!$page) {
	$page = 1;
}
$start = ($page - 1) * $lpp;
$logs = array_reverse($logs);
$num = count($logs);
$multipage = multi($num, $lpp, $page, "admincp.php?action=$action");

for($i = 0; $i < $start; $i++) {
	unset($logs[$i]);
}
for($i = $start + $lpp; $i < $num; $i++) {
	unset($logs[$i]);
}

?>
<table cellspacing="0" cellpadding="0" border="0" width="95%" align="center">
<tr class="multi"><td><?=$multipage?></td></tr>
<tr><td bgcolor="<?=$bordercolor?>">
<table border="0" cellspacing="<?=$borderwidth?>" cellpadding="<?=$tablespace?>" width="100%">
<?

if($action == "illegallog") {

	echo "<tr class=\"header\"><td colspan=\"4\">密码错误记录</td></tr>\n".
		"<tr class=\"header\" align=\"center\"><td>尝试用户名</td><td>尝试密码</td><td>IP 地址</td><td>时间</td></tr>\n";

	foreach($logs as $logrow) {
		$log = explode("\t", $logrow);
		$log[0] = addslashes($log[0]);
		$log[3] = gmdate("$dateformat $timeformat", $log[3] + $timeoffset * 3600);

		echo "<tr align=\"center\"><td bgcolor=\"$altbg1\" width=\"25%\">$log[0]</td>\n".
			"<td bgcolor=\"$altbg2\" width=\"25%\">$log[1]</td><td bgcolor=\"$altbg1\" width=\"25%\">$log[2]</td>\n".
			"<td bgcolor=\"$altbg2\" width=\"25%\">$log[3]</td></tr>\n";
	}

} elseif($action == "modslog") {

	echo "<tr class=\"header\"><td colspan=\"7\">版主管理记录</td></tr>\n".
		"<tr class=\"header\" align=\"center\"><td>用户名</td><td>头衔</td><td>IP 地址</td><td>时间</td><td>论坛</td><td>贴子</td><td>动作</td></tr>\n";

	foreach($logs as $logrow) {
		$log = explode("\t", $logrow);
		$log[0] = addslashes($log[0]);
		if($log[0] != $cdbuser) {
			$log[0] = "<b>$log[0]</b>";
		}
		$log[3] = gmdate("$dateformat $timeformat", $log[3] + $timeoffset * 3600);
		$log[5] = "<a href=\"./forumdisplay.php?fid=$log[4]\" target=\"_blank\">$log[5]</a>";
		$log[7] = "<a href=\"./viewthread.php?tid=$log[6]\" target=\"_blank\">".wordscut($log[7], 15)."</a>";

		echo "<tr align=\"center\"><td bgcolor=\"$altbg1\" width=\"10%\">$log[0]</td>\n".
			"<td bgcolor=\"$altbg2\" width=\"15%\">$log[1]</td><td bgcolor=\"$altbg1\" width=\"10%\">$log[2]</td>\n".
			"<td bgcolor=\"$altbg2\" width=\"22%\">$log[3]</td><td bgcolor=\"$altbg1\" width=\"15%\">$log[5]</td>\n".
			"<td bgcolor=\"$altbg2\" width=\"15%\">$log[7]</td><td bgcolor=\"$altbg1\" width=\"13%\">$log[8]</td></tr>\n";
	}
	
} elseif($action == "cplog") {

	echo "<tr class=\"header\"><td colspan=\"5\">系统管理记录</td></tr>\n".
		"<tr class=\"header\" align=\"center\"><td>管理员</td><td>IP 地址</td><td>时间</td><td>动作</td><td>其他</td></tr>\n";

	foreach($logs as $logrow) {
		$log = explode("\t", $logrow);
		$log[0] = addslashes($log[0]);
		if($log[0] != $cdbuser) {
			$log[0] = "<b>$log[0]</b>";
		}
		$log[2] = gmdate("$dateformat $timeformat", $log[2] + $timeoffset * 3600);

		echo "<tr align=\"center\"><td bgcolor=\"$altbg1\" width=\"15%\">$log[0]</td>\n".
			"<td bgcolor=\"$altbg2\" width=\"15%\">$log[1]</td><td bgcolor=\"$altbg1\" width=\"25%\">$log[2]</td>\n".
			"<td bgcolor=\"$altbg2\" width=\"15%\">$log[3]</td><td bgcolor=\"$altbg1\" width=\"30%\">$log[4]</td></tr>\n";
	}

}

?>
</table></td></tr><tr class="multi"><td><?=$multipage?></td></tr>
</table>