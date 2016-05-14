# 使用arduino 远程控制开门

实现使用php Api调用，控制arduino开门， 具体步骤和思路在： 
http://dongyado.com/remote-control-door/tool/funny/2016/04/26/remote-control-open-door-with-arduino-and-raspberry/

## 所需物品
- arduino
- servo 
- 一台电脑
- 一个有外网IP的路由器

## 流程

客户端（手机） -> 访问API -> 发送指令 -> arduino -> 操作servo -> 按下按钮开门

## 初始化安装
权限：

    slayer@dongyado:~$ sudo usermod -a -G dialout slayer
    slayer@dongyado:~$ sudo chmod a+rw /dev/ttyACM0 


    或者 chmod 777 /dev/ttyACM0
    或者 chown www-data:www-data /dev/ttyACM0


## 初始化串口（可选）
    stty -F /dev/ttyACM0 cs8 9600 ignbrk -brkint -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts

