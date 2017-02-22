# boyurl
boyurl是一个通过PHP来远程执行shell脚本工具。整个程序只有两个文件，一个PHP文件，一个shell安装脚本，易于使用和安装。支持PHP5.2+以上的版本。提供的功能包括：
 
1：用户登录注销。 

2：记录每次使用的IP，方便审计。 

3：定时执行shell脚本。 

4：写入shell脚本文件。 

5：手机远程执行命令管理linux服务器。 

使用文档： https://ppabc.cn/1321.html

默认管理账号admin  密码boyurl.com

boyurl工具使用场景：linux默认都有开启SSH端口22，很容易被小黑扫描到，使用boyurl工具可以执行远程关闭SSH命令/sbin/service sshd stop，需要的使用SSH的时候，远程执行开启SSH命令/sbin/service sshd start，还可以远程执行其他命令，比如设置iptables规则、重启nginx等操作。

boyurl工具实现原理：通过linux自带的crontab定时检测一个URL，并执行URL里面的文本内容，这个URL里面的文本内容通过PHP程序来写入的。

boyurl工具安装方法：把boyurl.php和install.sh文件上传到站点的一个文件夹下面，比如网站是www.boyurl.com，可以在站点下创建一个复杂的文件夹名，比如xnhbsygdxg

需root权限安装，进入你www.boyurl.com网站目录：

mkdir -p /data/wwwroot/www.boyurl.com/xnhbsygdxg

cd /data/wwwroot/www.boyurl.com/xnhbsygdxg

执行命令：

bash install.sh www.boyurl.com xnhbsygdxg

安装完成后，查询命令：

crontab -l

显示：
*/5 * * * * curl -fsSL http://www.boyurl.com/xnhbsygdxg/boyurl_cron.txt  | sed   's/\r//g' | sh

xnhbsygdxg文件夹名是什么意思？小男孩不是一个胆小鬼的拼音开头字母，我相信你可以想出更诡异的文件夹名，避免被小黑扫描到，最好定期更换文件夹名，文件夹名更换后，crontab里面URL也需要更换哦。

最后，如果这个项目对您有所帮助，可以来支持一下https://ppabc.cn/


