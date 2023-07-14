<section id="presets">
  <header>
    <h2>Presets</h2>
    <div>
      <form class="add-form">
        <label class="add-form__label" for="preset-name">Create new preset</label>
        <input class="add-form__input" required placeholder="E.g Busy townsquare, Quiet Night, Execution" type="text" id="item-name">
        <button type="button" class="action-button" id="add-preset">+</button>
      </form>
    </div>
  </header>
  <?php if (count($presets) > 0) : ?>
    <ul class="list">
      <?php foreach ($presets as $preset) : ?>
        <li data-preset-id="<?= $preset->preset_id; ?>" data-type="preset" class="list__item" <?= ($preset->preset_id == $active_preset_id ? 'data-state="selected"' : "") ?>>
          <input type="button" data-action="select" value="<?= $preset->name; ?>">
          <button class="action-button" data-action="delete">-</button>
        </li>
      <?php endforeach; ?>
    </ul>
    <template id="preset-item">
      <li data-theme-id="" class="list__item">
        <input data-action="select" type="button" value="">
        <button class="action-button" data-action="delete">-</button>
      </li>
    </template>
    <?php else: ?>
      <ul class="list"><li>Please add a preset</li></ul>
  <?php endif; ?>
</section>