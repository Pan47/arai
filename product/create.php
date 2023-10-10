<?php
require_once "../database/inti.php";
session_start();
if (isset($_SESSION["error"])){
    echo  $_SESSION["error"] ;
    unset($_SESSION["error"]);
}
if ($_SERVER["REQUEST_METHOD"]==="POST" && $_POST["create"]){
    $id=$_SESSION["id"];
    //check is admin
    if (empty($id))
    return  $_SESSION["error"] ="sign in!";
    try{
        $stmt=$db->prepare("select is_admin from users where id = :id");
        $stmt->execute([":id"=>$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        die($e->getMessage());
    }
    if (empty($user)){
        $_SESSION["error"] ="user is empty";
        unset($_SESSION);
        return ;
    }
    if (!$user["is_admin"])
        return $_SESSION["error"] ="you are not admin";
    //check input
    $product_name = $_POST["name"];
    $price = $_POST["price"];
    $stock =$_POST["stock"];
    $image = $_FILES["image"];
    if (empty($product_name))
        return  $_SESSION["error"] ="product_name is empty";
    if (empty($price))
        return  $_SESSION["error"] ="price is empty";
    if (empty($stock))
        return  $_SESSION["error"] ="stock is empty";
    if (empty($image))
        return  $_SESSION["error"] ="image is empty";
    try{
        $stmt=$db->prepare("select uuid()");
        $stmt->execute();
        $product_id =$stmt->fetch(PDO::FETCH_ASSOC)["uuid()"];
    }catch(PDOException $e){
        die($e->getMessage());
    }
    $check=getimagesize($image["tmp_name"]);
    if (!str_starts_with($check["mime"],"image"))
        return  $_SESSION["error"] ="this file is allow";
    if ($image["size"] >50000)
        return  $_SESSION["error"] ="this file is large 5MB";
    $upload="C:/xampp/htdocs/image/upload/".$product_id.".".explode("image/",$check["mime"])[1];
    move_uploaded_file($image["tmp_name"],$upload);
    try{
        $stmt=$db->prepare("insert into products(id,productname,price,stock,image) values(:id,:productname,:price,:stock,:image)");
        $stmt->execute([":id"=>$product_id ,":productname"=>$product_name,":price"=>$price,":stock"=>$stock,":image"=>$upload]);
        
    }catch(PDOException $e){
        die($e->getMessage());
    }

    $_SESSION["error"]="created";

}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>create</title>
</head>
<body>
    <form action="create.php" method="post" enctype="multipart/form-data">
        <input type="text" name="name" id="name">
        <input type="number" name="price" id="price">
        <input type="number" name="stock" id="stock">
        <input type="file" name="image" id="image">
        <input type="submit" name="create" value="create">
    </form>
</body>
</html>