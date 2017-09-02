<?php
class Gender{

  public function getGenders() {
    $query="SELECT * FROM `gender`";
    $response = getData($query, 'all');
    return $response;
  }
  
}
?>
