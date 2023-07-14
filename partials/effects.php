<section id="tracks">
  <header>
    <h2>Effects</h2>
    <div>
      <form class="add-form">
        <label class="add-form__label" for="theme-name">Create new Effect</label>
        <input class="add-form__input" required placeholder="E.g Scream, Atonal Bells, Fire spell" type="text" id="theme-name">
        <button type="button" class="action-button" id="add-track">+</button>
      </form>
    </div>
  </header>
  <?php if (count($effects) > 0) : ?>
    <ul class="tracks">
      <?php foreach ($effects as $effect) : ?>
        <li data-id="<?= $effect->track_id ?>" data-volume="" data-type="tracks">
          <div>
            <span><?= $effect->name; ?></span>
            <div>
              <button class="action-button" data-action="play">&#10148;</button>
              <input type="file" id="new-file">
              <label class="action-button" data-action="add-file" for="new-file">+</label>
              <button class="action-button" data-action="delete">-</button>
            </div>
          </div>
          <?php
          $files = $db->get_files_by_track($effect->track_id);
          if (count($files) > 0) :
          ?>
            <ul class="files">
              <?php foreach ($files as $file) : ?>
                <li class="files__file" data-filename="<?= $file->filename ?>">
                  <span><?= $file->filename ?></span>
                  <div>
                    <button class="action-button">&#10148;</button>
                    <button class="action-button" data-action="delete">-</button>
                </li>
                </div>
              <?php endforeach; ?>
            </ul>
          <?php else : ?>
            <ul>
              <li>Please add audio files to this preset</li>
            </ul>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else : ?>
    <ul>
      <li>Please add a track</li>
    </ul>
  <?php endif; ?>
</section>