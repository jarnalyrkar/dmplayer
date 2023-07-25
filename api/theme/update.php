<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$db = new DB();
header("Content-Type: application/json");

$missing_params = [];
if (!isset($_GET['id'])) {
  $missing_params[] = 'id';
}
if (!isset($_GET['new-name'])) {
  $missing_params[] = 'new-name';
}

if (!$missing_params) {
  header(200);
  echo json_encode($db->update_theme(htmlspecialchars($_GET['id']), htmlspecialchars($_GET['new-name'])));
} else {
  header(400);
  echo json_encode("Missing parameter(s): " . implode(", ", $missing_params));
}

exit();
