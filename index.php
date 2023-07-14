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
    include $partials . "themes.php";
    include $partials . "presets.php";
    include $partials . "tracks.php";
    include $partials . "effects.php";
    ?>
  </main>
  <script src="/assets/js/script.js"></script>
</body>

</html>