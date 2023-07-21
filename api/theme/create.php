<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['name']) && isset($_GET['order'])) {
  $data = $db->create_theme(htmlspecialchars($_GET['name']), htmlspecialchars($_GET['order']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();

