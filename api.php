<?php

if (isset($_GET['action'])) {
    file_put_contents('/tmp/ar.log', " ==============\n", FILE_APPEND);
    file_put_contents('/tmp/ar.log', time(). " exec\n", FILE_APPEND);

    $action = trim($_GET['action']);
    exec("php operation.php {$action}");

    echo "opened";
    exit();
}

echo "Failed";
?>
