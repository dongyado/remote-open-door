<?php
/**
 * tplink router control
 *
 * get the external ip 
*
* @author dongyado<dongyado@gmail.com>
*/

require "./tools/Util.php";
require("./tools/HttpClient.class.php");
$conf = include "config.php";

/**
* 加密函数
* 
* 
*/
function securityEncode($input1, $input2, $input3) {
    $dictionary = $input3;
    $output  = "";
    $cl = 0xBB;
    $cr = 0xBB;
    
    $len1 = strlen($input1);
    $len2 = strlen($input2);
    $lenDict = strlen($dictionary);
    
    $len = $len1 > $len2 ? $len1 : $len2;

    for( $index = 0; $index < $len; $index++){
        $cl = 0xBB;
        $cr = 0xBB;
        
        if ($index >= $len1) {
            $cr = ord($input2[$index]);
        }
        else if ($index >= $len2) {
            $cl = ord($input1[$index]);
        }else {
            $cl = ord($input1[$index]);
            $cr = ord($input2[$index]);
        }
        
        $tmp = ($cl ^ $cr) % $lenDict;
        $output .= $dictionary[$tmp];
    }    
    return $output;
}

/**
* 加密的password
* 
*/
function orgAuthPwd($pwd) {
    	$strDe = "RDpbLfCPsJZ7fiv";
		$dic = "yLwVl0zKqws7LgKPRQ84Mdt708T1qQ3Ha7xv3H7NyU84p21BriUWBU43odz3iP4rBL3cD02KZciX".
				  "TysVXiV8ngg6vL48rPJyAUw0HurW20xqxv9aYb4M9wK1Ae0wlro510qXeU07kV57fQMc8L6aLgML".
				  "wygtc0F10a0Dg70TOoouyFhdysuRMO51yY5ZlOZZLEal1h0t9YQW0Ko7oBwmCAHoic4HYbUyVeU3".
				  "sfQ1xtXcPcf1aT303wAQhv66qzW";

		return securityEncode($pwd, $strDe, $dic);
}


$password = orgAuthPwd("daydayup");


// $input1 = 'cA4l15])f{P^Fv$I';
// $input3 = 'I0pnNC[On,[lCjvoXXJ0EM2$3K,!JWs$*CEXuqY~6s3$hvk!+IjNXShu0I0C6bH7xiNspf7{k0oH0E]R50,2bciVu]w$)YqX1PUW[O*t,cFlupDKs9mnu9*A1Or4zb+z$]d4B>++42xW!GNN9RGxcnsRED({5uDl,j~mAp*]87]KW!sp~OfiZC]0Wl!KJuOU(L!A4iECD2{2W.c(Ww~Do0S4mc3gLUyneXiJ4{<<DcSD<0{yxW$p3{uug3,W';

// echo securityEncode($input1, $password, $input3)."\n";

// first try
$httpClient = new HttpClient("192.168.1.1");

$status = 401;
$id = "";
$opath = "?code=2&asyn=1";
$duration = 0;
$ip = "";
while(true) {
    
    $path = $id == "" ? $opath : $opath . "&id={$id}";
        
    $ret = $httpClient->post($path, array(23));
    
    // get the reponse data after login
    echo "status: ".$ret['status']."\n";
    $status = $ret['status'];
    
    $data = preg_split("/\r\n/", $ret['body']);
    if ($status == 401 || $id == "") {
        // handle the body
        
        echo "auth data:" .json_encode($data)."\n";

        // generate
        $id = securityEncode($data[3], $password, $data[4]);
        echo "id: {$id}\n";

        //file_put_contents("./log", "[duration] :" . (time() - $duration)."\n", FILE_APPEND);
        //$duration = time();
        sleep(5);
        continue;
    } 
    

    // get data
    //$datas = preg_split("/\r\n/", $ret['body']);

    $_data = array();
    foreach($data as $item) {
        $record = explode(" ", $item);
        if(count($record) > 1)
            $_data[$record[0]] = $record[1];
        else 
            $_data[] = $item;
    }


    if ( ($ip == "") || ($ip != "" && $_data['ip'] != $ip)) {
        $token = Util::generateToken($conf);
        exec('./tools/mail.sh "137042663@qq.com" "ipchanged"  "'.date('Y-m-d H:i:s')." http://".$_data['ip'].':88/?access_token='.$token.'"');        
    }
    $ip = $_data['ip'];

    echo "[".date('Y-m-d H:i:s')."] ip: {$_data['ip']}\n";
    sleep(120);
}

?>
