<?php
/**
 * simple controll api 
 *
 * @author dongyado<dongyado@gmail.com>
 * */

if (isset($_GET['action'])) {
    $action = trim($_GET['action']);
    exec("php operation.php {$action}");

    echo "opened";
    exit();
}

echo "Failed";
?>
