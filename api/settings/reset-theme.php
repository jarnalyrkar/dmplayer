<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
$data = $db->reset_theme();

header("Content-Type: application/json");
echo json_encode($data);
exit();
