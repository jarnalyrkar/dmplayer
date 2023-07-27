<?php

if (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN') {
  ini_set("extension_dir", ".\php\ext");
}

$partials = $_SERVER['DOCUMENT_ROOT'] . "/partials/";
include $partials . "setup.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Game Master Player</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="/assets/js/color-picker/index.min.css">
  <link rel="stylesheet" href="/assets/scss/style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=<?= $font; ?>&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary-100: <?= $shades[0] ?>;
      --primary-200: <?= $shades[1] ?>;
      --primary-300: <?= $shades[2] ?>;
      --primary-400: <?= $shades[3] ?>;
      --primary-400-trans: <?= $shades[4] ?>;
      --primary-500: <?= $shades[5] ?>;
      --primary-600: <?= $shades[6] ?>;
      --primary-600-trans: <?= $shades[7] ?>;
      --accent: <?= $accent_color ?>;
      --text: <?= $text_color ?>;
      --font: "<?= str_replace("+", ' ', $font); ?>"
    }
  </style>
</head>

<body <?= isset($background_image) ? "style=\"background-image: url($background_image)\"" : '' ?>>
  <div class="header-bg"></div>
  <main>
    <button class="action-button" data-action="toggle-themes">
      <span class="arrow">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/arrow-left.svg" ?>
      </span>
      Themes
    </button>

    <?php
    $title = "Themes";
    $data_type = "theme";
    $items = $db->get_themes();
    $placeholder = "E.g The Heart of Flamespike Citadel";
    include $partials . "section.php";

    $title = "Presets";
    $data_type = "preset";
    $items = $db->get_presets_by_theme($active_theme_id);
    $placeholder = "E.g Tavern, Forest, Temple, Goblin Hideout";
    ?>
    <div class="action-section">
      <?php
      include $partials . "section.php";
      include $partials . "tracks.php";
      include $partials . "effects.php";
      ?>
    </div>
  </main>
  <aside>
    <?php include $partials . "infobox.php"; ?>
    <?php include $partials . "toast.php"; ?>
    <?php include $partials . "settings.php"; ?>
  </aside>

  <?php include $partials . "footer.php"; ?>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.0.1/color-thief.min.js"></script>
  <script src="/assets/js/color-picker/index.min.js"></script>
  <script src="/assets/js/script.js"></script>

</body>

</html>