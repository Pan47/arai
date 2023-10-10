<?php
require_once "database/inti.php";
session_start();
try{
    $stmt= $db->prepare("select productname,image,price,stock,id from products where stock !=0 ");
    $stmt->execute();
    $products=$stmt->fetchAll();
}catch(PDOException $e){
    die($e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
</head>
<body>
<?php 
    foreach ($products as $product) {?>
        <a href="/product/get.php?id=<?php echo $product["id"];?>">
        <div>
            <p><?php echo $product["productname"] ?></p>
        <img src=<?php echo $product["image"];?>>
        </div>
        </a>
        
    <?php
    }

?>
</body>
</html>