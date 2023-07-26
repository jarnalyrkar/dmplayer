<div>
  <form action="" method="GET" class="add-form">
    <label class="add-form__label" for="new-name">Add <?= $data_type ?></label>
    <div class="add-form__row">
      <input class="add-form__input" required autocomplete="off" placeholder="<?= $placeholder; ?>" type="text" id="new-name">
      <button type="submit" class="action-button" id="add-new" title="Add new <?= $data_type ?>">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/plus.svg"; ?>
      </button>
    </div>
  </form>
</div>