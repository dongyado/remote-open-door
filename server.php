<?php
/**
 * for server 
 *
 * */
$lock_file = '/tmp/ctrl.lock';

if(isset($_GET['action']) )
{
    $action = trim($_GET['action']);

    if ($action == "open") {
        file_put_contents($lock_file, 'open');
        echo 'opening';
    }

    if ($action == "read") 
    {
        $action = file_get_contents($lock_file);

        echo $action;
        file_put_contents($lock_file, '');
    }
}

?>
