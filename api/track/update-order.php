<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['track_id']) && isset($_GET['theme_id']) && isset($_GET['order'])) {
  $data = $db->update_theme_track_order(
    htmlspecialchars($_GET['track_id']),
    htmlspecialchars($_GET['theme_id']),
    htmlspecialchars($_GET['order'])
  );
} else {
  $data = "Missing get parameters id or order";
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
