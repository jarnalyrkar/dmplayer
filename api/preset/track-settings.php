<?php

include $_SERVER['DOCUMENT_ROOT'] . '/DB.php';

$data = null;
$db = new DB();
if (isset($_GET['preset_id']) && isset($_GET['track_id'])) {
  $data = $db->get_preset_track_settings(htmlspecialchars($_GET['preset_id']), htmlspecialchars($_GET['track_id']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
