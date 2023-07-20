<?php

include $_SERVER['DOCUMENT_ROOT'] . '/DB.php';

$data = null;
$db = new DB();
if (isset($_GET['preset_id']) && isset($_GET['track_id']) && isset($_GET['volume'])) {
  $data = $db->update_preset_track_volume(
          htmlspecialchars($_GET['preset_id']),
          htmlspecialchars($_GET['track_id']),
          htmlspecialchars($_GET['volume'])
        );
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
