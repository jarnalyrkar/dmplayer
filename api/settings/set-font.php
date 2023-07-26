<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['font'])) {
  $data = $db->set_font(htmlspecialchars($_GET['font']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
