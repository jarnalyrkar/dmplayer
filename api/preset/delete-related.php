<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['id'])) {
  $data = $db->delete_presets_by_theme(htmlspecialchars($_GET['id']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
