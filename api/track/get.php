<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = "";
$db = new DB();
if (isset($_GET['theme_id']) && isset($_GET['type'])) {
  if ($_GET['type'] == 1) {
    $data = $db->get_music_by_theme(htmlspecialchars($_GET['theme_id']));
  }
  if ($_GET['type'] == 2) {
    $data = $db->get_effects_by_theme(htmlspecialchars($_GET['theme_id']));
  }
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
