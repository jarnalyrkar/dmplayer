<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
$db = new DB();
header("Content-Type: application/json");

$data = "";
if (isset($_GET['id'])) {
  $result = $db->get_files_by_track(htmlspecialchars($_GET['id']));
  if (count($result) > 0) {
    $data = $result[random_int(0, count($result) - 1)];
  } else {
    $data = false;
  }
}

if (!$data) {
  http_response_code(404);
  $id = $_GET['id'];
  return ['status' => 404, 'message' => "No files found for track id $id"];
}
$path = $db->get_media_folder() . htmlspecialchars_decode($data['filename']);
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
  echo json_encode($data);
} else {
  echo json_encode(['status' => 404, 'message' => "file not found: $path"]);
}
exit();
