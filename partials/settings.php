<div id="settings" class="dialog">
  <div class="dialog__outer">
    <div class="dialog__inner">
      <button data-action="close-dialog" aria-label="close dialog" title="Close settings">&times;</button>
      <h2>Settings</h2>
      <div class="settings-grid">
        <div class="colorpickers">
          <h3>Theme</h3>
          <div class="colorpickers-list">
            <article>
              <div id="primary-color" class="circle" style="background-color: <?= $db->HSLToRGB($primary_color); ?>"></div>
              <input type="hidden" id="primary-value" value="<?= $db->HSLToRGB($primary_color); ?>">
              <h4>Primary color</h3>
            </article>
            <article>
              <div id="accent-color" class="circle" style="background-color: <?= $db->HSLToRGB($accent_color); ?>"></div>
              <input type="hidden" id="accent-value" value="<?= $db->HSLToRGB($accent_color); ?>">
              <h4>Accent color</h3>
            </article>
            <article>
              <div id="text-color" class="circle" style="background-color: <?= $db->HSLToRGB($text_color); ?>"></div>
              <input type="hidden" id="text-value" value="<?= $db->HSLToRGB($text_color); ?>">
              <h4>Text color</h3>
            </article>
          </div>
          <button data-action="reset-theme">Reset theme</button>
        </div>
        <div class="file-persistance">
          <h3>Deleting files</h3>
          <input type="checkbox" id="delete-files">
          <label for="delete-files">Delete files on disk?</label>
          <p class="explain">
            This affects files in the dmplayer/audio directory when deleting themes, presets, tracks or effects.
          </p>
        </div>
        <div class="background-image">
          <h3>Background image</h3>
          <label for="bg-img-url">Background image URL</label>
          <input id="bg-img-url" type="text" value="<?= $background_image ?>">
          <p class="explain">Tip: You can also move an image into the app directory and rename it &ldquo;bg.jpeg&rdquo;, and it will be used, if no URL is present.</p>
        </div>
      </div>
    </div>
  </div>
</div>