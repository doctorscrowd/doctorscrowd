<?php
class Security {
  static function text($text) {
    return htmlspecialchars($text);
  }
  
  static function coalesce($array, $key) {
    if (!isset($array[$key])) {
      return NULL;
    }
    
    return Security::text($array[$key]);
  }
}
?>
