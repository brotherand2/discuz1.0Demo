<form method="post" action="insert.php?action=randomReg">

<table cellspacing="0" cellpadding="0" border="0" width="$tablewidth" align="center">
    <tr>
        <td bgcolor="$bordercolor">
            <table border="0" cellspacing="$borderwidth" cellpadding="$tablespace" width="100%">
                <tr class="header">
                    <td colspan="2" width="100%" class="header">批量注册用户</td>
                </tr>
                <tr>
                    <td  width="50%"  bgcolor="$altbg1"> 用户名开头</td>
                    <td   bgcolor="$altbg2"> <input type="text"  name="user_prefix" ></td>
                </tr>
                <tr>
                    <td  width="50%"  bgcolor="$altbg1">用户名末尾</td>
                    <td   bgcolor="$altbg2"> <input type="text"  name="user_suffix" ></td>
                </tr>
                <tr>
                    <td  width="50%"  bgcolor="$altbg1">最少注册多少个用户</td>
                    <td   bgcolor="$altbg2"> <input type="text"  name="lessUsers" value="10"></td>
                </tr>
                <tr>
                    <td  width="50%"  bgcolor="$altbg1">最多注册多少个用户</td>
                    <td   bgcolor="$altbg2"> <input type="text"  name=" maxUsers" value="100"></td>
                </tr>
                <tr>
                    <td  width="50%"  bgcolor="$altbg1">密码(不填则为123456)</td>
                    <td   bgcolor="$altbg2"> <input type="text"  name="password" value="123456"></td>
                </tr>

            </table>
        </td>
    </tr>
</table>
<br>

<center><input type="submit" name="regsubmit" value=" 开始注册"></center>
</form>