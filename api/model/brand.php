<?php
class Brand {

  public function getBrands() {
      $query = "SELECT * FROM `brand`";
      $response = getData($query, 'all');
      return $response;
  }

}
?>
