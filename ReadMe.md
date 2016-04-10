# arduino 远程控制

## 安装
权限：

slayer@dongyado:~$ sudo usermod -a -G dialout slayer
slayer@dongyado:~$ sudo chmod a+rw /dev/ttyACM0 


或者 chmod 777 /dev/ttyACM0
或者 chown www-data:www-data /dev/ttyACM0


## 初始化串口
stty -F /dev/ttyACM0 cs8 9600 ignbrk -brkint -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts

