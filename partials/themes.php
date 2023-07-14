<section id="themes">
  <header>
    <h2>Themes</h2>
    <div>
      <form class="add-form">
        <label class="add-form__label" for="theme-name">Create new theme</label>
        <input class="add-form__input" required placeholder="E.g Dungeon, Airship, Mountains, Village" type="text" id="theme-name">
        <button type="button" class="action-button" id="add-theme">+</button>
      </form>
    </div>
  </header>
  <?php if (count($themes) > 0): ?>
    <ul class="list">
      <?php foreach ($themes as $theme) : ?>
        <li data-theme-id="<?= $theme->theme_id; ?>" class="list__item" <?= ($theme->theme_id == $active_theme_id ? 'data-state="selected"' : "") ?>>
          <input data-action="select" type="button" value="<?= $theme->name; ?>">
          <button class="action-button" data-action="delete">-</button>
        </li>
      <?php endforeach; ?>
    </ul>
    <template id="theme-item">
      <li data-theme-id="" class="list__item">
        <input data-action="select" type="button" value="">
        <button class="action-button" data-action="delete">-</button>
      </li>
    </template>
  <?php endif; ?>
</section>