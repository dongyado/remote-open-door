<?php
require "./tools/Util.php";
$conf = include "config.php";

// access check
$auth = false;
$auth_code = "";
if (isset($_GET['access_token']))
{
    $access_token = trim($_GET['access_token']);

    if (strlen($access_token) == 32) {
        $sign = Util::generateToken($conf);
        if (strncmp($access_token, $sign, 32) == 0) {
            $auth = true;
            $auth_code = $sign;
        }
    } 
}



// handle action
if (isset($_GET['action'])) {
    $action = trim($_GET['action']);
    if ($auth) {
        // open the door
        if ($action == 'open')
        {
            /**
             * visit operation will cause the arduino work inconrrect  ,
             * but work fine if in cli mode 
             * */
            exec("php operation.php {$action}");
            exit("Opened");
        }
    } else {
        
        if ($action == 'login') {
            
            $user     = isset($_POST['user']) ? trim($_POST['user']) : ""; 
            $password = isset($_POST['password']) ? trim($_POST['password']) : ""; 

            if ($user== $conf['user'] && $password == $conf['password'])
            {
                $location = "index.php?access_token=".Util::generateToken($conf);
                header("Location:{$location}");
            }
        }
    }
    exit("Failed");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Open the damn door</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
<style>
* {margin: 0; padding: 0}
body{width: auto;}
h4 {border-bottom: 1px solid #CCC; height: 30px; line-height: 30px; text-align: center;}
.container{
padding: 10px;
text-align: left;
width: 90%;
border: 1px solid #CCC;
overflow: hidden;
height: 100%;
box-sizing: border-box;
margin: 5px auto;
}
.button {
display: block;
width: 80px;
height: 30px; 
font-size: 1em;
line-height: 30px;
background: #CCC;
color: #444;
text-align: center;
margin: 10px auto;
text-decoration: none;
}
.text {
width: 200px;
height: 30px;
padding-left: 5px;
line-height: 30px;
display: block;
margin: 10px auto;
border: 1px solid #CCC;
}

a {cursor: pointer;}

</style>
</head>
<body>
<div class="container" >
<h4> Remote Control Open Door</h4>
<?php 
if ($auth) {
?>
<a class="button" onclick="ArduinoCtrl.open(this);" >Open</a>
<?php    
} else {
?>
<div class="form" >
<form action="?action=login" method="post" >
<input class="text" type="text" name="user" />
<input class="text" type="password" name="password" />
<input class="text" type="submit" value="登录" />
</div>
</form>
<?php
}
?>
</div>

<script type="text/javascript" src="zepto.min.js" ></script>
<script type="text/javascript" >

// Controll Object
var ArduinoCtrl = function(){
    var o = {};
    o.lock = false;

    o.open = function(node) {
        if (o.lock) return;

        o.lock = true;
        $.ajax({
                url: "?action=open<?=$auth_code != "" ? "&access_token={$auth_code}" :""?>",
                beforeSend: function(){
                    node.innerHTML = "opening";
                },
                success: function(data){
                    node.innerHTML = data;
                    setTimeout(function(){node.innerHTML = "Open"; o.lock = false;}, 1000);
                }
            });
    }

    return o;
}();

</script>
</body>
</html>
