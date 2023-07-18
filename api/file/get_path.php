<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = "";
$db = new DB();
$data = $db->get_media_folder();

header("Content-Type: application/json");
echo json_encode($data);
exit();
