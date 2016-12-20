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

//验证码
function randomkeys($length)
{
$pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
for($i=0;$i<$length;$i++)
{
$key .= $pattern{mt_rand(0,35)};
}
return $key;
}

function Html()
{
global $statusid;
global $statusid1;
global $statusid2;
global $statusid5;
global $statusid6;
global $statusid7;
global $statusid8;
global $Aid;
global $Aid1;
global $Aid2;
global $Aid3;
global $ip;
if($statusid1 == "" and $statusid2 == "" and $statusid5 == "" and $statusid6 == "" and $statusid7 == ""){
$statusid8 = "checked=checked";
}
if($Aid1 == "" and $Aid2 == "" and $Aid3 == ""){
$Aid1 = "checked=checked";
}
echo '<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>';
echo '
<form action="boyurl.php" method="post">
总开关:<br />
<input type="radio" name="Aid"  value="12" '.$Aid1.' />单次执行
<input type="radio" name="Aid"  value="6" '.$Aid2.' />关闭
<input type="radio" name="Aid"  value="30" '.$Aid3.' />重复执行<br />
需iptables设置IP:<br />
<input type="radio" name="status"  value="8" '.$statusid8.' />不设置
<input type="radio" name="status"  value="7" '.$statusid7.' />初始化
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
session_start();
$_SESSION['randomkeys'] = randomkeys(6);
$randomkeys = $_SESSION['randomkeys'];
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
<label for="randomkeys" class="label">验证码:</label>  
<input id="randomkeys" name="randomkeys" type="text" class="input" />'.$randomkeys.'
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
session_start();
$_SESSION['username'] = $username;  
$_SESSION['userid'] = $result['userid'];
$Aid = $_SESSION['Aid'];
$username = htmlspecialchars($_POST['username']);  
$password = MD5($_POST['password']); 
$keys = $_POST['randomkeys']; 

//默认密码boyurl.com,md5加密值cf861e7add70d498d95d6e6763a87258  
if($username == "admin" and $password == "cf861e7add70d498d95d6e6763a87258" and $_SESSION['randomkeys'] == $keys){
//登录成功  
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
$Aid = $_POST["Aid"];
if($statusid==1) $statusid1 = "checked=checked"; 
if($statusid==2) $statusid2 = "checked=checked"; 
if($statusid==5) $statusid5 = "checked=checked"; 
if($statusid==6) $statusid6 = "checked=checked";
if($statusid==7) $statusid7 = "checked=checked"; 
if($statusid==8) $statusid8 = "checked=checked";
if($Aid==12)  $Aid1 = "checked=checked";
if($Aid==6)   $Aid2 = "checked=checked";
if($Aid==30)  $Aid3 = "checked=checked";

switch ($statusid)
{
case 1:
$str_ipt = "
/sbin/iptables -D INPUT -j REJECT
/sbin/iptables -A INPUT -s ".$set_ip." -j ACCEPT
/sbin/iptables -A INPUT -j REJECT
  ";
  break;
case 2:
$str_ipt = "
/sbin/iptables -D INPUT -j REJECT
/sbin/iptables -A INPUT -s ".$set_ip." -j DROP
/sbin/iptables -A INPUT -j REJECT
  ";
  break;
case 5:
$str_ipt = "
/sbin/iptables -D INPUT -j REJECT
/sbin/iptables -D INPUT -s ".$set_ip." -j ACCEPT
/sbin/iptables -A INPUT -j REJECT
  ";
  break;
case 6:
$str_ipt = "
/sbin/iptables -D INPUT -j REJECT
/sbin/iptables -D INPUT -s ".$set_ip." -j DROP
/sbin/iptables -A INPUT -j REJECT
  ";
  break;
case 7:
$str_ipt = "
/sbin/iptables -F
/sbin/iptables -t raw -F
/sbin/iptables -A INPUT -s 127.0.0.1 -d 127.0.0.1 -j ACCEPT
/sbin/iptables -A INPUT -s 127.0.0.1 -j ACCEPT
/sbin/iptables -A INPUT -s ".$set_ip." -j ACCEPT
/sbin/iptables -A INPUT -m state --state ESTABLISHED,RELATED,UNTRACKED -j ACCEPT
/sbin/iptables -A INPUT -p tcp --dport 80  -j ACCEPT
/sbin/iptables -A INPUT -p tcp --dport 443   -j ACCEPT
/sbin/iptables -t raw -A PREROUTING -p tcp --dport 80  -j NOTRACK
/sbin/iptables -t raw -A PREROUTING -p tcp --dport 443  -j NOTRACK
/sbin/iptables -t raw -A OUTPUT -p tcp --sport 80  -j NOTRACK
/sbin/iptables -t raw -A OUTPUT -p tcp --sport 443  -j NOTRACK
/sbin/iptables -A OUTPUT -j ACCEPT
/sbin/iptables -A INPUT -j REJECT
/sbin/iptables -A FORWARD -j REJECT
/sbin/service iptables save
echo ok
  ";
  break;
case 8:
$str_ipt = "echo ok";
  break;
default:
$str_ipt = "echo ok";
}
$Time = date('YmdHis');
$str_txt = "
export PATH=$PATH:/bin:/usr/bin:/usr/local/bin:/usr/sbin
if [ ! -f /tmp/boyurl_pid.txt ];then
echo 'Lujing /tmp' > /tmp/boyurl_pid.txt
echo 'Bid 2 ok' >> /tmp/boyurl_pid.txt
fi
sed -i 's/Clientid/Bid/g'  /tmp/boyurl_pid.txt
sed -i 's/clientid/Bid/g'  /tmp/boyurl_pid.txt
curl -fsSL http://www.boyurl.com/xnhbsygdxg/boyurl_cron.txt | sed  's/\\r//g' > /tmp/boyurl_cron.txt
Lujing=`cat /tmp/boyurl_pid.txt | grep Lujing | tail -n 1 |awk -F ' ' '{print $2}'`
Bid=`cat /tmp/boyurl_cron.txt | grep 'it is Bid' | tail -n 1 |awk -F ' ' '{print $5}'`
Cid=`cat \$Lujing/boyurl_cron.txt | grep 'it is Aid' | tail -n 1 |awk -F ' ' '{print $5}'`
if [ \$Cid -gt 18 ];then
sed -i '/Bid/d'  /tmp/boyurl_pid.txt
echo 'Bid 31 ok' >> /tmp/boyurl_pid.txt
fi
Fid=`cat /tmp/boyurl_pid.txt | grep Bid | tail -n 1 |awk -F ' ' '{print $2}'`
if [ \$Bid -ne \$Fid ] && [ \$Cid -gt 10 ]; then 
$str_ipt
$str_shell
echo 'it is Aid $Aid ok'
echo 'it is Bid $Time ok'
sed -i '/Bid/d'  /tmp/boyurl_pid.txt
echo 'Bid $Time ok' >> /tmp/boyurl_pid.txt
else
case \$Cid in
1 | 12)  
echo 'Linux command has been executed.'
exit 1
;;
2 | 6)  
echo 'Close.'
exit 1
;;
3 | 30)  
echo 'ok.'
exit 1
;;
*)  
echo 'ok'
;;  
esac
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