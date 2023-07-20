<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['id']) && isset($_GET['new-name'])) {
  $data = $db->update_preset(htmlspecialchars($_GET['id']), htmlspecialchars($_GET['new-name']));
} else {
  return "Missing get parameters id or new-name";
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
