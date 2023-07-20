<?php
define('TYPE', [
  'music' => 1,
  'effect' => 2
]);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class DB {
  public $pdo;

  function __construct() {
    $dbfile = $_SERVER['SERVER_NAME'] == "localhost" ? '/dmplayer.db' : '/dmplayer-dev.db';
    $this->pdo = new PDO("sqlite:" . $_SERVER['DOCUMENT_ROOT'] . $dbfile);
  }

  // Theme
  // Create
  public function create_theme($name) {
    $query = "INSERT INTO theme (name) VALUES(:name)";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindValue('name', $name);
    $stmt->execute();
    return $this->pdo->lastInsertId();
  }
  // Read
  public function get_themes() {
    $query = $this->pdo->query("SELECT theme_id, name FROM theme");
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
  public function update_theme($theme_id, $newName) {
    $sql = "UPDATE theme SET name = :new_name WHERE theme_id = :theme_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->bindValue(':new_name', $newName);
    $stmt->execute();
  }
  public function update_last_theme($id) {
    $sql = "UPDATE settings SET value = :id WHERE option = \"last_theme\"";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
  }
  // Delete
  public function delete_theme($id) {
    $this->pdo->query("DELETE FROM theme_track WHERE theme_id = $id;");
    $this->pdo->query("DELETE FROM theme_preset WHERE theme_id = $id;");
    $this->pdo->query("DELETE FROM theme WHERE theme_id = $id;");
    return ["message" => $id . " is deleted"];
  }

  // Preset
  // Create
  public function create_preset($name, $theme_id, $current = 0) {
    // create preset
    $sql = "INSERT INTO preset (name) VALUES(:name)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->execute();
    $preset_id = $this->pdo->lastInsertId();

    // add preset to theme
    $sql = "INSERT INTO theme_preset (theme_id, preset_id, current) VALUES(:theme_id, :preset_id, :current)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->bindValue(':preset_id', $preset_id);
    $stmt->bindValue(':current', $current);
    $stmt->execute();

    return $preset_id;
  }
  // Read
  public function get_presets_by_theme($id) {
    $query = $this->pdo->prepare("
      SELECT preset_id, name, current
      FROM theme_preset
      INNER JOIN preset USING(preset_id)
      WHERE theme_id = :theme_id
    ");
    $query->bindValue(':theme_id', $id);
    $query->execute();
    $results = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $results[] = $row;
    }
    return $results;
  }
  public function get_preset_track_settings($preset_id, $track_id) {
    $sql = "SELECT playing, volume
            FROM preset_track
            WHERE preset_id = :preset_id AND track_id = :track_id;";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':preset_id', $preset_id);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function get_last_active_preset($theme_id) {
    $query = $this->pdo->prepare("
      SELECT preset_id
      FROM theme_preset
      WHERE theme_id = :theme_id
      AND current = 1
    ");
    $query->bindValue(":theme_id", $theme_id);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_NUM);
    if (is_array($result) && array_key_exists(0, $result)) {
      return $result[0];
    } else {
      return false;
    }

  }

  // Update
  public function update_preset($preset_id, $newName) {
    $sql = "UPDATE preset SET name = :new_name WHERE preset_id = :theme_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $preset_id);
    $stmt->bindValue(':new_name', $newName);
    $stmt->execute();
  }

  public function update_last_preset($preset_id, $theme_id) {
    // remove old
    $sql = "
      UPDATE theme_preset
      SET current = 0
      WHERE current = 1
      AND theme_id = :theme_id
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->execute();

    // set new
    $sql = "
      UPDATE theme_preset
      SET current = 1
      WHERE theme_id = :theme_id
      AND preset_id = :preset_id
      ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->bindValue(':preset_id', $preset_id);
    $stmt->execute();

    $sql = "
      SELECT preset_id
      FROM theme_preset
      ORDER BY preset_id
      DESC LIMIT 1;
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetch();
  }
  public function update_preset_track_volume($preset_id, $track_id, $volume) {
    $sql = "UPDATE preset_track
            SET volume = :volume
            WHERE preset_id = :preset_id AND track_id = :track_id;";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':preset_id', $preset_id);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->bindValue(':volume', $volume);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  public function update_preset_track_play_status($preset_id, $track_id, $playing) {
    $sql = "UPDATE preset_track
            SET playing = :playing
            WHERE preset_id = :preset_id AND track_id = :track_id;";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':preset_id', $preset_id);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->bindValue(':playing', $playing);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
  // Delete
  public function delete_preset($id) {
    $this->pdo->query("DELETE FROM theme_preset WHERE preset_id = $id;");
    $this->pdo->query("DELETE FROM preset WHERE preset_id = $id;");

    return ["message" => $id . " is deleted"];
  }
  public function delete_presets_by_theme($id) {
    $presets = $this->get_presets_by_theme($id);
    foreach ($presets as $preset) {
      $this->delete_preset($preset['preset_id']);
    }
  }

  // Track
  // Create
  public function create_track($name, $theme_id, $type_id) {
    // create track
    $sql = "INSERT INTO track (name, type_id) VALUES(:name, :type_id)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':type_id', $type_id);
    $stmt->execute();
    $track_id = $this->pdo->lastInsertId();

    // add track to theme
    $sql = "INSERT INTO theme_track (theme_id, track_id) VALUES(:theme_id, :track_id)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->execute();

    return $track_id;
  }
  // Read
  private function get_tracks_by_theme($theme_id, $type_id) {
    $query = $this->pdo->prepare("
      SELECT *
      FROM theme_track
      INNER JOIN track USING (track_id)
      WHERE theme_id = :theme_id
      AND type_id = :type_id
    ");
    $query->bindValue(':theme_id', $theme_id);
    $query->bindValue(':type_id', $type_id);
    $query->execute();
    $results = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $results[] = $row;
    }
    return $results;
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
  public function delete_track($id) {
    $this->pdo->query("DELETE FROM theme_track WHERE track_id = $id");
    $this->pdo->query("DELETE FROM track_file WHERE track_id = $id");
    $this->pdo->query("DELETE FROM track WHERE track_id = $id");

    return ["message" => $id . " is deleted"];
  }

  // File
  // Create
  public function create_file($filename, $track_id) {
    // Add to file table
    $sql = "INSERT INTO file(filename) VALUES(:filename)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':filename', $filename);
    $stmt->execute();
    $file_id = $this->pdo->lastInsertId();

    // add to file track table
    $sql = "INSERT INTO track_file(track_id, file_id) VALUES(:track_id, :file_id)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->bindValue(':file_id', $file_id);
    $stmt->execute();
    return $file_id;
  }
  // Read
  public function get_files_by_track($track_id) {
    $query = $this->pdo->prepare("
      SELECT file_id, filename
      FROM file
      INNER JOIN track_file USING(file_id)
      WHERE track_id = :track_id
    ");
    $query->bindValue(':track_id', $track_id);
    $query->execute();
    $results = [];
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
      $results[] = $row;
    }
    return $results;
  }
  public function get_files_except_by_track($track_id) {
    $query = $this->pdo->prepare("
      SELECT filename
      FROM file
      INNER JOIN track_file USING(file_id)
      WHERE track_id NOT LIKE :track_id
    ");
    $query->bindValue(':track_id', $track_id);
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
  public function delete_file($id) {
    $this->pdo->query("DELETE FROM file WHERE file_id = $id;");
    $this->pdo->query("DELETE FROM track_file WHERE file_id = $id;");
  }

  public function get_media_folder() {
    return $this->get_setting_by_name('media_folder');
  }

  // Settings
  private function get_setting_by_name($option) {
    $query = $this->pdo->query("SELECT value FROM settings WHERE option = \"$option\"");
    $query->execute();
    return $query->fetch()['value'];
  }

}