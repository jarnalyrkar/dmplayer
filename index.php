<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dungeon Master Player</title>
  <link rel="stylesheet" href="/assets/scss/style.css">
</head>

<body>
  <main>
    <?php
    $partials = $_SERVER['DOCUMENT_ROOT'] . "/partials/";
    include $partials . "setup.php";

    $title = "Themes";
    $data_type = "theme";
    $items = $db->get_themes();
    $placeholder = "E.g Dungeon, Airship, Mountains, Village";
    include $partials . "section.php";

    $title = "Presets";
    $data_type = "preset";
    $items = $db->get_presets_by_theme($active_theme_id);
    $placeholder = "E.g Busy townsquare, Quiet Night, Execution";
    ?>
    <div class="action-section">
    <?php
      include $partials . "section.php";
      include $partials . "tracks.php";
      include $partials . "effects.php";
      ?>
    </div>
  </main>
  <footer>
    <div class="footer-buttons">
      <button class="action-button" data-action="settings">&#9881;</button>
      <button class="action-button" data-action="stop">&block;</button>
      <button class="action-button" data-action="info">&#8505;</button>
    </div>
    <div class="footer-sliders">
      <div class="footer-slider">
        <label>Effects</label>
        <div class="volume-bar">
          <?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/speaker.svg" ?>
          <div class="volume-bar-background">
            <input id="main-effects-volume" type="range" min="0" max="100" value="100">
          </div>
        </div>
      </div>
  </footer>
  <script src="/assets/js/script.js"></script>
</body>

</html>