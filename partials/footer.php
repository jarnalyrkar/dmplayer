<footer class="page-footer">
  <div class="footer-buttons">
    <button class="action-button" title="Settings" data-action="settings">
      <?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/cogwheel.svg" ?>
    </button>
    <button class="action-button" title="Info about the program" data-action="info">
      <?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/info.svg" ?>
    </button>
    <button class="action-button" title="Stop all tracks" data-action="stop">
      <?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/square.svg" ?>
    </button>
  </div>

  <div class="footer-sliders">
    <div class="footer-slider">
      <label>Effects</label>
      <div class="volume-bar">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/speaker.svg" ?>
        <div class="volume-bar-background">
          <input id="main-effects-volume" type="range" min="0" max="100" value="<?= $init_volume; ?>">
        </div>
      </div>
    </div>
</footer>