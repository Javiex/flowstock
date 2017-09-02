<?php
class Product {

  public function getProducts() {
    // $query="SELECT * FROM `product`";
    $query="SELECT p.*, l.name_line, f.name_family, u.code_unit, b.name_brand FROM `product` p
    INNER JOIN line l ON l.id_line = p.line
    INNER JOIN family f ON f.id_family = p.family
    INNER JOIN unit u ON u.id_unit = p.unit
    INNER JOIN brand b ON b.code_brand = p.code_brand";
    $response= getData($query, 'all');
    return $response;
  }

  //list
  public function getProductByElemento($search) {
    $query="SELECT * FROM `product`
    WHERE `code_product` LIKE '%$search%'
    OR `description_product` LIKE '%$search%'";
    $response= getData($query, 'all');
    return $response;
  }
  //register
  public function setProduct($code, $line, $family, $gender, $unit, $description, $codeBrand){
    $query = "INSERT INTO product VALUES (null, ?,?,?,?,?,?,?)";
    $params = [$code, $line, $family, $gender, $unit, $description, $codeBrand];

    $id = newPostData($query, $params, "insert");

    return $id;
  }
  //update codeProduct
  public function updateCodeProduct($code, $producto_id){
    $query = "UPDATE `product` SET `code_product`=? WHERE `id_product` = ?";
    $params = [$code,$producto_id];

    $rc = newPostData($query, $params, "update");
    return $rc;
  }
  //update
  public function updateProduct($code, $line, $family, $gender, $unit, $description, $codeBrand, $id_product){
    $query="UPDATE `product` SET
    `code_product`=$code,
    `line`=$line,
    `family`=$family,
    `gender`=$gender,
    `unit`=$unit,
    `description_product`=$description,
    `code_brand`=$codeBrand
    WHERE `id_product` = $id_product";

    $row = postData($query, 'update');
    return $row;
    // $con= getConnection();
    // $dbh= $con->prepare($query);
    // $dbh->execute();
  }

  /*******Tabla productDetail********/
  //list
  public function getProductDetail($id_product){
    $query="SELECT * FROM `product_detail`
    WHERE `id_product`=$id_product";
    $response= getData($query, 'all');
    return $response;
  }
  //register
  public function setProductDetail(){
    $query="INSERT INTO `product_detail`(
      `id_productDetail`,
      `id_product`,
      `model`,
      `color`,
      `size`,
      `sku`)
      VALUES (
        NULL,
        $id_product,
        $model,
        $color,
        $size,
        $sku)";

        $id = postData($query, 'insert');
        return $id;
        // $con= getConnection();
        // $dbh= $con->prepare($query);
        // $dbh->execute();
      }

    }
    ?>
