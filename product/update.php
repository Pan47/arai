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
    
    if (empty($_GET["id"]))
        return $_SESSION["error"] ="404";
    $product_id = $_GET["id"];
    $update = array("name"=>true,"price"=>true,"stock"=>true,"image"=>true);
    if (empty($product_name))
        $update["name"]=false;
    if (empty($price))
        $update["price"]=false;
    if (empty($stock))
        $update["stock"]=false;
    if (empty($image))
        $update["image"]=false;
    try{
        $stmt=$db->prepare("select * from products where id =:id");
        $stmt->execute(["id"=>$product_id]);
        $product =$stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        die($e->getMessage());
    }
    
    try{
        if ($update["name"]){
            $stmt=$db->prepare("update products set productname =:productname where id =:id ");
            $stmt->execute([":id"=>$product_id ,":productname"=>$product_name]);
        }
        if ($update["price"]){
            $stmt=$db->prepare("update products set price =:price where id =:id ");
            $stmt->execute([":id"=>$product_id ,":price"=>$price]);
        }
        if ($update["stock"]){
            $stmt=$db->prepare("update products set stock =:stock where id =:id ");
            $stmt->execute([":id"=>$product_id ,":stock"=>$stock]);
        }
    }catch(PDOException $e){
        die("failed :".$e->getMessage());
    }
    if (!$update["image"]){
        unlink( $product["image"]);
        savefile($image, $product_id);
    }
    $_SESSION["error"]="created";

}
function savefile(array $image, string $product_id)  {
    $check=getimagesize($image["tmp_name"]);
    if (!str_starts_with($check["mime"],"image"))
        return  $_SESSION["error"] ="this file is allow";
    if ($image["size"] >50000)
        return  $_SESSION["error"] ="this file is large 5MB";
    $upload="C:/xampp/htdocs/image/upload/".$product_id.".".explode("image/",$check["mime"])[1];
    move_uploaded_file($image["tmp_name"],$upload);
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
    <form action=<?php echo "update.php?id=".$_GET["id"] ?> method="post" enctype="multipart/form-data">
        <input type="text" name="name" id="name">
        <input type="number" name="price" id="price">
        <input type="number" name="stock" id="stock">
        <input type="file" name="image" id="image">
        <input type="submit" name="create" value="create">
    </form>
</body>
</html>