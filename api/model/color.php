<?php
class Color {

  public function getColors() {
    $query = "SELECT * FROM `color`";
    $response = getData($query, 'all');
    return $response;
  }
  
}
?>
