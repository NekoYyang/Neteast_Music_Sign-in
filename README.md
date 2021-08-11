功能：
刷歌300首
云贝签到
监控自动运行
多用户
VIP卡密机制
使用说明：
1.导入music.sql文件到数据库内
2.配置application/database.php文件中的信息
3.配置伪静态
4.后台路径：域名/root
5.后台账号admin，密码666666

同时，我也搭建了这个程序，供大家免费使用。

没钱买服务器了 倒闭了

伪静态规则：
Apache：
RewriteEngine on
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?s=/$1 [QSA,PT,L]

Nginx：
if (!-d $request_filename){
        set $rule_0 1$rule_0;
}
if (!-f $request_filename){
        set $rule_0 2$rule_0;
}
if ($rule_0 = "21"){
        rewrite ^/(.*)$ /index.php?s=/$1 last;
}
