<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
$db = new DB();

// header("Content-Type: application/json");
if (!isset($_GET['id'])) {
  http_response_code(400);
  echo json_encode(["status" => "400", "message" => "Missing parameter: " . "id"]);
} else {
  http_response_code(200);
  echo json_encode($db->delete_theme(htmlspecialchars($_GET['id'])));
}
exit();
