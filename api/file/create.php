<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
system("touch " . $_SERVER['DOCUMENT_ROOT'] . "/myfile.test");
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
  $path = $_SERVER['DOCUMENT_ROOT'] . $db->get_media_folder();
  $filename = $_FILES['file']['name'];
  $pathAndFilename = $path . $filename;
  move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . "/audio/" . $filename);
  $match = [];
  preg_match("/^([^.]+)/", $filename, $match);
  $winExe = $_SERVER['DOCUMENT_ROOT'] . "/ffmpeg.exe";
  $linux = $_SERVER['DOCUMENT_ROOT'] . "/ffmpeg";
  $convertToOgg = (php_uname('s') === "Linux" ? $linux : $winExe) . " -i \"" . $_SERVER['DOCUMENT_ROOT'] . "/audio-dev/$filename\" -c:a libvorbis -q:a 4 \"" . $path . $match[0] . ".ogg" . "\"";
  passthru($convertToOgg);
  $data = $db->create_file(htmlspecialchars($match[0] . ".ogg"), htmlspecialchars($_POST['track_id']));
} else {
  echo $extension . " not supported";
}

header("Content-Type: application/json");
echo json_encode($data);
exit();