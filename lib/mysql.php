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


class dbstuff {
	var $querynum = 0;

	function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = 0) {
		if($pconnect) {
			if(!@mysql_pconnect($dbhost, $dbuser, $dbpw)) {
				$this->halt("MySQL 数据库无法连接，请检查服务器或程序设置");
			}
		} else {
			if(!@mysql_connect($dbhost, $dbuser, $dbpw)) {
				$this->halt("MySQL 数据库无法连接，请检查服务器或程序设置");
			}
		}
	}

	function select_db($dbname) {
		return mysql_select_db($dbname);
	}

	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		$query = mysql_fetch_array($query, $result_type);
		return $query;
	}

	function query($sql, $silence = 0) {
		//echo "|$sql|<br>"; //debug
		$query = mysql_query($sql);
		if(!$query && !$silence) {
			$this->halt("MySQL Query 错误", $sql);
		}
		$this->querynum++;
		return $query;
	}

	function affected_rows() {
		return mysql_affected_rows();
	}

	function error() {
		return mysql_error();
	}

	function errno() {
		return mysql_errno();
	}

	function result($query, $row) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	function num_rows($query) {
		$query = mysql_num_rows($query);
		return $query;
	}

	function num_fields($query) {
		return mysql_num_fields($query);
	}

	function free_result($query) {
		return mysql_free_result($query);
	}

	function insert_id() {
		$id = mysql_insert_id();
		return $id;
	}

	function fetch_row($query) {
		$query = mysql_fetch_row($query);
		return $query;
	}

	function close() {
		return mysql_close();
	}

	function halt($message = "", $sql = "") {
		$timestamp = time();
		$errmsg = "";
		$cdbuser = $GLOBALS[HTTP_SESSION_VARS]["_cdbuser"] ? $GLOBALS[HTTP_SESSION_VARS]["_cdbuser"] : $GLOBALS[HTTP_COOKIE_VARS]["_cdbuser"];
		if($message) {
			$errmsg = "Discuz! 提示: $message\n\n";
		}
		if($cdbuser) {
			$errmsg .= "用户:  $cdbuser\n";
		}
		$errmsg .= "时间:  ".gmdate("Y-n-j g:ia", $timestamp + ($GLOBALS["timeoffset"] * 3600))."\n";
		$errmsg .= "程序:  ".$GLOBALS["PHP_SELF"]."\n\n";
		if($sql) {
			$errmsg .= "语句:  ".htmlspecialchars($sql)."\n";
		}
		$dberror = $this->error();
		$dberrno = $this->errno();
		$errmsg .= "错误:  $dberror\n";
		$errmsg .= "错误号:  $dberrno";

		echo "</table></table></table></table></table>\n";
		echo "<p style=\"font-family: Tahoma, Verdana, 宋体; font-size: 12px; background: #FFFFFF;\">";
		echo nl2br($errmsg);
		if($GLOBALS[adminemail]) {
			$errnos = array();
			$errorlogs = "";
			if($errlog = @file("./datatemp/dberror.log")) {
				for($i = 0; $i < count($errlog); $i++) {
					$log = explode("\t", $errlog[$i]);
					if(($timestamp - $log[0]) > 86400) {
						$errlog[$i] = "";
					} else {
						$errnos[] = $log[1];
					}
				}
			}
			if(!in_array($dberrno, $errnos)) {
				$errorlogs .= "$timestamp\t$dberrno\n";
				echo "<br><br>错误报告已经发送至系统 Email 信箱";
				@mail($GLOBALS[adminemail], "[Discuz!] MySQL 错误报告",
						"您的 Discuz! 论坛产生数据库错误, 详情如下\n\n".
						"$errmsg\n\n".
						"请检查数据库服务器或论坛程序,24小时以内类似的错误将不再向您报告\n".
						"如您有疑问请访问 Discuz! 技术支持论坛 http://www.Discuz.net.");
			} else {
				echo "<br><br>同样的错误报告之前已经发送至系统 Email 信箱";
			}
			for($i = 0; $i < count($errlog); $i++) {
				$errorlogs .= $errlog[$i] ? trim($errlog[$i])."\n" : NULL;
			}
			@$fp = fopen("./datatemp/dberror.log", "w");
			@flock($fp, 3);
			@fwrite($fp, $errorlogs);
			@fclose($fp);
		}
		echo "</p>";
		exit;
	}
}
?>