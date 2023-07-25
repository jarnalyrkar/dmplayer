<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');
$db = new DB();
header("Content-Type: application/json");

if (isset($_GET['id'])) {
  echo json_encode($db->delete_presets_by_theme(htmlspecialchars($_GET['id'])));
}

exit();
