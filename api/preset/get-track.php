<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['track_id']) && isset($_GET['preset_id'])) {
  $current = $_GET['current'] ?? 0;
  $data = $db->get_track_preset(
    htmlspecialchars($_GET['track_id']),
    htmlspecialchars($_GET['preset_id']),
    $current
  );
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
