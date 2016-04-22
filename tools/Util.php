<?php
/**
 * Util class
 *
 * */

class Util{
    public static function generateToken($conf){
        return md5("!".$conf['user']."@". $conf['password'] ."#".$conf['salt']);
    }
}

?>
