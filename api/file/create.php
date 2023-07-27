<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
$db = new DB();

$data = null;
if (!isset($_FILES) || count($_FILES) === 0) {
  echo json_encode(['message' => 'No files found', 'data' => file_get_contents( 'php://input' )]);
  return;
}
if (!isset($_POST['track_id'])) {
  echo json_encode(['message' => 'Missing track id']);
  return;
}

$results = [];
for ($i = 0; $i < count($_FILES); $i++) {

  $existing = $db->get_file_by_name($_FILES["file-$i"]['name']);
  if ($existing) {
    $results['files'][] = [
      'id' => $db->add_file_to_track($existing, htmlspecialchars($_POST['track_id'])),
      'name' => $existing,
    ];
    $results['messages'][] = "File $existing already exists. Adding to track.";
  }

  $allowedFormats = ['mp3', 'flac', 'ogg', 'vorbis', 'wav', 'mp4'];
  $extension = pathinfo($_FILES["file-$i"]['name'], PATHINFO_EXTENSION);
  if (in_array($extension, $allowedFormats)) {
    $path = $_SERVER['DOCUMENT_ROOT'] . "/" . $db->get_media_folder();
    $filename = $_FILES["file-$i"]['name'];
    $pathAndFilename = $path . "/" . $filename;
    move_uploaded_file($_FILES["file-$i"]['tmp_name'], $pathAndFilename);
    $db->create_file(htmlspecialchars($filename), htmlspecialchars($_POST['track_id']));
    $results['files'][] = [
      'id' => $db->create_file(htmlspecialchars($filename), htmlspecialchars($_POST['track_id'])),
      'name' => $filename,
    ];
  } else {
    $results['messages'][] = "$extension not supported, in file " . $_FILES["file-$i"]['name'];
  }
}
header("Content-Type: application/json");
http_response_code(200);
echo json_encode($results);
exit();


