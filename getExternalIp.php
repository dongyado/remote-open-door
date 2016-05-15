<?php
/**
 * tplink router control 
 * 
 * simulate login to get  external ip, 
 * and send current ip to someone by email when the ip changed 
 *
 * @author dongyado<dongyado@gmail.com>
 */

require "./tools/Util.php";
require("./tools/HttpClient.class.php");
$conf = include "config.php";

/**
* encrypt function
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
* password encrypt from original password
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

// ecrypted orginal password
$password = orgAuthPwd($conf['router_passwd']);

$httpClient = new HttpClient($conf['router_host']);

// request example
// http:192.168.1.1/?code=2&asyn=1&id=R0TPpo5pDg%3CJr)5k
$status = 401;
$id = ""; // global id to store newest id
$opath = "?code=2&asyn=1"; // prefix of path 
$duration = 0;
$ip = "";

// loop to check the external ip
while(true) {
    
    $path = $id == "" ? $opath : $opath . "&id={$id}";
    $ret = $httpClient->post($path, array(23));
    
    // get the reponse data after login
    echo "status: ".$ret['status']."\n";
    
    $status = $ret['status'];
    // parse body
    $data = preg_split("/\r\n/", $ret['body']);
    
    // response 401, Unauthoried
    if ($status == 401 || $id == "") {

        echo "auth data:" .json_encode($data)."\n";

        // generate new id
        $id = securityEncode($data[3], $password, $data[4]);
        echo "id: {$id}\n";

        //file_put_contents("./log", "[duration] :" . (time() - $duration)."\n", FILE_APPEND);
        sleep(5);
        continue; 
    } 
    
    // response ok		
    // parse data
    $_data = array();
    foreach($data as $item) {
        $record = explode(" ", $item);
        if(count($record) > 1)
            $_data[$record[0]] = $record[1];
        else 
            $_data[] = $item;
    }

    // send email if necessary
    if ( ($ip == "") || ($ip != "" && $_data['ip'] != $ip)) {
        $token = Util::generateToken($conf);
        // send email
        exec('./tools/mail.sh "'.$conf['email'].'" "ipchanged"  "'.date('Y-m-d H:i:s')." http://".$_data['ip'].':88/?access_token='.$token.'"');        
    }
    $ip = $_data['ip'];

    echo "[".date('Y-m-d H:i:s')."] ip: {$_data['ip']}\n";
    
    // sleep 2 minutes
    sleep(120);
}

?>
