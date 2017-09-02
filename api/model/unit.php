<?php
class Unit {

  public function getUnits() {
    $query = "SELECT * FROM `unit`";
    $response = getData($query, 'all');
    return $response;
  }
  
}
?>
