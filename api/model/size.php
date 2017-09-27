<?php
class Size {

  public function getBabySizes() {
    $query="SELECT * FROM `size` WHERE `code_size` LIKE '%BB'";
    $response = getData($query, 'all');
    return $response;
  }

  public function getChildSizes() {
    $query="SELECT * FROM `size` WHERE `code_size` LIKE '%T'";
    $response = getData($query, 'all');
    return $response;
  }
}

?>
