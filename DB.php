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
    $dbfile = '/dmplayer.db';
    $this->pdo = new PDO("sqlite:" . $_SERVER['DOCUMENT_ROOT'] . $dbfile);
  }

  public function create_theme($name, $order) {
    $sql = "INSERT INTO theme (name, \"order\") VALUES(:name, :order)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue('name', $name);
    $stmt->bindValue('order', $order);
    $stmt->execute();

    // Get last created item
    return $this->get_theme($this->pdo->lastInsertId());
  }

  public function get_themes() {
    $query = $this->pdo->query("
      SELECT *
      FROM theme
      ORDER BY \"order\" ASC
    ");
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }

  public function get_theme($id) {
    $query = $this->pdo->query("
      SELECT *
      FROM theme
      WHERE theme_id = \"$id\"
    ");
    return $query->fetch(PDO::FETCH_ASSOC);
  }

  public function get_last_active_theme() {
    return $this->get_setting_by_name('last_theme');
  }

  public function update_theme($theme_id, $newName) {
    $sql = "UPDATE theme SET name = :new_name WHERE theme_id = :theme_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->bindValue(':new_name', $newName);
    $stmt->execute();
    return $this->get_theme($theme_id);
  }

  public function update_last_theme($id) {
    $sql = "UPDATE settings SET value = :id WHERE option = \"last_theme\"";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
  }

  public function update_theme_order($id, $order) {
    $sql = "UPDATE theme SET \"order\" = :order WHERE theme_id = :theme_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $id);
    $stmt->bindValue(':order', $order);
    $stmt->execute();
  }

  public function delete_theme($id) {

    // Get files associated with tracks associated with theme
    $music = $this->get_tracks_by_theme($id, TYPE['music']);
    $effects = $this->get_tracks_by_theme($id, TYPE['effect']);
    $tracks = array_merge($music, $effects); // All tracks for this theme

    $track_ids = array_column($tracks, 'track_id'); // Ids only
    foreach ($tracks as $track) { // For alle tracks i vårt theme
      $files = $this->get_files_by_track($track['track_id']);
      foreach ($files as $file) {
        $file_id = $file['file_id'];
        $query = $this->pdo->query("SELECT * FROM track_file WHERE file_id = \"$file_id\"");
        $files_in_track = $query->fetchAll(PDO::FETCH_ASSOC);

        $delete_file = true;
        foreach($files_in_track as $track_file) {
          if (!in_array($track_file['track_id'], $track_ids)) {
            $delete_file = false;
            break;
          }
        }
        if ($delete_file) {
          $this->delete_file($file_id);
          unlink($_SERVER['DOCUMENT_ROOT'] . $this->get_media_folder() . $file['filename']);
        }
      }
      $this->delete_track($track['track_id']);
    }

    // Slett alle relaterte presets
    $presets = $this->get_presets_by_theme($id);
    foreach($presets as $preset) {
      $preset_id = $preset['preset_id'];
      $this->delete_preset($preset_id);
      $query = $this->pdo->query("DELETE FROM preset_track WHERE preset_id = $preset_id;");
      $query->execute();
    }
    // Slett alle koblinger
    $query = $this->pdo->query("DELETE FROM theme_track WHERE theme_id = $id;");
    $query->execute();
    $query = $this->pdo->query("DELETE FROM theme_preset WHERE theme_id = $id;");
    $query->execute();
    $query = $this->pdo->query("DELETE FROM theme WHERE theme_id = $id;");
    $query->execute();
    return;
  }

  public function create_preset($name, $theme_id, $order, $current = 0) {
    $sql = "INSERT INTO preset (name) VALUES(:name)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->execute();
    $preset_id = $this->pdo->lastInsertId();

    // add preset to theme
    $sql = "INSERT INTO theme_preset (theme_id, preset_id, current, \"order\") VALUES(:theme_id, :preset_id, :current, :order)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->bindValue(':preset_id', $preset_id);
    $stmt->bindValue(':current', $current);
    $stmt->bindValue(':order', $order);
    $stmt->execute();

    return $preset_id;
  }

  public function get_presets_by_theme($id) {
    $query = $this->pdo->prepare("
      SELECT preset_id, name, current
      FROM theme_preset
      INNER JOIN preset USING(preset_id)
      WHERE theme_id = :theme_id
      ORDER BY \"order\"
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
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    return $data;
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

  public function update_preset_theme_order($preset_id, $theme_id, $order) {
    $sql = "UPDATE theme_preset SET \"order\" = :order WHERE preset_id = :preset_id AND theme_id = :theme_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':order', $order);
    $stmt->bindValue(':preset_id', $preset_id);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->execute();
  }

  public function delete_preset($id) {
    $query = $this->pdo->query("DELETE FROM theme_preset WHERE preset_id = \"$id\";");
    $query->execute();
    $query = $this->pdo->query("DELETE FROM preset WHERE preset_id = \"$id\";");
    $query->execute();

    $this->delete_tracks_by_preset($id);
    return;
  }

  public function delete_presets_by_theme($id) {
    $presets = $this->get_presets_by_theme($id);
    foreach ($presets as $preset) {
      $this->delete_preset($preset['preset_id']);
    }
    return;
  }

  public function create_track($name, $theme_id, $type_id, $order, $preset_id = null) {
    $sql = "INSERT INTO track (name, type_id) VALUES(:name, :type_id)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':type_id', $type_id);
    $stmt->execute();
    $track = $this->get_track($this->pdo->lastInsertId());

    // add track to theme
    $sql = "INSERT INTO theme_track (theme_id, track_id, \"order\") VALUES(:theme_id, :track_id, :order)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->bindValue(':track_id', $track['track_id']);
    $stmt->bindValue(':order', $order);
    $stmt->execute();

    if ($track['type_id'] === TYPE['music']) {
      $this->add_track_to_preset($track['track_id'], $preset_id);
    }

    return $track;
  }

  public function add_track_to_preset($track_id, $preset_id) {
    $sql = "INSERT INTO preset_track (track_id, preset_id, playing, volume) VALUES(:track_id, :preset_id, 0, 75)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->bindValue(':preset_id', $preset_id);
    $stmt->execute();
  }

  public function get_track($track_id) {
    $query = $this->pdo->query("SELECT * FROM track WHERE track_id = \"$track_id\"");
    return $query->fetch(PDO::FETCH_ASSOC);
  }

  public function get_track_preset($track_id, $preset_id) {
    $sql = $this->pdo->query("SELECT * FROM preset_track WHERE track_id = $track_id AND preset_id = $preset_id");
    return $sql->fetch(PDO::FETCH_ASSOC);
  }

  private function get_tracks_by_theme($theme_id, $type_id) {
    $query = $this->pdo->prepare("
      SELECT *
      FROM theme_track
      INNER JOIN track USING (track_id)
      WHERE theme_id = :theme_id
      AND type_id = :type_id
      ORDER BY \"order\"
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

  public function update_track($track_id, $newName) {
    $sql = "UPDATE track SET name = :new_name WHERE track_id = :track_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->bindValue(':new_name', $newName);
    $stmt->execute();
  }

  public function update_theme_track_order($track_id, $theme_id, $order) {
    $sql = "UPDATE theme_track SET \"order\" = :order WHERE track_id = :track_id AND theme_id = :theme_id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':order', $order);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->bindValue(':theme_id', $theme_id);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function delete_track($id) {
    $query = $this->pdo->query("DELETE FROM preset_track WHERE track_id = $id");
    $query->execute();
    $query = $this->pdo->query("DELETE FROM theme_track WHERE track_id = $id");
    $query->execute();
    $query = $this->pdo->query("DELETE FROM track WHERE track_id = $id");
    $query->execute();
    return;
  }

  public function delete_tracks_by_preset($id) {
    $sql = $this->pdo->query("DELETE FROM theme_preset WHERE preset_id = $id");
    $sql->execute();
    $sql = $this->pdo->query("DELETE FROM preset WHERE preset_id = $id");
    $sql->execute();
    return;
  }

  public function create_file($filename, $track_id) {
    $sql = "INSERT INTO file(filename) VALUES(:filename)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':filename', $filename);
    $stmt->execute();
    $file_id = $this->pdo->lastInsertId();
    $this->add_file_to_track($file_id, $track_id);
    return $file_id;
  }

  public function add_file_to_track($file_id, $track_id) {
    // add to file track table
    $sql = "INSERT INTO track_file(track_id, file_id) VALUES(:track_id, :file_id)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':track_id', $track_id);
    $stmt->bindValue(':file_id', $file_id);
    $stmt->execute();
  }

  public function get_file_by_name($name) {
    $query = $this->pdo->query("SELECT file_id FROM file WHERE filename LIKE \"$name\"");
    return $query->fetch(PDO::FETCH_ASSOC)['file_id'] ?? false;
  }

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

  public function delete_file($id) {
    $this->pdo->query("DELETE FROM file WHERE file_id = $id;");
  }

  public function get_media_folder() {
    return $this->get_setting_by_name('media_folder');
  }

  public function get_last_effect_volume() {
    return $this->get_setting_by_name('effect_volume');
  }

  public function set_last_effect_volume($volume) {
    $sql = "UPDATE settings SET value = :volume WHERE option = \"effect_volume\"";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':volume', $volume);
    $stmt->execute();
  }

  public function set_background_image($url) {
    $query = $this->pdo->query("UPDATE settings SET value = \"$url\" WHERE option = \"background_image\"");
    return $query->fetch();
  }

  public function get_background_image() {
    return $this->get_setting_by_name('background_image');
  }

  public function set_primary_color($color) {
    $query = $this->pdo->query("UPDATE settings SET value = \"$color\" WHERE option = \"primary_color\"");
    return $query->fetch();
  }

  public function set_accent_color($color) {
    $query = $this->pdo->query("UPDATE settings SET value = \"$color\" WHERE option = \"accent_color\"");
    return $query->fetch();
  }

  public function set_text_color($color) {
    $query = $this->pdo->query("UPDATE settings SET value = \"$color\" WHERE option = \"text_color\"");
    return $query->fetch();
  }

  public function get_shades() {
    $primary = $this->get_primary_color();
    if (!$primary) return;
    $primaryValues = explode(',', substr(str_replace('%', '', $primary), 4, -1));
    $p1 = $primaryValues[2] + 20;
    $p2 = $primaryValues[2] + 10;
    $p3 = $primaryValues[2];
    $p4 = $primaryValues[2] - 5;
    $p5 = $primaryValues[2] - 10;
    $p6 = $primaryValues[2] - 15;
    return [
      "hsl({$primaryValues[0]}, {$primaryValues[1]}%, {$p1}%)",
      "hsl({$primaryValues[0]}, {$primaryValues[1]}%, {$p2}%)",
      "hsl({$primaryValues[0]}, {$primaryValues[1]}%, {$p3}%)",
      "hsl({$primaryValues[0]}, {$primaryValues[1]}%, {$p4}%)",
      "hsla({$primaryValues[0]}, {$primaryValues[1]}%, {$p4}%, 70%)",
      "hsl({$primaryValues[0]}, {$primaryValues[1]}%, {$p5}%)",
      "hsl({$primaryValues[0]}, {$primaryValues[1]}%, {$p6}%)",
      "hsla({$primaryValues[0]}, {$primaryValues[1]}%, {$p6}%, 70%)",
    ];
  }

  public function get_primary_color() {
    return $this->get_setting_by_name('primary_color');
  }

  public function get_accent_color() {
    return $this->get_setting_by_name('accent_color');
  }

  public function get_text_color() {
    return $this->get_setting_by_name('text_color');
  }

  private function get_setting_by_name($option) {
    $query = $this->pdo->query("SELECT value FROM settings WHERE option = \"$option\"");
    $query->execute();
    return $query->fetch()['value'];
  }

  public function reset_theme() {
    $this->set_primary_color("hsl(25, 56%, 25%)");
    $this->set_accent_color("hsl(43, 74%, 49%)");
    $this->set_text_color("hsl(0,0%,100%)");
  }

  public function HSLToRGB($hsl) {
    $values = explode(',', substr(str_replace('%', '', $hsl), 4, -1));
    $hue = intval($values[0]);
    $sat = intval($values[1]);
    $val = intval($values[2]);

    $rgb = array(0, 0, 0);
    //calc rgb for 100% SV, go +1 for BR-range
    for ($i = 0; $i < 4; $i++) {
      if (abs($hue - $i * 120) < 120) {
        $distance = max(60, abs($hue - $i * 120));
        $rgb[$i % 3] = 1 - (($distance - 60) / 60);
      }
    }
    //desaturate by increasing lower levels
    $max = max($rgb);
    $factor = 255 * ($val / 100);
    for ($i = 0; $i < 3; $i++) {
      //use distance between 0 and max (1) and multiply with value
      $rgb[$i] = round(($rgb[$i] + ($max - $rgb[$i]) * (1 - $sat / 100)) * $factor);
    }
    // $rgb['html'] = sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    return "rgb($rgb[0], $rgb[1], $rgb[2])";
  }

}