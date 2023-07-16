<?php
include $_SERVER['DOCUMENT_ROOT'] . '/DB.php';

// Setup initial view
$db = new DB();
$active_theme_id = $db->get_last_active_theme();
$active_preset_id = $db->get_last_active_preset($active_theme_id);