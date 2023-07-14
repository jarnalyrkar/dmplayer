<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'db.php';

$db = new Db();
$themes = $db->get_themes();
$active_theme_id = $db->get_last_active_theme();
$active_preset_id = $db->get_last_active_preset();

// Set volume per track according to active preset
?>

<style>
  .volume {
    height: 5px;
    width: 268px;
    background-color: blue;
    display: inline-block;
  }

  h3 {
    margin: 0;
  }

  li {
    background-color: rgba(0, 0, 0, 25%);
    border-radius: 15px;
    padding: 1rem;
  }

  li>div:first-child {
    display: flex;
    align-items: center;
    gap: .5rem;
    justify-content: space-between;
  }

  .effect {
    border-color: goldenrod;
    border-style: ridge;
    background: linear-gradient(120deg, goldenrod 0%, red 100%);
    border-radius: 50%;
    height: 4rem;
    width: 4rem;
    transition: border-color 150ms;
    cursor: pointer;
  }

  .effect:active {
    border-color: brown;
    background: linear-gradient(120deg, goldenrod 0%, red 75%);
  }

  .li {
    max-width: 100px;
  }

  .li>div:first-child {
    flex-direction: column;
    align-items: start;
  }

  body {
    display: flex;
    justify-content: space-evenly;
  }
</style>
<div>
  <h2>Themes</h2>
  <ul>
    <li>Insane Asylum</li>
  </ul>
</div>

<div>
  <h2>Presets</h2>
  <ul>
    <li>Entrance</li>
    <li>Chapel</li>
    <li>Showers</li>
    <li>Kitchen</li>
  </ul>
</div>

<div>
  <h2>Tracks</h2>
  <ul>
    <li>
      <div>
        <h3>Howling Wind</h3>
        <div>
          <button aria-label="Add files to track">➕🎵</button>
          <button aria-label="See all tracks">🎵</button>
          <button aria-label="Delete track">🗑️</button>
        </div>
      </div>
      <button>▶️</button>
      <div class="volume"></div>
    </li>
  </ul>
</div>
<div>

  <h2>Effects</h2>
  <ul>
    <li class="li">
      <div>
        <h3>Fireball</h3>
        <div>
          <button aria-label="Add files to effect">➕🎵</button>
          <button aria-label="See all files">🎵</button>
          <button aria-label="Delete effect">🗑️</button>
        </div>
      </div>
      <button class="effect"></button>
    </li>
  </ul>
</div>