<?php
class Family{

  public function getFamilies() {
    $query="SELECT * FROM `family`";
    $response = getData($query, 'all');
    return $response;
  }

}
?>
