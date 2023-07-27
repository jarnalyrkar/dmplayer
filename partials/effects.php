<?php
$items = $db->get_effects_by_theme($active_theme_id);
$placeholder = "E.g Scream, Sword hits Metal, Fire spell";
$data_type = "effect";
$alphabet = range('a', 'z');
$numbers = range(1, 9);
$keystrokes = array_merge($numbers, $alphabet);
$keyCounter = 0;
?>
<section id="effect">
  <header>
    <?php include $partials . "add-form.php"; ?>
  </header>
  <ul class="list">
    <?php if (count($items) > 0) : ?>
      <?php foreach ($items as $item) : ?>
        <li draggable="true" data-order="<?= $item['order']; ?>" data-id="<?= $item['track_id'] ?>" data-keystroke="<?= $keystrokes[$keyCounter]; ?>">
          <div class="track__inner">
            <div class="track-title__container">
              <span class="track-title"><?= $item['name']; ?></span>
            </div>
            <div>
              <div class="keystroke__container">
                <button class="keystroke" data-action="play"><?= $keystrokes[$keyCounter]; ?></button>
              </div>
              <div class="play-actions">
                <button class="action-button" data-action="see-files" title="Effect settings"><?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/cogwheel.svg" ?></button>
                <button class="action-button" data-action="delete" title="Delete"><?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/trashcan.svg" ?></button>
              </div>
            </div>
          </div>
        </li>
        <?php $keyCounter++; ?>
      <?php endforeach; ?>
    <?php else : ?>
      <li class="empty">No effects added yet!</li>
    <?php endif; ?>
  </ul>
  <template id="effect-item">
    <li draggable="true" data-order="" data-id="" data-keystroke="">
      <div class="track__inner">
        <div class="track-title__container">
          <span class="track-title"></span>
        </div>
        <div class="keystroke__container">
          <button class="keystroke" data-action="play"></button>
        </div>
        <div class="play-actions">
          <button class="action-button" data-action="see-files"><?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/cogwheel.svg" ?></button>
          <button class="action-button" data-action="delete"><?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/trashcan.svg" ?></button>
        </div>
      </div>
    </li>
  </template>
</section>