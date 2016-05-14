<?php
/**
 * client to get operation data from server
 *
 * get actoin from server
 * */


while(true) {
    $action = file_get_contents("http://ctrl.dongyado.com/server.php?action=read");

    if ($action == "open") {
        exec("php operation.php {$action}");
    }
    echo $action."--\n";

    unset($action);
    sleep(3);
}

?>
