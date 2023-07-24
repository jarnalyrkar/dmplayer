<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/DB.php');

$data = null;
$db = new DB();
if (isset($_GET['url'])) {
  $data = $db->set_background_image(htmlspecialchars($_GET['url']));
}

header("Content-Type: application/json");
echo json_encode($data);
exit();
