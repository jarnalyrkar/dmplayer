<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'DB.php';

// Setup initial view
$db = new DB();
$themes = $db->get_themes();
$active_theme_id = $db->get_last_active_theme();
$presets = $db->get_presets_by_theme($active_theme_id);
$songs = $db->get_music_by_theme($active_theme_id);
$effects = $db->get_effects_by_theme($active_theme_id);
$active_preset_id = $db->get_last_active_preset();

// Set volume per track according to active preset
?>
<div>
  <h2>Themes</h2>
  <?php if (count($themes) > 0) : ?>
    <ul>
      <?php foreach ($themes as $theme) : ?>
        <li><?= $theme->name; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<div>
  <h2>Presets</h2>
  <?php if (count($presets) > 0) : ?>
    <ul>
      <?php foreach ($presets as $preset) : ?>
        <li><?= $preset->name; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<div>
  <h2>Tracks</h2>
  <?php if (count($songs) > 0) : ?>
    <ul>
      <?php foreach ($songs as $song) : ?>
        <li><?= $song->name; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>
<div>

  <h2>Effects</h2>
  <?php if (count($effects) > 0) : ?>
    <ul>
      <?php foreach ($effects as $effect) : ?>
        <li><?= $effect->name; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>