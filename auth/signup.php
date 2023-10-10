<?php
require_once "../database/inti.php";
session_start();
if (isset($_SESSION["error"])){
    echo  $_SESSION["error"] ;
    unset($_SESSION["error"]);
}
if ($_SERVER["REQUEST_METHOD"]==="POST" && !empty($_POST["signup"])){
    $username= $_POST["username"];
    $password= $_POST["password"];
    $repassword = $_POST["repassword"];
    if (empty($username))
        return  $_SESSION["error"] ="username is empty";
    if (empty($password))
        return  $_SESSION["error"] ="password is empty";
    if (empty($repassword))
        return  $_SESSION["error"] ="repassword is empty";
    if (strlen($username)>30)
        return  $_SESSION["error"] ="username longer then 30char";
    if ($password!==$repassword)
        return  $_SESSION["error"] ="password and repassword are not same";
    if (strlen($password)<6)
        return  $_SESSION["error"] ="password shorter then 6char";
    try{
        $stmt=$db->prepare("select * from users where username = :username limit 1");
        $stmt->execute([":username"=>$username]);
        $user=$stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        die($e->getMessage());
    }
    if (isset($user))
        return  $_SESSION["error"] ="someone used this email";
    $hashed_password = password_hash($password,"2y");
    try{
        $stmt=$db->prepare("select uuid()");
        $stmt->execute();
        $id=$stmt->fetch(PDO::FETCH_ASSOC)["uuid()"];

        $stmt=$db->prepare("insert into users(id,username,password) values(:id,:username,:password)");
        $stmt->execute([":id"=>$id,":username"=>$username,":password"=>$hashed_password]);
    }catch(PDOException $e){
        die($e->getMessage());
    }
    $_SESSION["error"]="created ";
    $_SESSION['id']=$id;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
</head>
<body>
    <form action="signup.php" method="post">
        <input type="text" name="username" id="username">
        <input type="password" name="password" id="password">
        <input type="password" name="repassword" id="repassword">
        <input type="submit" name="signup" value="signup">
    </form>
</body>
</html>