<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
header("Content-Type: application/json");

$data = null;
$db = new DB();
$missing_params = [];
if (!isset($_GET['name'])) {
  $missing_params[] = "name";
}
if (!isset($_GET['order'])) {
  $missing_params[] = "order";
}
if (!$missing_params) {
  http_response_code(200);
  echo json_encode($db->create_theme(htmlspecialchars($_GET['name']), htmlspecialchars($_GET['order'])));
} else {
  http_response_code(400);
  echo json_encode("Missing parameter(s): " . implode(', ', $missing_params));
}
exit();

