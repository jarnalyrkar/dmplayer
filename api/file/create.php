<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$data = null;
$db = new DB();

$allowedFormats = ['mp3', 'flac', 'ogg', 'vorbis', 'wav', 'mp4'];
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if (in_array($extension, $allowedFormats)) {
  move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/audio/" . $_FILES['file']['full_path']);
  $data = $db->create_file(htmlspecialchars($_FILES['file']['full_path']), htmlspecialchars($_POST['track_id']));
} else {
  echo $extension . " not supported";
}

header("Content-Type: application/json");
echo json_encode($data);
exit();