<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();


if (!isset($_FILES['file'])) {
  echo json_encode(['status' => 400, 'message' => 'No file found', 'data' => file_get_contents( 'php://input' )]);
  return;
}
if (!isset($_POST['track_id'])) {
  echo json_encode(['status' => 400, 'message' => 'Missing track id']);
  return;
}

$existing = $db->get_file_by_name($_FILES['file']['name']);
if ($existing) {
  $db->add_file_to_track($existing, htmlspecialchars($_POST['track_id']));
  echo json_encode($existing);
  return;
}

$allowedFormats = ['mp3', 'flac', 'ogg', 'vorbis', 'wav', 'mp4'];
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (in_array($extension, $allowedFormats)) {
  move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $db->get_media_folder() . $_FILES['file']['full_path']);
  $data = $db->create_file(htmlspecialchars($_FILES['file']['full_path']), htmlspecialchars($_POST['track_id']));
} else {
  echo $extension . " not supported";
}

header("Content-Type: application/json");
echo json_encode($data);
exit();