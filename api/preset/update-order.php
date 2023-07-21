<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['preset_id']) && isset($_GET['theme_id']) && isset($_GET['order'])) {
  $data = $db->update_preset_theme_order(
    htmlspecialchars($_GET['preset_id']),
    htmlspecialchars($_GET['theme_id']),
    htmlspecialchars($_GET['order']));
} else {
  $data = "Missing get parameters id or order";
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
