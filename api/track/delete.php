<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
$db = new DB();
header("Content-Type: application/json");
$data = null;
if (isset($_GET['id'])) {
  $data = $db->delete_track(htmlspecialchars($_GET['id']));
  http_response_code(204);
  return;
} else {
  http_response_code(402);
  echo json_encode([
    "status" => 402,
    "message" => "Missing parameter(s): id"
  ]);
}

exit();
