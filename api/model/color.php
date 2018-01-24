<?php
class Color {

  public function getColors() {
    $query = "SELECT * FROM `color`";
    $response = getData($query, 'all');
    return $response;
  }

  public function setColor($codeColor, $nameColor) {
    $query = "INSERT INTO color VALUES (null,?,?)";
    $params = [$codeColor, $nameColor];
    $id = newPostData($query, $params, "insert");
    return $id;
  }

}
?>
