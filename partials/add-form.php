<div>
  <form action="" method="GET" class="add-form">
    <label class="add-form__label" for="new-name">Create new <?= $data_type ?></label>
    <input class="add-form__input" required autocomplete="off" placeholder="<?= $placeholder; ?>" type="text" id="new-name">
    <button type="submit" class="action-button" id="add-new" title="Add new <?= $data_type ?>">
      <?php include $_SERVER['DOCUMENT_ROOT'] . "/assets/img/plus.svg"; ?>
    </button>
  </form>
</div>