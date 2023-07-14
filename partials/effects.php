<div>
  <h2>Effects</h2>
  <?php if (count($effects) > 0) : ?>
    <ul>
      <?php foreach ($effects as $effect) : ?>
        <li><?= $effect->name; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>