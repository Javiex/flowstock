<?php
class Model {

  public function getModels() {
    $query = "SELECT * FROM `model`";
    $response = getData($query, 'all');
    return $response;
  }
}

?>
