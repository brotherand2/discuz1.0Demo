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


error_reporting(7);
set_magic_quotes_runtime(0);
define("IN_CDB", TRUE);
$action = ($_POST["action"]) ? $_POST["action"] : $_GET["action"];
$PHP_SELF = $_SERVER["PHP_SELF"];

if (function_exists("set_time_limit") == 1 && @ini_get("safe_mode") == 0) {
	@set_time_limit(1000);
}

@require "./config.php";
require "./functions.php";

header("Content-Type: text/html; charset=$charset");
$version = "1.0";

function process($name) {
	global $tablepre;
	echo"建立数据表 ".$tablepre.$name." ";
}

function insert($name) {
	global $tablepre;
	echo"插入数据至 ".$tablepre.$name." ";
}

function result($result = 1, $output = 1) {
	if($result) {
		$text = "... <font color=\"#0000EE\">成功</font><br>";
		if(!$output) {
			return $text;
		}
		echo $text;
	} else {
		$text = "... <font color=\"#FF0000\">失败</font><br>";
		if(!$output) {
			return $text;
		}
		echo $text;
	}
}

function dir_writeable($dir) {
	if(!is_dir($dir)) {
		@mkdir($dir, 0777);
	}
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/test.test", "w")) {
			@fclose($fp);
			@unlink("$attachdir/test.test");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}

?>
<html>
<head>
<title>Discuz! Installation Wizard</title>
<style>
A:visited	{COLOR: #3A4273; TEXT-DECORATION: none}
A:link		{COLOR: #3A4273; TEXT-DECORATION: none}
A:hover		{COLOR: #3A4273; TEXT-DECORATION: underline}
p		{TEXT-INDENT : 15px}
body,table,td	{COLOR: #3A4273; FONT-FAMILY: Tahoma, Verdana, Arial; FONT-SIZE: 11px; LINE-HEIGHT: 20px; scrollbar-base-color: #E3E3EA; scrollbar-arrow-color: #5C5C8D}
input		{COLOR: #085878; FONT-FAMILY: Tahoma, Verdana, Arial; FONT-SIZE: 12px; background-color: #3A4273; color: #FFFFFF; scrollbar-base-color: #E3E3EA; scrollbar-arrow-color: #5C5C8D}
.install	{FONT-FAMILY: Arial, Verdana; FONT-SIZE: 20px; FONT-WEIGHT: bold; COLOR: #000000}
</style>
</head>

<body bgcolor="#3A4273" text="#000000">
<table width="95%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
  <tr>
    <td>
      <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr> 
          <td class="install" height="30" valign="bottom"><font color="#FF0000">&gt;&gt;</font> 
            Discuz! Installation Wizard</td>
        </tr>
        <tr>
          <td> 
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td align="center"> 
            <b>欢迎来到 Crossday Discuz! Board 安装向导，安装前请仔细阅读 licence 档的每个细节，在您确定可以完全满足 Discuz! 的授权协议之后才能开始安装。readme 档提供了有关软件安装的说明，请您同样仔细阅读，以保证安装进程的顺利进行。</b>
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
<?

if(!$action) {

?>
        <tr> 
          <td><b>当前状态：</b><font color="#0000EE">Discuz! 用户许可协议</font></td>
        </tr>
        <tr> 
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> 请您务必仔细阅读下面的许可协议</font></b></td>
        </tr>
        <tr>
          <td><br>
            <table width="90%" cellspacing="1" bgcolor="#000000" border="0" align="center">
              <tr>
                <td bgcolor="#E3E3EA">
                  <table width="99%" cellspacing="1" border="0" align="center">
                    <tr>
                      <td>
版权所有 (c) 2002, Crossday Studio<br>
保留所有权力.<br><br>

感谢您选择 Discuz! 论坛产品。希望我们的努力能为您提供一个高效快速和强大的 web 论坛
解决方案。<br><br>

Discuz! 英文全称为 Crossday Discuz! Board，中文全称为 Discuz! 论坛。<br>
Crossday Studio 为实易数码 (http://www.11cn.org) 从事 Discuz! 项目开发的工作室。<br><br>

Discuz! 由 Crossday Studio 独立开发，技术支持论坛官方网站为 http://www.Discuz.net；
Crossday Studio 官方网站为 http://www.crossday.com。<br><br>

本授权协议适用且仅适用于 Discuz! 1.0 版本，Crossday Studio 拥有对任何版本 Discuz!
授权协议的完全的最终解释和修改权。<br><br>

在开始安装 Discuz! 之前，请务必仔细阅读本授权文档，在您确定符合授权协议的全部条件
后，即可继续 Discuz! 论坛的安装。即：您一旦开始安装 Discuz!，即被视为完全同意本授
权协议的全部内容，如果出现问题，我们将根据协议上的条款追究。<br><br>

对于个人用户，您手中目前版本的 Discuz! 为免费软件，您可以免费获得本程序，并安装在
自己的主机上，作为个人网站的一部分，而不必支付费用。但我们也不承诺对个人用户提供任
何形式的技术支持。<br><br>

对于商业用户，或个人用户将 Discuz! 用于商业场合，Discuz! 为商业软件，您必须支付商业
授权费用，获得我们的正式授权后，才能使用 Discuz!，否则一切与商业活动有关的 Discuz!
应用，如应用于客户服务论坛、商业产品论坛、商业公司讨论板等等都是非法的，必将得到严
厉的追究。购买的 Discuz! 授权包含指定范围内的技术支持服务，有关商业授权的价格、付费
方式、技术支持信息，Discuz! 技术支持论坛提供惟一的官方价目表和解释。<br><br>

无论如何，既无论用途如何、是否经过修改或美化，只要您使用 Discuz! 的任何整体或部分，
页脚处的 Discuz! 名称和 Crossday Studio (http://www.Discuz.net) 的链接都必须保留而
不能清除或修改。<br><br>

您可以查看 Discuz! 的全部源代码，也可以根据自己的需要对其进行修改，但只要 Discuz!
程序的任何部分被包含在您修改后的系统中，无论修改程度如何，都必须保留页脚处的 Discuz!
名称和 Crossday Studio 的链接地址。您修改后的代码，在没有获得我们 (Crossday Studio，
http://www.crossday.com) 的正式许可的情况下，严禁公开发布或发售。<br><br>

对于且仅对于个人用户，Discuz! 是开放源代码的免费软件，欢迎您在原样完整保留全部版权
信息和说明档的前提下，传播和发布本程序，但违背上述条款或未支付授权费用或未经我们正
式许可而将 Discuz! 用于商业目的使用和传播都是被禁止的。同时欢迎对 Discuz! 感兴趣并
有实力的团体或个人对 Discuz! 的开发提供支持。<br><br>

安装 Discuz! 建立在完全同意本授权文档的基础之上，因此而产生的纠纷，违反本授权协议
的一方将承担全部责任。
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          </td>
        </tr>
        <tr>
          <td align="center">
            <br>
            <form method="post" action="<?=$PHP_SELF?>">
              <input type="hidden" name="action" value="config">
              <input type="submit" name="submit" value="我完全同意" style="height: 25">&nbsp;
              <input type="button" name="exit" value="我不能同意" style="height: 25" onclick="javascript: window.close();">
            </form>
          </td>
        </tr>
<?

} elseif($action == "config") {

	$exist_error = FALSE;
	$write_error = FALSE;
	if(file_exists("./config.php")) {
		$fileexists = result(1, 0);
	} else {
		$fileexists = result(0, 0);
		$exist_error = TRUE;
	}
	if(is_writeable("./config.php")) {
		$filewriteable = result(1, 0);
	} else {
		$filewriteable = result(0, 0);
		$write_error = TRUE;
	}
	if($exist_error) {
		$config_info = "您的 config.php 不存在, 无法继续安装, 请用 FTP 将该文件上传后再试.";
	} elseif(!$write_error) {
		$config_info = "请在下面填写您的数据库账号信息, 通常情况下请不要修改红色选项内容.";
	} elseif($write_error) {
		$config_info = "安装向导无法写入配置文件, 请核对现有信息, 如需修改, 请通过 FTP 将改好的 config.php 上传.";
	}

?>
        <tr> 
          <td><b>当前状态：</b><font color="#0000EE">配置 config.php</font></td>
        </tr>
        <tr> 
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> 检查配置文件状态</font></b></td>
        </tr>
        <tr>
          <td>config.php 存在检查 <?=$fileexists?></td>
        </tr>
        <tr>
          <td>config.php 可写检查 <?=$filewriteable?></td>
        </tr>
        <tr> 
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> 浏览/编辑当前配置</font></b></td>
        </tr>
        <tr>
          <td align="center"><br><?=$config_info?></td>
        </tr>
<?

	if(!$exist_error) {

		if(!$write_error) {

			$dbhost = "localhost";
			$dbuser = "dbuser";
			$dbpw = "dbpw";
			$dbname = "dbname";
			$adminemail = "admin@domain.com";
			$tablepre = "cdb_";

			@include "./config.php";

?>
        <tr>
          <td align="center">
            <br>
            <form method="post" action="<?=$PHP_SELF?>">
              <table width="500" cellspacing="1" bgcolor="#000000" border="0" align="center">
                <tr bgcolor="#3A4273">
                  <td align="center" width="20%" style="color: #FFFFFF">设置选项</td>
                  <td align="center" width="35%" style="color: #FFFFFF">当前值</td>
                  <td align="center" width="45%" style="color: #FFFFFF">注释</td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA">&nbsp;数据库服务器:</td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="dbhost" value="<?=$dbhost?>" size="30"></td>
                  <td bgcolor="#E3E3EA">&nbsp;数据库服务器地址, 一般为 localhost</td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA">&nbsp;数据库用户名:</td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="dbuser" value="<?=$dbuser?>" size="30"></td>
                  <td bgcolor="#E3E3EA">&nbsp;数据库账号用户名</td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA">&nbsp;数据库密码:</td>
                  <td bgcolor="#EEEEF6" align="center"><input type="password" name="dbpw" value="<?=$dbpw?>" size="30"></td>
                  <td bgcolor="#E3E3EA">&nbsp;数据库账号密码</td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA">&nbsp;数据库名:</td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="dbname" value="<?=$dbname?>" size="30"></td>
                  <td bgcolor="#E3E3EA">&nbsp;数据库名称</td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA">&nbsp;系统 Email:</td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="adminemail" value="<?=$adminemail?>" size="30"></td>
                  <td bgcolor="#E3E3EA">&nbsp;用于发送程序错误报告</td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA" style="color: #FF0000">&nbsp;表名前缀:</td>
                  <td bgcolor="#EEEEF6" align="center"><input type="text" name="tablepre" value="<?=$tablepre?>" size="30" onClick="javascript: alert('安装向导提示:\n\n除非您需要在同一数据库安装多个 CDB\n论坛,否则,强烈建议您不要修改表名前缀.');"></td>
                  <td bgcolor="#E3E3EA">&nbsp;同一数据库安装多论坛时使用</td>
                </tr>
              </table>
              <br>
              <input type="hidden" name="action" value="environment">
              <input type="hidden" name="saveconfig" value="1">
              <input type="submit" name="submit" value="保存配置信息" style="height: 25">
              <input type="button" name="exit" value="退出安装向导" style="height: 25" onclick="javascript: window.close();">
            </form>
          </td>
        </tr>
<?

		} else {

			@include "./config.php";

?>
        <tr>
          <td>
            <br>
            <table width="60%" cellspacing="1" bgcolor="#000000" border="0" align="center">
              <tr bgcolor="#3A4273">
                <td align="center" style="color: #FFFFFF">变量</td>
                <td align="center" style="color: #FFFFFF">当前值</td>
                <td align="center" style="color: #FFFFFF">注释</td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">$dbhost</td>
                <td bgcolor="#EEEEF6" align="center"><?=$dbhost?></td>
                <td bgcolor="#E3E3EA" align="center">数据库服务器, 一般为 localhost</td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">$dbuser</td>
                <td bgcolor="#EEEEF6" align="center"><?=$dbuser?></td>
                <td bgcolor="#E3E3EA" align="center">数据库账号(用户名)</td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">$dbpw</td>
                <td bgcolor="#EEEEF6" align="center"><?=$dbpw?></td>
                <td bgcolor="#E3E3EA" align="center">数据库密码</td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">$dbname</td>
                <td bgcolor="#EEEEF6" align="center"><?=$dbname?></td>
                <td bgcolor="#E3E3EA" align="center">数据库名称</td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">$adminemail</td>
                <td bgcolor="#EEEEF6" align="center"><?=$adminemail?></td>
                <td bgcolor="#E3E3EA" align="center">系统 Email</td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">$tablepre</td>
                <td bgcolor="#EEEEF6" align="center"><?=$tablepre?></td>
                <td bgcolor="#E3E3EA" align="center">数据表名前缀</td>
              </tr>
            </table>
            <br>
          </td>
        </tr>
        <tr>
          <td align="center">
            <form method="post" action="<?=$PHP_SELF?>">
              <input type="hidden" name="action" value="environment">
              <input type="submit" name="submit" value="上述配置正确" style="height: 25">
              <input type="button" name="exit" value="刷新修改结果" style="height: 25" onclick="javascript: window.location=('<?=$PHP_SELF?>?action=config');">
            </form>
          </td>
        </tr>
<?

		}

	} else {

?>
        <tr>
          <td align="center">
            <br>
            <form method="post" action="<?=$PHP_SELF?>">
              <input type="hidden" name="action" value="config">
              <input type="submit" name="submit" value="重新检查设置" style="height: 25">
              <input type="button" name="exit" value="退出安装向导" style="height: 25" onclick="javascript: window.close();">
            </form>
          </td>
        </tr>
<?

	}

} elseif($action == "environment") {

	if($_POST["saveconfig"] && is_writeable("./config.php")) {

		$dbhost = $_POST["dbhost"];
		$dbuser = $_POST["dbuser"];
		$dbpw = $_POST["dbpw"];
		$dbname = $_POST["dbname"];
		$adminemail = $_POST["adminemail"];
		$tablepre = $_POST["tablepre"];

		$fp = fopen("./config.php", "r");
		$configfile = fread($fp, filesize("./config.php"));
		fclose($fp);

		$configfile = preg_replace("/[$]dbhost\s*\=\s*\".*?\"/is", "\$dbhost = \"$dbhost\"", $configfile);
		$configfile = preg_replace("/[$]dbuser\s*\=\s*\".*?\"/is", "\$dbuser = \"$dbuser\"", $configfile);
		$configfile = preg_replace("/[$]dbpw\s*\=\s*\".*?\"/is", "\$dbpw = \"$dbpw\"", $configfile);
		$configfile = preg_replace("/[$]dbname\s*\=\s*\".*?\"/is", "\$dbname = \"$dbname\"", $configfile);
		$configfile = preg_replace("/[$]adminemail\s*\=\s*\".*?\"/is", "\$adminemail = \"$adminemail\"", $configfile);
		$configfile = preg_replace("/[$]tablepre\s*\=\s*\".*?\"/is", "\$tablepre = \"$tablepre\"", $configfile);

		$fp = fopen("./config.php", "w");
		fwrite($fp, trim($configfile));
		fclose($fp);

	}

	include "./config.php";
	include "./lib/$database.php";
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);

	$msg = "";
	$quit = FALSE;

	$curr_os = PHP_OS;

	$curr_php_version = PHP_VERSION;
	if($curr_php_version < "4.0.0") {
		$msg .= "<font color=\"#FF0000\">您的 PHP 版本小于 4.0.0, 无法使用 Discuz!.</font>\t";
		$quit = TRUE;
	} elseif($curr_php_version < "4.0.6") {
		$msg .= "<font color=\"#FF0000\">您的 PHP 版本小于 4.0.6, 无法使用头像尺寸检查和 gzip 压缩功能.</font>\t";
	}

	if(@ini_get(file_uploads)) {
		$max_size = @ini_get(upload_max_filesize);
		$curr_upload_status = "允许/最大 $max_size";
		$msg .= "您可以上传尺寸在 $max_size 以下的附件文件.\t";
	} else {
		$curr_upload_status = "不允许上传附件";
		$msg .= "<font color=\"#FF0000\">由于服务器屏蔽, 您无法使用附件功能.</font>\t";
	}

	$query = $db->query("SELECT VERSION()");
	$curr_mysql_version = $db->result($query, 0);
	if($curr_mysql_version < "3.23") {
		$msg .= "<font color=\"#FF0000\">您的 MySQL 版本低于 3.23, Discuz! 的一些功能可能无法正常使用.</font>\t";
	}

	$curr_disk_space = intval(diskfreespace(".") / (1024 * 1024))."M";

	if(dir_writeable($attachdir)) {
		$curr_attach_writeable = "可写";
	} else {
		$curr_attach_writeable = "不可写";
		$msg .= "<font color=\"#FF0000\">附件 $attachdir 目录属性非 777 或无法写入, 无法使用附件功能.</font>\t";
	}

	if(dir_writeable("./datatemp")) {
		$curr_data_writeable = "可写";
	} else {
		$curr_data_writeable = "不可写";
		$msg .= "<font color=\"#FF0000\">数据 (./datatemp) 目录属性非 777 或无法写入, 无法使用备份到服务器/管理记录/数据库记录等功能.</font>\t";
	}

	$db->select_db($dbname);
	if($db->error()) {
		$db->query("CREATE DATABASE $dbname");
		if($db->error()) {
			$msg .= "<font color=\"#FF0000\">指定的数据库 $dbname 不存在, 系统也无法自动建立, 无法安装 Discuz!.</font>\t";
			$quit = TRUE;
		} else {
			$db->select_db($dbname);
			$msg .= "指定的数据库 $dbname 不存在, 但系统已成功建立, 可以继续安装.\t";
		}
	}

	$query - $db->query("SELECT COUNT(*) FROM $tablepre"."settings", 1);
	if(!$db->error()) {
		$msg .= "<font color=\"#FF0000\">数据库中已经安装过 Discuz!, 继续安装会清空原有数据.</font>\t";
		$alert = " onSubmit=\"return confirm('继续安装会清空全部原有数据，您确定要继续吗?');\"";
	} else {
		$alert = "";
	}

	if($quit) {
		$msg .= "<font color=\"#FF0000\">由于服务器配置原因, 您无法安装和使用 Discuz!, 请退出安装向导.</font>";
	} else {
		$msg .= "您的服务器可以安装和使用 Discuz!, 请进入下一步安装.";
	}
?>
        <tr>
          <td><b>当前状态：</b><font color="#0000EE">检查当前服务器环境</font></td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> Discuz! 所需环境和当前服务器配置对比</font></b></td>
        </tr>
        <tr>
          <td>
            <br>
            <table width="80%" cellspacing="1" bgcolor="#000000" border="0" align="center">
              <tr bgcolor="#3A4273">
                <td align="center"></td>
                <td align="center" style="color: #FFFFFF">Discuz! 所需配置</td>
                <td align="center" style="color: #FFFFFF">Discuz! 最佳配置</td>
                <td align="center" style="color: #FFFFFF">当前服务器</td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">操作系统</td>
                <td bgcolor="#EEEEF6" align="center">不限</td>
                <td bgcolor="#E3E3EA" align="center">UNIX/Linux/FreeBSD</td>
                <td bgcolor="#E3E3EA" align="center"><?=$curr_os?></td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">PHP 版本</td>
                <td bgcolor="#EEEEF6" align="center">4.0.0 以上</td>
                <td bgcolor="#E3E3EA" align="center">4.0.6 以上</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_php_version?></td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">附件上传</td>
                <td bgcolor="#EEEEF6" align="center">不限</td>
                <td bgcolor="#E3E3EA" align="center">允许</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_upload_status?></td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">MySQL 版本</td>
                <td bgcolor="#EEEEF6" align="center">3.23 以上</td>
                <td bgcolor="#E3E3EA" align="center">3.23.51</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_mysql_version?></td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">磁盘空间</td>
                <td bgcolor="#EEEEF6" align="center">2M 以上</td>
                <td bgcolor="#E3E3EA" align="center">50M 以上</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_disk_space?></td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center"><?=$attachdir?> 目录写入</td>
                <td bgcolor="#EEEEF6" align="center">不限</td>
                <td bgcolor="#E3E3EA" align="center">可写</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_attach_writeable?></td>
              </tr>
              <tr>
                <td bgcolor="#E3E3EA" align="center">./datatemp 目录写入</td>
                <td bgcolor="#EEEEF6" align="center">可写</td>
                <td bgcolor="#E3E3EA" align="center">可写</td>
                <td bgcolor="#EEEEF6" align="center"><?=$curr_data_writeable?></td>
              </tr>
            </table>
            <br>
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> 请确认您已完成以下步骤</font></b></td>
        </tr>
        <tr>
          <td>
            <br>
            <ol>
              <li>将 Discuz! 目录下全部文件和目录上传到服务器.</li>
              <li>修改服务器上的 config.php 文件以适合您的配置.</li>
              <li>如果您使用非 WIN32 系统请修改以下属性:<br>&nbsp; &nbsp; ./config.php 文件 777<br>
              &nbsp; &nbsp; <?=$attachdir?> 目录 777;&nbsp; &nbsp; ./datatemp 目录 777;<br></li>
              <li>确认 URL 中 <?=$attachurl?> 可以访问服务器目录 <?=$attachdir?> 内容.</li>
            </ol>
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> 安装向导提示</font></b></td>
        </tr>
        <tr>
          <td>
            <br>
            <ol>
<?

	$msgs = explode("\t", $msg);
	unset($msg);
	for($i = 0; $i < count($msgs); $i++) {
		echo "              <li>".$msgs[$i]."</li>\n";
	}
	echo"            </ol>\n";

	if($quit) {

?>
            <center>
            <input type="button" name="refresh" value="重新检查设置" style="height: 25" onclick="javascript: window.location=('<?=$PHP_SELF?>?action=environment');">&nbsp;
            <input type="button" name="exit" value="退出安装向导" style="height: 25" onclick="javascript: window.close();">
            </center>
<?

	} else {

?>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> 设置管理员账号</font></b></td>
        </tr>
        <tr>
          <td align="center">
            <br>
            <form method="post" action="<?=$PHP_SELF?>"<?=$alert?>>
              <table width="300" cellspacing="1" bgcolor="#000000" border="0" align="center">
                <tr>
                  <td bgcolor="#E3E3EA" width="40%">&nbsp;管理员用户名:</td>
                  <td bgcolor="#EEEEF6" width="60%"><input type="text" name="username" value="Crossday" size="30"></td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA" width="40%">&nbsp;管理员 Email:</td>
                  <td bgcolor="#EEEEF6" width="60%"><input type="text" name="email" value="name@domain.com" size="30"></td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA" width="40%">&nbsp;管理员密码:</td>
                  <td bgcolor="#EEEEF6" width="60%"><input type="password" name="password1" size="30"></td>
                </tr>
                <tr>
                  <td bgcolor="#E3E3EA" width="40%">&nbsp;重复密码:</td>
                  <td bgcolor="#EEEEF6" width="60%"><input type="password" name="password2" size="30"></td>
                </tr>
              </table>
              <br>
              <input type="hidden" name="action" value="install">
              <input type="submit" name="submit" value="开始安装 Discuz!" style="height: 25" >&nbsp;
              <input type="button" name="exit" value="退出安装向导" style="height: 25" onclick="javascript: window.close();">
            </form>
          </td>
        </tr>

<?

	}	

} elseif($action == "install") {

	$username = $_POST["username"];
	$email = $_POST["email"];
	$password1 = $_POST["password1"];
	$password2 = $_POST["password2"];

?>
        <tr>
          <td><b>当前状态：</b><font color="#0000EE">检查管理员账号信息并开始安装 Discuz!。</font></td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr> 
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> 检查管理员账号</font></b></td>
        </tr>
        <tr>
          <td>检查信息合法性
<?

	$msg = "";
	if($username && $email && $password1 && $password2) {
		if($password1 != $password2) {
			$msg = "两次输入密码不一致.";
		} elseif(strlen($username) > 25) {
			$msg = "用户名超过 25 个字符限制.";
		} elseif(preg_match("/^$|^c:\\con\\con$|[\s\t\<\>]|^游客/is", $username)) {
			$msg = "用户名空或包含非法字符.";
		} elseif(!strstr($email, "@") || $email != stripslashes($email) || $email != htmlspecialchars($email)) {
			$msg = "Email 地址无效";
		}
	} else {
		$msg = "您的信息没有填写完整.";
	}

	if($msg) { 

?>
            ... <font color="#FF0000">失败. 原因: <?=$msg?></font></td>
        </tr>
        <tr>
          <td align="center">
            <br>
            <input type="button" name="back" value="返回上一页修改" onclick="javascript: history.go(-1);">
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr> 
          <td align="center">
            <b>Powered by <a href="http://www.Discuz.net" target="_blank">Discuz! <?=$version?></a> , &nbsp; Copyright &copy; <a href="http://www.crossday.com" target=\"_blank\">Crossday Studio</a>, 2002</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
</body>
</html>


<?

		exit;
	} else {
		echo result(1, 0)."</td>\n";
		echo"        </tr>\n";
	}

?>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr> 
          <td><b><font color="#FF0000">&gt;</font><font color="#000000"> 选择数据库</font></b></td>
        </tr>
<?
	include "./config.php";
	include "./lib/$database.php";
	$db = new dbstuff;
	$db->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	$db->select_db($dbname);

echo"        <tr>\n";
echo"          <td>选择数据库 $dbname ".result(1, 0)."</td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td>\n";
echo"            <hr noshade align=\"center\" width=\"100%\" size=\"1\">\n";
echo"          </td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td><b><font color=\"#FF0000\">&gt;</font><font color=\"#000000\"> 建立数据表结构</font></b></td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td>\n";

process("announcements");

	$db->query("DROP TABLE IF EXISTS {$tablepre}announcements", 1);
	$db->query("CREATE TABLE {$tablepre}announcements (
		id smallint(6) unsigned NOT NULL auto_increment,
		author varchar(25) NOT NULL default '',
		subject varchar(250) NOT NULL default '',
		starttime int(10) unsigned NOT NULL default '0',
		endtime int(10) unsigned NOT NULL default '0',
		message text NOT NULL,
		PRIMARY KEY  (id)
	)charset=utf8");

result();

process("attachments");

	$db->query("DROP TABLE IF EXISTS {$tablepre}attachments", 1);
	$db->query("CREATE TABLE {$tablepre}attachments (
		aid mediumint(8) unsigned NOT NULL auto_increment,
		tid mediumint(8) unsigned NOT NULL default '0',
		pid int(10) unsigned NOT NULL default '0',
		creditsrequire smallint(6) unsigned NOT NULL default '0',
		filename varchar(255) NOT NULL default '',
		filetype varchar(50) NOT NULL default '',
		filesize int(12) unsigned NOT NULL default '0',
		attachment varchar(255) NOT NULL default '',
		downloads smallint(6) NOT NULL default '0',
		PRIMARY KEY (aid)
	)charset=utf8");

result();

process("banned");

	$db->query("DROP TABLE IF EXISTS {$tablepre}banned;", 1);
	$db->query("CREATE TABLE {$tablepre}banned (
		id smallint(6) unsigned NOT NULL auto_increment,
		ip1 smallint(3) NOT NULL default '0',
		ip2 smallint(3) NOT NULL default '0',
		ip3 smallint(3) NOT NULL default '0',
		ip4 smallint(3) NOT NULL default '0',
		admin varchar(25) NOT NULL default '',
		dateline int(10) unsigned NOT NULL default '0',
		PRIMARY KEY (id),
		KEY ip1 (ip1),
		KEY ip2 (ip2),
		KEY ip3 (ip3),
		KEY ip4 (ip1)
	)charset=utf8");

result();

process("buddys");

	$db->query("DROP TABLE IF EXISTS {$tablepre}buddys;", 1);
	$db->query("CREATE TABLE {$tablepre}buddys (
		username varchar(25) NOT NULL default '',
		buddyname varchar(25) NOT NULL default ''
	)charset=utf8");

result();

process("caches");

	$db->query("DROP TABLE IF EXISTS {$tablepre}caches;", 1);
	$db->query("CREATE TABLE {$tablepre}caches (
		cachename varchar(20) NOT NULL default '',
		cachevars text NOT NULL,
		KEY cachename (cachename)
	)charset=utf8");

result();

process("favorites");

	$db->query("DROP TABLE IF EXISTS {$tablepre}favorites;", 1);
	$db->query("CREATE TABLE {$tablepre}favorites (
		tid mediumint(8) unsigned NOT NULL default '0',
		username varchar(25) NOT NULL default '',
		KEY tid (tid)
	)charset=utf8");

result();

process("forumlinks");

	$db->query("DROP TABLE IF EXISTS {$tablepre}forumlinks;", 1);
	$db->query("CREATE TABLE {$tablepre}forumlinks (
		id smallint(6) unsigned NOT NULL auto_increment,
		displayorder tinyint(3) NOT NULL default '0',
		name varchar(100) NOT NULL default '',
		url varchar(100) NOT NULL default '',
		note varchar(200) NOT NULL default '',
		logo varchar(100) NOT NULL default '',
		PRIMARY KEY (id)
	)charset=utf8");

result();

process("forums");

	$db->query("DROP TABLE IF EXISTS {$tablepre}forums;", 1);
	$db->query("CREATE TABLE {$tablepre}forums (
		fid smallint(6) unsigned NOT NULL auto_increment,
		fup smallint(6) unsigned NOT NULL default '0',
		type enum('group','forum','sub') NOT NULL default 'forum',
		icon varchar(100) NOT NULL default '',
		name varchar(50) NOT NULL default '',
		description text NOT NULL,
		status tinyint(1) NOT NULL default '0',
		displayorder tinyint(3) NOT NULL default '0',
		moderator tinytext NOT NULL,
		threads smallint(6) unsigned NOT NULL default '0',
		posts mediumint(8) unsigned NOT NULL default '0',
		lastpost varchar(130) NOT NULL default '',
		allowsmilies tinyint(1) NOT NULL default '0',
		allowhtml tinyint(1) NOT NULL default '0',
		allowbbcode tinyint(1) NOT NULL default '0',
		allowimgcode tinyint(1) NOT NULL default '0',
		password varchar(12) NOT NULL default '',
		postcredits tinyint(1) NOT NULL default '-1',
		viewperm tinytext NOT NULL,
		postperm tinytext NOT NULL,
		getattachperm tinytext NOT NULL,
		postattachperm tinytext NOT NULL,
		PRIMARY KEY (fid),
		KEY status (status)
	)charset=utf8");

result();

process("members");

	$db->query("DROP TABLE IF EXISTS {$tablepre}members;", 1);
	$db->query("CREATE TABLE {$tablepre}members (
		uid mediumint(8) unsigned NOT NULL auto_increment,
		username varchar(25) NOT NULL default '',
		password varchar(40) NOT NULL default '',
		gender tinyint(1) NOT NULL default '0',
		status varchar(20) NOT NULL default '',
		regip varchar(20) NOT NULL default '',
		regdate int(10) unsigned NOT NULL default '0',
		lastvisit int(10) unsigned NOT NULL default '0',
		postnum smallint(6) unsigned NOT NULL default '0',
		credit smallint(6) NOT NULL default '0',
		charset varchar(10) NOT NULL default '0',
		email varchar(60) NOT NULL default '',
		site varchar(75) NOT NULL default '',
		icq varchar(12) NOT NULL default '',
		oicq varchar(12) NOT NULL default '',
		yahoo varchar(40) NOT NULL default '',
		msn varchar(40) NOT NULL default '',
		location varchar(30) NOT NULL default '',
		bday date NOT NULL default '0000-00-00',
		bio text NOT NULL,
		avatar varchar(100) NOT NULL default '',
		signature text NOT NULL,
		customstatus varchar(20) NOT NULL default '',
		tpp tinyint(3) unsigned NOT NULL default '0',
		ppp tinyint(3) unsigned NOT NULL default '0',
		theme varchar(30) NOT NULL default '',
		dateformat varchar(10) NOT NULL default '',
		timeformat varchar(5) NOT NULL default '',
		showemail tinyint(1) NOT NULL default '0',
		newsletter tinyint(1) NOT NULL default '0',
		timeoffset char(3) NOT NULL default '0',
		ignoreu2u text NOT NULL,
		newu2u tinyint(1) NOT NULL default '0',
		pwdrecover varchar(30) NOT NULL default '',
		pwdrcvtime int(10) unsigned NOT NULL default '0',
		PRIMARY KEY (uid),
		KEY username (username)
	)charset=utf8");

result();

process("memo");

	$db->query("DROP TABLE IF EXISTS {$tablepre}memo", 1);
	$db->query("CREATE TABLE {$tablepre}memo (
		id int(10) unsigned NOT NULL auto_increment,
		username varchar(25) NOT NULL default '',
		type enum('address','notebook','collections') NOT NULL default 'address',
		dateline int(10) unsigned NOT NULL default '0',
		var1 varchar(50) NOT NULL default '',
		var2 varchar(100) NOT NULL default '',
		var3 tinytext NOT NULL,
		PRIMARY KEY (id),
		KEY username (username),
		KEY type (type)
	)charset=utf8");

result();

process("news");

	$db->query("DROP TABLE IF EXISTS {$tablepre}news;", 1);
	$db->query("CREATE TABLE {$tablepre}news (
		id smallint(6) unsigned NOT NULL auto_increment,
		subject varchar(100) NOT NULL default '',
		link varchar(100) NOT NULL default '',
		PRIMARY KEY (id)
	)charset=utf8");

result();

process("posts");

	$db->query("DROP TABLE IF EXISTS {$tablepre}posts;", 1);
	$db->query("CREATE TABLE {$tablepre}posts (
		fid smallint(6) unsigned NOT NULL default '0',
		tid mediumint(8) unsigned NOT NULL default '0',
		pid int(10) unsigned NOT NULL auto_increment,
		aid mediumint(8) unsigned NOT NULL default '0',
		icon varchar(30) NOT NULL default '',
		author varchar(25) NOT NULL default '',
		subject varchar(100) NOT NULL default '',
		dateline int(10) unsigned NOT NULL default '0',
		message text NOT NULL,
		useip varchar(20) NOT NULL default '',
		usesig tinyint(1) NOT NULL default '0',
		bbcodeoff tinyint(1) NOT NULL default '0',
		smileyoff tinyint(1) NOT NULL default '0',
		parseurloff tinyint(1) NOT NULL default '0',
		PRIMARY KEY (pid),
		KEY fid (fid),
		KEY tid (tid, dateline)
	)charset=utf8");

result();

process("searchindex");

	$db->query("DROP TABLE IF EXISTS {$tablepre}searchindex;", 1);
	$db->query("CREATE TABLE {$tablepre}searchindex (
		keywords varchar(200) NOT NULL default '',
		num smallint(6) NOT NULL default '0',
		dateline int(10) unsigned NOT NULL default '0',
		KEY dateline (dateline)
	)charset=utf8");

result();

process("sessions");

	$db->query("DROP TABLE IF EXISTS {$tablepre}sessions;", 1);
	$db->query("CREATE TABLE {$tablepre}sessions (
		sid varchar(32) binary NOT NULL default '',
		username varchar(25) NOT NULL default '',
		status varchar(20) NOT NULL default '',
		time int(10) unsigned NOT NULL default '0',
		ip varchar(20) NOT NULL default '',
		fid smallint(6) unsigned NOT NULL default '0',
		action varchar(60) NOT NULL default '',
		sessionvars text NOT NULL,
		KEY sid (sid),
		KEY fid (fid)
	)charset=utf8");

result();

process("settings");

	$db->query("DROP TABLE IF EXISTS {$tablepre}settings;", 1);
	$db->query("CREATE TABLE {$tablepre}settings (
		bbname varchar(50) NOT NULL default '',
		regstatus tinyint(1) NOT NULL default '0',
		censoruser text NOT NULL,
		doublee tinyint(1) NOT NULL default '0',
		emailcheck tinyint(1) NOT NULL default '0',
		bbrules tinyint(1) NOT NULL default '0',
		bbrulestxt text NOT NULL,
		welcommsg tinyint(1) NOT NULL default '0',
		welcommsgtxt text NOT NULL,
		bbclosed tinyint(1) NOT NULL default '0',
		closedreason text NOT NULL,
		sitename varchar(50) NOT NULL default '',
		siteurl varchar(60) NOT NULL default '',
		theme varchar(30) NOT NULL default '',
		credittitle varchar(20) NOT NULL default '',
		creditunit varchar(10) NOT NULL default '',
		moddisplay enum('flat','selectbox') NOT NULL default 'flat',
		floodctrl smallint(6) unsigned NOT NULL default '0',
		karmactrl smallint(6) unsigned NOT NULL default '0',
		hottopic tinyint(3) unsigned NOT NULL default '0',
		topicperpage tinyint(3) unsigned NOT NULL default '0',
		postperpage tinyint(3) unsigned NOT NULL default '0',
		memberperpage tinyint(3) unsigned NOT NULL default '0',
		maxpostsize smallint(6) unsigned NOT NULL default '0',
		maxavatarsize tinyint(3) unsigned NOT NULL default '0',
		smcols tinyint(3) unsigned NOT NULL default '0',
		postcredits tinyint(3) NOT NULL default '0',
		digistcredits tinyint(3) NOT NULL default '0',
		whosonlinestatus tinyint(1) NOT NULL default '0',
		vtonlinestatus tinyint(1) NOT NULL default '0',
		chcode tinyint(1) NOT NULL default '0',
		gzipcompress tinyint(1) NOT NULL default '0',
		hideprivate tinyint(1) NOT NULL default '0',
		fastpost tinyint(1) NOT NULL default '0',
		memliststatus tinyint(1) NOT NULL default '0',
		statstatus tinyint(1) NOT NULL default '0',
		debug tinyint(1) NOT NULL default '0',
		reportpost tinyint(1) NOT NULL default '0',
		bbinsert tinyint(1) NOT NULL default '0',
		smileyinsert tinyint(1) NOT NULL default '0',
		editedby tinyint(1) NOT NULL default '0',
		dotfolders tinyint(1) NOT NULL default '0',
		attachimgpost tinyint(1) NOT NULL default '0',
		timeformat varchar(5) NOT NULL default '',
		dateformat varchar(10) NOT NULL default '',
		timeoffset char(3) NOT NULL default '',
		version varchar(30) NOT NULL default '',
		onlinerecord varchar(30) NOT NULL default '',
		lastmember varchar(25) NOT NULL default ''
	)charset=utf8");

result();

process("smilies");

	$db->query("DROP TABLE IF EXISTS {$tablepre}smilies;", 1);
	$db->query("CREATE TABLE {$tablepre}smilies (
		id smallint(6) unsigned NOT NULL auto_increment,
		type enum('smiley','picon') NOT NULL default 'smiley',
		code varchar(10) NOT NULL default '',
		url varchar(30) NOT NULL default '',
		PRIMARY KEY (id)
	)charset=utf8");

result();

process("stats");

	$db->query("DROP TABLE IF EXISTS {$tablepre}stats;", 1);
	$db->query("CREATE TABLE {$tablepre}stats (
		type varchar(20) NOT NULL default '',
		var varchar(20) NOT NULL default '',
		count int(10) unsigned NOT NULL default '0',
		KEY type (type),
		KEY var (var)
	)charset=utf8");

result();

process("subscriptions");

	$db->query("DROP TABLE IF EXISTS {$tablepre}subscriptions;", 1);
	$db->query("CREATE TABLE {$tablepre}subscriptions (
		username varchar(25) NOT NULL default '',
		email varchar(60) NOT NULL default '',
		tid mediumint(8) unsigned NOT NULL default '0',
		lastnotify int(10) unsigned NOT NULL default '0',
		KEY username (username),
		KEY tid (tid)
	)charset=utf8");

result();

process("templates");

	$db->query("DROP TABLE IF EXISTS {$tablepre}templates;", 1);
	$db->query("CREATE TABLE {$tablepre}templates (
		id smallint(6) NOT NULL auto_increment,
		name varchar(40) NOT NULL default '',
		modified tinyint(1) NOT NULL default '0',
		template text NOT NULL,
		PRIMARY KEY (id)
	)charset=utf8");

result();

process("themes");

	$db->query("DROP TABLE IF EXISTS {$tablepre}themes;", 1);
	$db->query("CREATE TABLE {$tablepre}themes (
		themeid smallint(6) unsigned NOT NULL auto_increment,
		themename varchar(30) NOT NULL default '',
		bgcolor varchar(25) NOT NULL default '',
		altbg1 varchar(15) NOT NULL default '',
		altbg2 varchar(15) NOT NULL default '',
		link varchar(15) NOT NULL default '',
		bordercolor varchar(15) NOT NULL default '',
		headercolor varchar(15) NOT NULL default '',
		headertext varchar(15) NOT NULL default '',
		catcolor varchar(15) NOT NULL default '',
		tabletext varchar(15) NOT NULL default '',
		text varchar(15) NOT NULL default '',
		borderwidth varchar(15) NOT NULL default '',
		tablewidth varchar(15) NOT NULL default '',
		tablespace varchar(15) NOT NULL default '',
		font varchar(40) NOT NULL default '',
		fontsize varchar(40) NOT NULL default '',
		nobold tinyint(1) NOT NULL default '0',
		boardimg varchar(50) NOT NULL default '',
		imgdir varchar(120) NOT NULL default '',
		smdir varchar(120) NOT NULL default '',
		cattext varchar(15) NOT NULL default '',
		PRIMARY KEY  (themeid),
		KEY themename (themename)
	)charset=utf8");

result();

process("threads");

	$db->query("DROP TABLE IF EXISTS {$tablepre}threads;", 1);
	$db->query("CREATE TABLE {$tablepre}threads (
		tid mediumint(8) unsigned NOT NULL auto_increment,
		fid smallint(6) NOT NULL default '0',
		creditsrequire smallint(6) unsigned NOT NULL default '0',
		icon varchar(30) NOT NULL default '',
		author varchar(25) NOT NULL default '',
		subject varchar(100) NOT NULL default '',
		dateline int(10) unsigned NOT NULL default '0',
		lastpost int(10) unsigned NOT NULL default '0',
		lastposter varchar(25) NOT NULL default '',
		views smallint(6) unsigned NOT NULL default '0',
		replies smallint(6) unsigned NOT NULL default '0',
		topped tinyint(1) NOT NULL default '0',
		digist tinyint(1) NOT NULL default '0',
		closed varchar(15) NOT NULL default '',
		pollopts text NOT NULL,
		attachment varchar(50) NOT NULL default '',
		PRIMARY KEY  (tid),
		KEY lastpost (topped,lastpost,fid)
	)charset=utf8");

result();

process("u2u");

	$db->query("DROP TABLE IF EXISTS {$tablepre}u2u;", 1);
	$db->query("CREATE TABLE {$tablepre}u2u (
		u2uid int(10) unsigned NOT NULL auto_increment,
		msgto varchar(25) NOT NULL default '',
		msgfrom varchar(25) NOT NULL default '',
		folder varchar(10) NOT NULL default '',
		new tinyint(1) NOT NULL default '0',
		subject varchar(75) NOT NULL default '',
		dateline int(10) unsigned NOT NULL default '0',
		message text NOT NULL,
		PRIMARY KEY (u2uid),
		KEY msgto (msgto)
	)charset=utf8");

result();

process("usergroups");

	$db->query("DROP TABLE IF EXISTS {$tablepre}usergroups;", 1);
	$db->query("CREATE TABLE {$tablepre}usergroups (
		groupid smallint(6) unsigned NOT NULL auto_increment,
		specifiedusers text NOT NULL,
		status varchar(20) NOT NULL default '',
		grouptitle varchar(30) NOT NULL default '',
		creditshigher int(10) NOT NULL default '0',
		creditslower int(10) NOT NULL default '0',
		stars tinyint(3) NOT NULL default '0',
		groupavatar varchar(60) NOT NULL default '',
		allowcstatus tinyint(1) NOT NULL default '0',
		allowavatar tinyint(1) NOT NULL default '0',
		allowvisit tinyint(1) NOT NULL default '0',
		allowview tinyint(1) NOT NULL default '0',
		allowpost tinyint(1) NOT NULL default '0',
		allowpostpoll tinyint(1) NOT NULL default '0',
		allowgetattach tinyint(1) NOT NULL default '0',
		allowpostattach tinyint(1) NOT NULL default '0',
		allowvote tinyint(1) NOT NULL default '0',
		allowsearch tinyint(1) NOT NULL default '0',
		allowkarma tinyint(1) NOT NULL default '0',
		allowsetviewperm tinyint(1) NOT NULL default '0',
		allowsetattachperm tinyint(1) NOT NULL default '0',
		allowsigbbcode tinyint(1) NOT NULL default '0',
		allowsigimgcode tinyint(1) NOT NULL default '0',
		allowviewstats tinyint(1) NOT NULL default '0',
		ismoderator tinyint(1) NOT NULL default '0',
		issupermod tinyint(1) NOT NULL default '0',
		isadmin tinyint(1) NOT NULL default '0',
		maxu2unum smallint(6) unsigned NOT NULL default '0',
		maxmemonum smallint(6) unsigned NOT NULL default '0',
		maxsigsize smallint(6) unsigned NOT NULL default '0',
		maxkarmavote tinyint(3) unsigned NOT NULL default '0',
		maxattachsize mediumint(8) unsigned NOT NULL default '0',
		attachextensions tinytext NOT NULL,
		PRIMARY KEY (groupid),
		KEY status (status),
		KEY creditshigher (creditshigher),
		KEY creditslower (creditslower)
	) charset=utf8");

result();

process("words");

	$db->query("DROP TABLE IF EXISTS {$tablepre}words;", 1);
	$db->query("CREATE TABLE {$tablepre}words (
		id smallint(6) unsigned NOT NULL auto_increment,
		find varchar(60) NOT NULL default '',
		replacement varchar(60) NOT NULL default '',
		PRIMARY KEY (id)
	) charset=utf8");

result();

echo"          </td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td>\n";
echo"            <hr noshade align=\"center\" width=\"100%\" size=\"1\">\n";
echo"          </td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td><b><font color=\"#FF0000\">&gt;</font><font color=\"#000000\"> 插入数据</font></b></td>\n";
echo"        </tr>\n";
echo"        <tr>\n";
echo"          <td>\n";

insert("caches");

	$db->query("INSERT INTO {$tablepre}caches VALUES ('settings', 'a:43:{s:6:\"bbname\";s:13:\"Discuz! Board\";s:9:\"regstatus\";s:1:\"1\";s:8:\"bbclosed\";s:1:\"0\";s:12:\"closedreason\";s:0:\"\";s:8:\"sitename\";s:15:\"Crossday Studio\";s:7:\"siteurl\";s:24:\"http://www.crossday.com/\";s:5:\"theme\";s:8:\"标准界面\";s:11:\"credittitle\";s:4:\"积分\";s:10:\"creditunit\";s:2:\"点\";s:10:\"moddisplay\";s:4:\"flat\";s:9:\"floodctrl\";s:2:\"15\";s:9:\"karmactrl\";s:3:\"300\";s:8:\"hottopic\";s:2:\"10\";s:12:\"topicperpage\";s:2:\"20\";s:11:\"postperpage\";s:2:\"10\";s:13:\"memberperpage\";s:2:\"25\";s:11:\"maxpostsize\";s:5:\"10000\";s:13:\"maxavatarsize\";s:1:\"0\";s:6:\"smcols\";s:1:\"3\";s:16:\"whosonlinestatus\";s:1:\"1\";s:14:\"vtonlinestatus\";s:1:\"1\";s:6:\"chcode\";s:1:\"0\";s:12:\"gzipcompress\";s:1:\"1\";s:11:\"postcredits\";s:1:\"1\";s:13:\"digistcredits\";s:2:\"10\";s:11:\"hideprivate\";s:1:\"1\";s:10:\"emailcheck\";s:1:\"0\";s:8:\"fastpost\";s:1:\"1\";s:13:\"memliststatus\";s:1:\"1\";s:10:\"statstatus\";s:1:\"0\";s:5:\"debug\";s:1:\"1\";s:10:\"reportpost\";s:1:\"1\";s:8:\"bbinsert\";s:1:\"1\";s:12:\"smileyinsert\";s:1:\"1\";s:8:\"editedby\";s:1:\"1\";s:10:\"dotfolders\";s:1:\"0\";s:13:\"attachimgpost\";s:1:\"1\";s:10:\"timeformat\";s:5:\"h:i A\";s:10:\"dateformat\";s:5:\"Y-n-j\";s:10:\"timeoffset\";s:1:\"8\";s:7:\"version\";s:3:\"1.0\";s:12:\"onlinerecord\";s:0:\"\";s:10:\"lastmember\";s:".strlen($username).":\"$username\";}');");
	$db->query("INSERT INTO {$tablepre}caches VALUES ('usergroups', 'a:17:{i:0;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:10:\"论坛管理员\";s:10:\"grouptitle\";s:10:\"论坛管理员\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"9\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}i:1;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"超级版主\";s:10:\"grouptitle\";s:8:\"超级版主\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"8\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}i:2;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:4:\"版主\";s:10:\"grouptitle\";s:4:\"版主\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"7\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}i:3;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"社区乞丐\";s:13:\"creditshigher\";s:8:\"-9999999\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:4;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"等待验证\";s:10:\"grouptitle\";s:12:\"等待验证会员\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:5;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:4:\"游客\";s:10:\"grouptitle\";s:4:\"游客\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"0\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:6;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"禁止访问\";s:10:\"grouptitle\";s:14:\"用户被禁止访问\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"0\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:7;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:6:\"禁止IP\";s:10:\"grouptitle\";s:12:\"用户IP被禁止\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"0\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:8;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"禁止发言\";s:10:\"grouptitle\";s:14:\"用户被禁止发言\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:1:\"0\";s:5:\"stars\";s:1:\"0\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"0\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:9;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"新手上路\";s:13:\"creditshigher\";s:1:\"0\";s:12:\"creditslower\";s:2:\"10\";s:5:\"stars\";s:1:\"1\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"0\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:10;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"初级会员\";s:13:\"creditshigher\";s:2:\"10\";s:12:\"creditslower\";s:2:\"50\";s:5:\"stars\";s:1:\"2\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:11;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"高级会员\";s:13:\"creditshigher\";s:2:\"50\";s:12:\"creditslower\";s:3:\"150\";s:5:\"stars\";s:1:\"3\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:12;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"支柱会员\";s:13:\"creditshigher\";s:3:\"150\";s:12:\"creditslower\";s:3:\"300\";s:5:\"stars\";s:1:\"4\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:13;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"青铜长老\";s:13:\"creditshigher\";s:3:\"300\";s:12:\"creditslower\";s:3:\"600\";s:5:\"stars\";s:1:\"5\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:14;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"黄金长老\";s:13:\"creditshigher\";s:3:\"600\";s:12:\"creditslower\";s:4:\"1000\";s:5:\"stars\";s:1:\"6\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"0\";}i:15;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"白金长老\";s:13:\"creditshigher\";s:4:\"1000\";s:12:\"creditslower\";s:4:\"3000\";s:5:\"stars\";s:1:\"7\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}i:16;a:10:{s:14:\"specifiedusers\";s:0:\"\";s:6:\"status\";s:8:\"正式会员\";s:10:\"grouptitle\";s:8:\"本站元老\";s:13:\"creditshigher\";s:4:\"3000\";s:12:\"creditslower\";s:7:\"9999999\";s:5:\"stars\";s:1:\"8\";s:11:\"groupavatar\";s:0:\"\";s:11:\"allowavatar\";s:1:\"1\";s:14:\"allowsigbbcode\";s:1:\"1\";s:15:\"allowsigimgcode\";s:1:\"1\";}}');");
	$db->query("INSERT INTO {$tablepre}caches VALUES ('announcements', 'a:0:{}');");
	$db->query("INSERT INTO {$tablepre}caches VALUES ('forums', 'a:1:{i:1;a:4:{s:4:\"type\";s:5:\"forum\";s:4:\"name\";s:8:\"默认板块\";s:3:\"fup\";s:1:\"0\";s:8:\"viewperm\";s:0:\"\";}}');");
	$db->query("INSERT INTO {$tablepre}caches VALUES ('forumlinks', 'a:1:{i:0;a:6:{s:2:\"id\";s:1:\"2\";s:12:\"displayorder\";s:1:\"0\";s:4:\"name\";s:13:\"Discuz! Board\";s:3:\"url\";s:21:\"http://www.Discuz.net\";s:4:\"note\";s:91:\"本站论坛程序 Discuz! 的官方站点，专门讨论 Discuz! 的使用与 Hack，提供论坛升级与技术支持等。\";s:4:\"logo\";s:15:\"images/logo.gif\";}}');");
	$db->query("INSERT INTO {$tablepre}caches VALUES ('smilies', 'a:9:{i:0;a:4:{s:2:\"id\";s:2:\"19\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":)\";s:3:\"url\";s:9:\"smile.gif\";}i:1;a:4:{s:2:\"id\";s:2:\"20\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":(\";s:3:\"url\";s:7:\"sad.gif\";}i:2;a:4:{s:2:\"id\";s:2:\"21\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":D\";s:3:\"url\";s:11:\"biggrin.gif\";}i:3;a:4:{s:2:\"id\";s:2:\"22\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\";)\";s:3:\"url\";s:8:\"wink.gif\";}i:4;a:4:{s:2:\"id\";s:2:\"23\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:6:\":cool:\";s:3:\"url\";s:8:\"cool.gif\";}i:5;a:4:{s:2:\"id\";s:2:\"24\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:5:\":mad:\";s:3:\"url\";s:7:\"mad.gif\";}i:6;a:4:{s:2:\"id\";s:2:\"25\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":o\";s:3:\"url\";s:11:\"shocked.gif\";}i:7;a:4:{s:2:\"id\";s:2:\"26\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:2:\":P\";s:3:\"url\";s:10:\"tongue.gif\";}i:8;a:4:{s:2:\"id\";s:2:\"27\";s:4:\"type\";s:6:\"smiley\";s:4:\"code\";s:5:\":lol:\";s:3:\"url\";s:7:\"lol.gif\";}}');");
	$db->query("INSERT INTO {$tablepre}caches VALUES ('picons', 'a:9:{i:0;a:4:{s:2:\"id\";s:2:\"28\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon1.gif\";}i:1;a:4:{s:2:\"id\";s:2:\"29\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon2.gif\";}i:2;a:4:{s:2:\"id\";s:2:\"30\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon3.gif\";}i:3;a:4:{s:2:\"id\";s:2:\"31\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon4.gif\";}i:4;a:4:{s:2:\"id\";s:2:\"32\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon5.gif\";}i:5;a:4:{s:2:\"id\";s:2:\"33\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon6.gif\";}i:6;a:4:{s:2:\"id\";s:2:\"34\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon7.gif\";}i:7;a:4:{s:2:\"id\";s:2:\"35\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon8.gif\";}i:8;a:4:{s:2:\"id\";s:2:\"36\";s:4:\"type\";s:5:\"picon\";s:4:\"code\";s:0:\"\";s:3:\"url\";s:9:\"icon9.gif\";}}');");
	$db->query("INSERT INTO {$tablepre}caches VALUES ('censor', 'a:2:{s:7:\"replace\";a:0:{}s:4:\"find\";a:0:{}}');");
	$db->query("INSERT INTO {$tablepre}caches VALUES ('news', 'a:4:{i:0;a:3:{s:2:\"id\";s:1:\"1\";s:7:\"subject\";s:59:\"有疑问请光临 Discuz! 技术支持论坛，我们会尽快解决您的问题。\";s:4:\"link\";s:22:\"http://www.Discuz!.net\";}i:1;a:3:{s:2:\"id\";s:1:\"2\";s:7:\"subject\";s:44:\"我们会不断完善程序，也希望得到您的继续支持。\";s:4:\"link\";s:25:\"http://forum.crossday.com\";}i:2;a:3:{s:2:\"id\";s:1:\"3\";s:7:\"subject\";s:44:\"Crossday Studio 向您问好，欢迎您选择 Discuz!\";s:4:\"link\";s:0:\"\";}i:3;a:3:{s:2:\"id\";s:1:\"4\";s:7:\"subject\";s:32:\"欢迎我们的新会员 {\$lastmember}！\";s:4:\"link\";s:46:\"member.php?action=viewpro&username=\$encodemember\";}}');");



result();

insert("forumlinks");

	$db->query("INSERT INTO {$tablepre}forumlinks VALUES (1, 0, 'Discuz! Board', 'http://www.Discuz.net', '本站论坛程序 Discuz! 的官方站点，专门讨论 Discuz! 的使用与 Hack，提供论坛升级与技术支持等。', 'images/logo.gif')");

result();

insert("forums");

	$db->query("INSERT INTO {$tablepre}forums VALUES (1, 0, 'forum', '', '默认板块', '', 1, 0, '', 0, 0, '', 1, 0, 1, 1, '', 0, '', '', '', '');");

result();

insert("members");

	$db->query("INSERT INTO {$tablepre}members (username, password, status, regip, regdate, charset, email, tpp, ppp, dateformat, timeformat, showemail, newsletter, timeoffset)
		VALUES ('$username', '".encrypt($password1)."', '论坛管理员', 'hidden', '".time()."', '$charset', '$email', '20', '10', 'Y-n-j', 'h:i A', '1', '1', '8');");

result();

insert("news");

	$db->query("INSERT INTO {$tablepre}news (subject, link) VALUES ('有疑问请光临 Discuz! 技术支持论坛，我们会尽快解决您的问题。', 'http://www.Discuz.net');");
	$db->query("INSERT INTO {$tablepre}news (subject, link) VALUES ('我们会不断完善程序，也希望得到您的继续支持。', 'http://www.Discuz.net');");
	$db->query("INSERT INTO {$tablepre}news (subject, link) VALUES ('Crossday Studio 向您问好，欢迎您选择 Discuz!', '');");
	$db->query("INSERT INTO {$tablepre}news (subject, link) VALUES ('欢迎我们的新会员 {\$lastmember}！', 'member.php?action=viewpro&username=\$encodemember');");

result();

insert("templates");

	$tplfile = "./templates.cdb";
	if(is_readable($tplfile)) {
		$db->query("DELETE FROM {$tablepre}templates");
		$fp = fopen($tplfile, "r");
		$templates = explode("|#*CDB TEMPLATE FILE*#|", fread($fp, filesize($tplfile)));
		fclose($fp);

		ksort($templates);
		foreach($templates as $template) {
			$template = explode("|#*CDB TEMPLATE*#|", $template);
			if($template[0] && $template[1]) {
				$db->query("INSERT INTO {$tablepre}templates (name, template)
					VALUES ('".addslashes($template[0])."', '".addslashes(addslashes(trim($template[1])))."')");
			}
		}
	} else {
		echo " ... <font color=\"#FF0000\">失败. 原因: 模板文件不存在.</font>";
		exit;
	}

result();

insert("settings");

	$db->query("INSERT INTO {$tablepre}settings VALUES ('Discuz! Board', 1, '', 1, 0, 0, '', 0, '', 0, '', 'Crossday Studio', 'http://www.crossday.com/', '标准界面', '积分', '点', 'flat', 15, 300, 10, 20, 10, 25, 10000, 0, 3, 1, 10, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 0, 1, 'h:i A', 'Y-n-j', '8', '1.0', '', '$username');");

result();

insert("smilies");

	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ':)', 'smile.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ':(', 'sad.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ':D', 'biggrin.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ';)', 'wink.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ':cool:', 'cool.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ':mad:', 'mad.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ':o', 'shocked.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ':P', 'tongue.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'smiley', ':lol:', 'lol.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon1.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon2.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon3.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon4.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon5.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon6.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon7.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon8.gif');");
	$db->query("INSERT INTO {$tablepre}smilies VALUES ('', 'picon', '', 'icon9.gif');");

result();

insert("stats");

	$db->query("INSERT INTO {$tablepre}stats VALUES ('total', 'hits', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('total', 'members', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('total', 'guests', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'Windows', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'Mac', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'Linux', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'FreeBSD', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'SunOS', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'BeOS', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'OS/2', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'AIX', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('os', 'Other', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('browser', 'MSIE', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('browser', 'Netscape', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('browser', 'Mozilla', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('browser', 'Lynx', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('browser', 'Opera', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('browser', 'Konqueror', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('browser', 'Other', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('week', '0', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('week', '1', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('week', '2', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('week', '3', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('week', '4', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('week', '5', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('week', '6', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '00', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '01', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '02', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '03', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '04', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '05', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '06', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '07', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '08', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '09', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '10', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '11', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '12', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '13', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '14', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '15', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '16', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '17', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '18', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '19', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '20', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '21', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '22', 0);");
	$db->query("INSERT INTO {$tablepre}stats VALUES ('hour', '23', 0);");

result();

insert("themes");

	$db->query("INSERT INTO {$tablepre}themes VALUES ('', '标准界面', '#FFFFFF', '#E3E3EA', '#EEEEF6', '#3A4273', '#000000', 'header_bg.gif', '#F1F3FB', 'cat_bg.gif', '#464F86', '#464F86', '1', '99%', '3', 'Tahoma, Verdana', '12px', 0, 'logo.gif', 'images/standard', 'images/smilies', '#D9D9E9');");

result();

insert("stats");

	$db->query("INSERT INTO {$tablepre}usergroups VALUES (1, '', '论坛管理员', '论坛管理员', 0, 0, 9, '', 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 100, 100, 500, 16, 2048000, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (2, '', '超级版主', '超级版主', 0, 0, 8, '', 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 90, 60, 300, 12, 2048000, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (3, '', '版主', '版主', 0, 0, 7, '', 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 80, 40, 200, 10, 2048000, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (4, '', '正式会员', '社区乞丐', -9999999, 0, 0, '', 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (5, '', '正式会员', '新手上路', 0, 10, 1, '', 0, 0, 1, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 1, 0, 0, 0, 30, 3, 50, 0, 0, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (6, '', '正式会员', '初级会员', 10, 50, 2, '', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 0, 1, 0, 0, 0, 40, 5, 50, 0, 128000, 'gif,jpg,png');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (7, '', '正式会员', '高级会员', 50, 150, 3, '', 0, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 0, 1, 0, 0, 0, 50, 10, 100, 2, 256000, 'gif,jpg,png');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (8, '', '正式会员', '支柱会员', 150, 300, 4, '', 1, 21, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 0, 1, 0, 0, 0, 50, 15, 100, 3, 512000, 'zip,rar,chm,txt,gif,jpg,png');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (9, '', '正式会员', '青铜长老', 300, 600, 5, '', 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 1, 0, 0, 0, 50, 20, 100, 4, 1024000, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (10, '', '正式会员', '黄金长老', 600, 1000, 6, '', 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 1, 0, 0, 0, 50, 25, 100, 5, 1024000, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (11, '', '正式会员', '白金长老', 1000, 3000, 7, '', 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 50, 30, 100, 6, 2048000, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (12, '', '正式会员', '本站元老', 3000, 9999999, 8, '', 1, 2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 50, 40, 100, 8, 2048000, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (13, '', '等待验证', '等待验证会员', 0, 0, 0, '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 10, 0, 50, 0, 0, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (14, '', '游客', '游客', 0, 0, 0, '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (15, '', '禁止访问', '用户被禁止访问', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (16, '', '禁止IP', '用户IP被禁止', 0, 0, 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '');");
	$db->query("INSERT INTO {$tablepre}usergroups VALUES (17, '', '禁止发言', '用户被禁止发言', 0, 0, 0, '', 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, '');");

result();

?>
          </td>
        </tr>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr>
          <td align="center">
            <font color="#FF0000"><b>恭喜您，Crossday Bulletin 安装成功！</font><br>
            管理员账号：</b><?=$username?><b> 密码：</b><?=$password1?><br><br>
            <a href="index.php" target="_blank">点击这里进入论坛</a>
          </td>
        </tr>
<?

}

?>
        <tr>
          <td>
            <hr noshade align="center" width="100%" size="1">
          </td>
        </tr>
        <tr> 
          <td align="center">
            <b>Powered by <a href="http://www.Discuz.net" target="_blank">Discuz! <?=$version?></a> , &nbsp; Copyright &copy; <a href="http://www.crossday.com" target=\"_blank\">Crossday Studio</a>, 2002</b>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
</body>
</html>

