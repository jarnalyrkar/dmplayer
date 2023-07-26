<?php
include $_SERVER['DOCUMENT_ROOT'] . '/DB.php';

// Setup initial view
$db = new DB();
$active_theme_id = $db->get_last_active_theme();
$active_preset_id = $db->get_last_active_preset($active_theme_id);
$init_volume = $db->get_last_effect_volume();

$primary_color = $db->get_primary_color();
$accent_color = $db->get_accent_color();
$text_color = $db->get_text_color();
$shades = $db->get_shades();

$font = $db->get_font();

$background_image = null;
$background_image = $db->get_background_image();

$local_image = glob("bg.{jpg,jpeg,png,gif,webp,avif}", GLOB_BRACE)[0] ?? false;
if (!$background_image && file_exists($local_image)) {
  $background_image = $local_image;
}

