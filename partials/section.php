<section id="<?= $data_type ?>">
  <header>
    <h2><?= $title ?></h2>
    <?php include $partials . "add-form.php"; ?>
  </header>
    <ul class="list">
      <?php foreach ($items as $item):
        $selected = "";
        if ($data_type === "theme") {
          $selected = $item["theme_id"] == $active_theme_id ? ' data-state="selected"' : "";
        } else if ($data_type === "preset") {
          $selected = ($item["preset_id"] == $active_preset_id) ? ' data-state="selected"' : "";
        }
        ?>
        <li data-id="<?= $item[$data_type . "_id"]; ?>" class="list__item"<?= $selected ?>>
          <input data-action="select" type="button" value="<?= $item['name']; ?>">
          <button class="action-button" data-action="delete">-</button>
        </li>
      <?php endforeach; ?>
    </ul>
    <template id="item">
      <li data-id="" class="list__item">
        <input data-action="select" type="button" value="">
        <button class="action-button" data-action="delete">-</button>
      </li>
    </template>
</section>