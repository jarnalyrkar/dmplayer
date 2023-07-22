<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = "";
$db = new DB();
if (isset($_GET['id'])) {
  $result = $db->get_files_by_track(htmlspecialchars($_GET['id']));
  if (count($result) > 0) {
    $data = $result[random_int(0, count($result) - 1)];
  } else {
    $data = false;
  }
}

header("Content-Type: application/json");
$path = $db->get_media_folder() . $data['filename'];
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
  echo json_encode($data);
} else {
  echo json_encode(['message' => "file not found", 'status' => 404, 'data' => $path]);
}
exit();
