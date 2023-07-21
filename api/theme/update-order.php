<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['id']) && isset($_GET['order'])) {
  $data = $db->update_theme_order(htmlspecialchars($_GET['id']), htmlspecialchars($_GET['order']));
} else {
  $data = "Missing get parameters id or order";
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
