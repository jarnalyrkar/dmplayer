<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['preset_id']) && isset($_GET['theme_id'])) {
  $data = $db->update_last_preset(htmlspecialchars($_GET['preset_id']), htmlspecialchars($_GET['theme_id']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
