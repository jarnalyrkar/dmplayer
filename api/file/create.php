<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
$db = new DB();

$data = null;

if (!isset($_FILES['file'])) {
  echo json_encode(['message' => 'No file found', 'data' => file_get_contents( 'php://input' )]);
  return;
}
if (!isset($_POST['track_id'])) {
  echo json_encode(['message' => 'Missing track id']);
  return;
}

$existing = $db->get_file_by_name($_FILES['file']['name']);
if ($existing) {
  $db->add_file_to_track($existing, htmlspecialchars($_POST['track_id']));
  header("Content-Type: application/json");
  http_response_code(200);
  echo json_encode($existing);
  exit();
}

$allowedFormats = ['mp3', 'flac', 'ogg', 'vorbis', 'wav', 'mp4'];
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (in_array($extension, $allowedFormats)) {
  $path = $_SERVER['DOCUMENT_ROOT'] . "/" . $db->get_media_folder();
  $filename = $_FILES['file']['name'];
  $pathAndFilename = $path . "/" . $filename;
  move_uploaded_file($_FILES['file']['tmp_name'], $pathAndFilename);
  header("Content-Type: application/json");
  http_response_code(200);
  json_encode($db->create_file(htmlspecialchars($filename), htmlspecialchars($_POST['track_id'])));
} else {
  header("Content-Type: application/json");
  http_response_code(400);
  echo json_encode($extension . " not supported");
}

exit();
