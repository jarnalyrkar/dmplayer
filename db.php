<?php
define('TYPE', [['music' => 1], ['effect' => 2]]);

class DB {
  protected $pdo;

  function __construct() {
    $this->pdo = new PDO("sqlite:" . $_SERVER['DOCUMENT_ROOT'] . "/dmplayer.db");
  }

  // Theme
  // Create
  public function create_theme($name) {}
  // Read
  public function get_themes() {
    $query = $this->pdo->query("SELECT name FROM theme");
    $themes = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $themes[] = $row;
    }
    return $themes;
  }
  public function get_last_active_theme() {
    return $this->get_setting_by_name('last_theme');
  }
  // Update
  public function update_theme($id, $newName) {}
  // Delete
  public function delete_theme($id) {}

  // Preset
  // Create
  public function create_preset($name, $theme_id) {}
  // Read
  public function get_presets_by_theme($id) {
    $query = $this->pdo->prepare("
      SELECT name
      FROM theme_preset
      INNER JOIN preset USING(preset_id)
      WHERE theme_id = :theme_id
    ");
    $query->bindValue(':theme_id', 1); // get value during init
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result;
  }
  public function get_last_active_preset() {
    return $this->get_setting_by_name('last_preset');
  }
  // Update
  public function update_preset($id, $newName) {}
  // Delete
  public function delete_preset($id) {}

  // Track
  // Create
  public function create_track($name) {}
  // Read
  private function get_tracks_by_theme($theme_id, $type_id) {
    $query = $this->pdo->prepare("
      SELECT name
      FROM theme_track, track_file
      INNER JOIN track USING (track_id)
      WHERE theme_id = :theme_id
      AND type_id = :type_id
      GROUP BY track_id, type_id
    ");
        // AND type_id = 2 for lydeffekter
        $query->bindValue(':theme_id', $theme_id); // get value during init
        $query->bindValue(':type_id', $type_id); // get value during init
        $query->execute();
        $results = [];
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $results[] = $row;
        }
  }

  public function get_music_by_theme($theme_id) {
    return $this->get_tracks_by_theme($theme_id, TYPE['music']);
  }

  public function get_effects_by_theme($theme_id) {
    return $this->get_tracks_by_theme($theme_id, TYPE['effect']);
  }

  // Update
  public function update_track($id, $newName) {}
  // Delete
  public function delete_track($id) {}

  // File
  // Create
  public function create_file($filename, $track_id) {}
  // Read
  public function get_files_by_track($track_id) {
    $query = $this->pdo->prepare("
      SELECT filename
      FROM file
      INNER JOIN track_file USING(file_id)
      WHERE track_id = :track_id
    ");
    $query->bindValue(':track_id', 1);
    $query->execute();
    $results = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $results[] = $row;
    }
    return $results;
  }
  // Update
  public function update_file($id, $newName) {}
  // Delete
  public function delete_file($id) {}

  // Settings
  private function get_setting_by_name($option) {}

}