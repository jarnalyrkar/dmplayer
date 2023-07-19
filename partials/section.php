<section id="<?= $data_type ?>">
  <header>
    <?php if ($data_type === "preset") : ?>
      <div class="preset-header-row">
        <button class="action-button" data-action="toggle-themes"><span class="arrow">&larr;</span> Themes</button>
        <h2><?= $title ?></h2>
      </div>
    <?php else : ?>
      <h2><?= $title ?></h2>
    <?php endif; ?>
    <?php include $partials . "add-form.php"; ?>
  </header>
  <ul class="list">
    <?php if (count($items) > 0) : ?>
      <?php foreach ($items as $item) :
        $selected = "";
        if ($data_type === "theme") {
          $selected = $item["theme_id"] == $active_theme_id ? ' data-state="selected"' : "";
        } else if ($data_type === "preset") {
          $selected = ($item["preset_id"] == $active_preset_id) ? ' data-state="selected"' : "";
        }
      ?>
        <li data-id="<?= $item[$data_type . "_id"]; ?>" class="list__item" <?= $selected ?>>
          <input data-action="select" type="button" value="<?= $item['name']; ?>">
          <button class="action-button" data-action="delete">-</button>
        </li>
      <?php endforeach; ?>
    <?php else : ?>
      <li class="empty">No <?= $data_type; ?>s added yet!</li>
    <?php endif; ?>
  </ul>
  <template id="item">
    <li data-id="" class="list__item">
      <input data-action="select" type="button" value="">
      <button class="action-button" data-action="delete">-</button>
    </li>
  </template>
</section>