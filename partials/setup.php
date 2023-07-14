<?php
include $_SERVER['DOCUMENT_ROOT'] . '/DB.php';

// Setup initial view
$db = new DB();
$themes = $db->get_themes();
$active_theme_id = $db->get_last_active_theme();
$presets = $db->get_presets_by_theme($active_theme_id);
$songs = $db->get_music_by_theme($active_theme_id);
$effects = $db->get_effects_by_theme($active_theme_id);
$active_preset_id = $db->get_last_active_preset($active_theme_id);