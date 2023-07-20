<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['volume'])) {
  $data = $db->set_last_effect_volume(htmlspecialchars($_GET['volume']));
} else {
  return "Missing get parameters id or new-name";
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
