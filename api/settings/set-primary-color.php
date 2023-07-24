<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['color'])) {
  $data = $db->set_primary_color(htmlspecialchars($_GET['color']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
