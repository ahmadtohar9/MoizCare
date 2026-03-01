<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
$_POST['slip_id'] = 6;
// Bypass CI login redirect globally
define('ENVIRONMENT', 'development');
chdir(__DIR__);
ob_start();
require_once 'index.php';
$output = ob_get_clean();
echo "CAPTURED OUTPUT:\n";
echo $output;
