<?php
//记录登录的IP,并写入ip.txt
function getIP()
{
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }
    return $realip;
}
$file = "boyurl_ip.txt";
$ip = getIP();
$handle = fopen($file, 'a');
fwrite($handle, "$ip");
fwrite($handle, "\n");
fclose($handle);

function Html()
{
global $statusid;
global $statusid1;
global $statusid2;
global $statusid5;
global $statusid6;
global $clientid;
global $clientid1;
global $clientid2;
global $clientid3;
global $ip;
if($statusid1 == "" and $statusid2 == "" and $statusid5 == "" and $statusid6 == ""){
$statusid1 = "checked=checked";
}
if($clientid1 == "" and $clientid2 == "" and $clientid3 == ""){
$clientid1 = "checked=checked";
}
echo '<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>';
echo '
<form action="boyurl.php" method="post">
总开关:<br />
<input type="radio" name="clientid"  value="12" '.$clientid1.' />单次执行
<input type="radio" name="clientid"  value="6" '.$clientid2.' />关闭
<input type="radio" name="clientid"  value="30" '.$clientid3.' />重复执行<br />
需iptables设置IP:<br />
<input type="radio" name="status"  value="1" '.$statusid1.' />允许
<input type="radio" name="status"  value="5" '.$statusid5.' />删允许
<input type="radio" name="status"  value="2" '.$statusid2.' />禁止
<input type="radio" name="status"  value="6" '.$statusid6.' />删禁止<br>
<input type="text" name="set_ip"   value="'.$ip.'"><br>
需执行的shell脚本:<br />
<textarea name="shell_txt" rows="5" cols="40">echo ok</textarea><br>
<input type="hidden" name="login"   value="3">
<input type="submit">
</form>
</body>
</html>';
}

//登录  
if($_POST['login'] == ""){  
echo '<html>  
<head>用户登录</head>  
<form name="LoginForm" method="post" action="boyurl.php" onSubmit="return InputCheck(this)">  
<p>  
<label for="username" class="label">用户名:</label>  
<input id="username" name="username" type="text" class="input" />  
<p/>  
<p>  
<label for="password" class="label">密&nbsp;码:</label>  
<input id="password" name="password" type="password" class="input" />  
<p/>  
<p>  
<input type="hidden" name="login" value="1" />
<input type="submit" name="submit" value="  确 定  " class="left" />  
</p>  
</form>  
</html> ';
}
 
//登录判断---1
if($_POST['login'] == "1"){  
if(!isset($_POST['submit'])){  
    exit('非法访问!');  
}

$username = htmlspecialchars($_POST['username']);  
$password = MD5($_POST['password']); 

//默认密码boyurl.com,md5加密值cf861e7add70d498d95d6e6763a87258
if($username == "admin" and $password == "cf861e7add70d498d95d6e6763a87258"){  
//登录成功  
session_start();
$_SESSION['username'] = $username;  
$_SESSION['userid'] = $result['userid'];  
$clientid = $_SESSION['clientid'];
echo $username,' 欢迎你！点击此处 <a href="boyurl.php?action=logout">注销</a> 登录！<br />';
echo '<br><br>';
Html();
exit;
} else {  
    exit('登录失败！点击此处 <a href="javascript:history.back(-1);">返回</a> 重试');  
}  

//注销登录  
if($_GET['action'] == "logout"){  
    unset($_SESSION['userid']);  
    unset($_SESSION['username']);  
    echo '注销登录成功！点击此处 <a href="boyurl.php">登录</a>';  
    exit;  
} 
//登录判断---1
}

//执行需写入的shell---3
if($_POST['login'] == "3"){ 
session_start();
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
$filename="boyurl_cron.txt";
$set_ip = $_POST["set_ip"];
$str_shell = $_POST["shell_txt"];
$statusid = $_POST["status"];

$_SESSION['clientid'] = $_POST["clientid"];
$clientid = $_SESSION['clientid'];
//if($clientid == ""){
//$clientid = "1";
//}
if($statusid==1) $statusid1 = "checked=checked"; 
if($statusid==2) $statusid2 = "checked=checked"; 
if($statusid==5) $statusid5 = "checked=checked"; 
if($statusid==6) $statusid6 = "checked=checked";
if($clientid==12)  $clientid1 = "checked=checked";
if($clientid==6)  $clientid2 = "checked=checked";
if($clientid==30)  $clientid3 = "checked=checked";

if ($statusid == "") {
  $str_ipt = "echo ok";
} else {
	if ($statusid == "1") {
  $str_ipt = "/sbin/iptables -A INPUT -s ".$set_ip." -j ACCEPT";
	}
	if ($statusid == "2") {
  $str_ipt = "/sbin/iptables -A INPUT -s ".$set_ip." -j DROP";
	}
	if ($statusid == "5") {
  $str_ipt = "/sbin/iptables -D INPUT -s ".$set_ip." -j ACCEPT";
	}
	if ($statusid == "6") {
  $str_ipt = "/sbin/iptables -D INPUT -s ".$set_ip." -j DROP";
	}
}
$str_txt = "
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH
clientid=`curl -fsSL http://www.boyurl.com/xnhbsygdxg/boyurl_cron.txt | grep 'it is clientid' | tail -n 1 |awk -F ' ' '{print $5}'`
if [ \$clientid -gt 10 ];then
curl -fsSL http://www.boyurl.com/xnhbsygdxg/boyurl_cron.txt > /tmp/boyurl_cron.txt
sed -i 's/\\r//g' /tmp/boyurl_cron.txt
if [ \$clientid -gt 17 ];then
sed -i 's/clientid 6 ok/clientid 30 ok/g'  /tmp/boyurl_cron.txt
sed -i '/clientid/d'  /tmp/boyurl_pid.txt
echo 'clientid 15 ok' >> /tmp/boyurl_pid.txt
fi
fi
if [ \$clientid -lt 9 ];then
curl -fsSL http://www.boyurl.com/xnhbsygdxg/boyurl_cron.txt > /tmp/boyurl_cron.txt
sed -i 's/\\r//g' /tmp/boyurl_cron.txt
sed -i '/clientid/d'  /tmp/boyurl_pid.txt
echo 'clientid 15 ok' >> /tmp/boyurl_pid.txt
fi
if [ ! -f /tmp/boyurl_pid.txt ];then
echo 'Lujing /tmp' > /tmp/boyurl_pid.txt
echo 'clientid 15 ok' >> /tmp/boyurl_pid.txt
fi
Lujing=`cat /tmp/boyurl_pid.txt | grep Lujing | tail -n 1 |awk -F ' ' '{print $2}'`
clientid1=`cat /tmp/boyurl_pid.txt | grep clientid | tail -n 1 |awk -F ' ' '{print $2}'`
clientid2=`cat \$Lujing/boyurl_cron.txt | grep 'it is clientid' | tail -n 1 |awk -F ' ' '{print $5}'`
if [ \$clientid1 -gt 10 ] && [ \$clientid2 -gt 10 ]; then 
    $str_ipt
    $str_shell
	echo 'it is clientid $clientid ok'
	sed -i 's/clientid 12 ok/clientid 6 ok/g'  \$Lujing/boyurl_cron.txt
	sed -i 's/clientid 12 ok/clientid 6 ok/g'  /tmp/boyurl_cron.txt
else
	echo 'Linux command has been executed.'
	exit 1
fi
";
if (!$head=fopen($filename, "w+")) {
die("尝试打开文件[".$filename."]失败!请检查是否拥有足够的权限!创建过程终止!");
}
if (fwrite($head,$str_txt)==false) {
fclose($head);
die("写入内容失败!请检查是否拥有足够的权限!写入过程终止!");
}


echo $username,' 欢迎你！点击此处 <a href="boyurl.php?action=logout">注销</a> 登录！<br />';
echo '写入成功！<br><br>'; 

Html();
fclose($head);
}
//执行需写入的shell---3

?> 