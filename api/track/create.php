<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['name']) && isset($_GET['theme_id']) && isset($_GET['type_id'])) {
  $data = $db->create_track(
    htmlspecialchars($_GET['name']),
    htmlspecialchars($_GET['theme_id']),
    htmlspecialchars($_GET['type_id']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
