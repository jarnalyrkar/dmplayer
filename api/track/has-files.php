<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = "";
$db = new DB();
if (isset($_GET['id'])) {
  $data = $db->get_files_by_track(htmlspecialchars($_GET['id']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
