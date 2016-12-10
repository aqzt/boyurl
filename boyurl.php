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
 
//登录判断
if($_POST['login'] == "1"){  
if(!isset($_POST['submit'])){  
    exit('非法访问!');  
}

$username = htmlspecialchars($_POST['username']);  
$password = MD5($_POST['password']); 

##默认密码boyurl.com,md5加密值cf861e7add70d498d95d6e6763a87258
if($password == "cf861e7add70d498d95d6e6763a87258"){  
//登录成功  
session_start();
$_SESSION['username'] = $username;  
$_SESSION['userid'] = $result['userid'];  
echo $username,' 欢迎你！点击此处 <a href="boyurl.php?action=logout">注销</a> 登录！<br />';   
echo '<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<form action="boyurl.php" method="post">
需执行的shell脚本:<br />
<input type="hidden" name="login"   value="3">
<textarea name="shell_txt" rows="5" cols="40"></textarea><br>
<input type="submit">
</form>
</body>
</html>';
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
}
//执行需写入的shell
if($_POST['login'] == "3"){ 
$filename="boyurl_cron.txt";
$str = $_POST["shell_txt"];
if ($str == "") {
  $str = "echo ok";
} else {
  $str = $_POST["shell_txt"];
}
if (!$head=fopen($filename, "w+")) {
die("尝试打开文件[".$filename."]失败!请检查是否拥有足够的权限!创建过程终止!");
}
if (fwrite($head,$str)==false) {
fclose($head);
die("写入内容失败!请检查是否拥有足够的权限!写入过程终止!");
}
session_start();
$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
echo $username,' 欢迎你！点击此处 <a href="boyurl.php?action=logout">注销</a> 登录！<br />';   
echo '写入成功！<br />'; 
echo '<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<form action="boyurl.php" method="post">
需执行的shell脚本:<br />
<input type="hidden" name="login"  value="3">
<textarea name="shell_txt" rows="5" cols="40"></textarea><br>
<input type="submit">
</form>
</body>
</html>';
fclose($head);
}

?> 