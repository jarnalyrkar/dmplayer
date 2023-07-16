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
    <h2>Effects</h2>
    <?php include $partials . "add-form.php"; ?>
  </header>
  <ul class="list">
    <?php if (count($items) > 0): ?>
      <?php foreach ($items as $item) : ?>
        <li data-id="<?= $item['track_id'] ?>" data-keystroke="<?= $keystrokes[$keyCounter]; ?>">
          <div>
            <span>[<span class="keystroke"><?= $keystrokes[$keyCounter]; ?></span>]</span> <span class="track-title"><?= $item['name']; ?></span>
            <div class="play-actions">
              <button class="action-button" data-action="play">&#10148;</button>
              <button class="action-button" data-action="see-files">&#128065;</button>
              <button class="action-button" data-action="delete">-</button>
            </div>
          </div>
        </li>
        <?php $keyCounter++; ?>
      <?php endforeach; ?>
    <?php else: ?>
      <li class="empty">No tracks yet</li>
    <?php endif; ?>
  </ul>
  <template id="effect-item">
    <li data-id="" data-keystroke="">
      <div>
        <span>[<span class="keystroke"></span>]</span> <span class="track-title"></span>
        <div class="play-actions">
          <button class="action-button" data-action="play">&#10148;</button>
          <button class="action-button" data-action="see-files">&#128065;</button>
          <button class="action-button" data-action="delete">-</button>
        </div>
      </div>
    </li>
  </template>
</section>