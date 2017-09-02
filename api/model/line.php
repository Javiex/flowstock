<?php
class Line {

  //list
  public function getLines() {
    $query="SELECT * FROM `line`";
    $response = getData($query, 'all');
    return $response;
  }

  //Insert
  public function setLine(){
    $query="";
    $id = postData($query, 'insert');
    return $id;
  }

}

?>
