
<?php

require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

check_csrf();

$_SESSION = [];

session_destroy();

header('Location: index.php');
exit;

?>
