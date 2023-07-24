<section id="<?= $data_type ?>">
  <header>
    <?php include $partials . "add-form.php"; ?>
  </header>
  <ul class="list">
    <?php $counter = 1; ?>
    <?php if (count($items) > 0) : ?>
      <?php foreach ($items as $item) :
        $selected = "";
        if ($data_type === "theme") {
          $selected = $item["theme_id"] == $active_theme_id ? ' data-state="selected"' : "";
        } else if ($data_type === "preset") {
          $selected = ($item["preset_id"] == $active_preset_id) ? ' data-state="selected"' : "";
        }
      ?>
        <li data-order="<?= $counter; ?>" draggable="true" data-id="<?= $item[$data_type . "_id"]; ?>" class="list__item" <?= $selected ?>>
          <input data-action="select" type="button" value="<?= $item['name']; ?>">
          <button class="action-button" data-action="delete" title="Delete"><?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/trashcan.svg" ?></button>
        </li>
        <?php $counter++; ?>
      <?php endforeach; ?>
    <?php else : ?>
      <li class="empty">No <?= $data_type; ?>s added yet!</li>
    <?php endif; ?>
  </ul>
  <template id="item">
    <li data-order="" draggable="true" data-id="" class="list__item">
      <input data-action="select" type="button" value="">
      <button class="action-button" data-action="delete"><?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/trashcan.svg" ?></button>
    </li>
  </template>
</section>