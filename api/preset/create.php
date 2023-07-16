<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['name']) && isset($_GET['theme_id'])) {
  $current = $_GET['current'] ?? 0;
  $data = $db->create_preset(htmlspecialchars($_GET['name']), htmlspecialchars($_GET['theme_id']), $current);
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
