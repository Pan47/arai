<?php
require_once "../database/inti.php";
session_start();
if (isset($_SESSION["error"])){
    echo  $_SESSION["error"] ;
    unset($_SESSION["error"]);
}
if ($_SERVER["REQUEST_METHOD"]==="POST" && !empty($_POST["signin"])){
    $username= $_POST["username"];
    $password= $_POST["password"];
    if (empty($username))
        return  $_SESSION["error"] ="username is empty";
    if (empty($password))
        return  $_SESSION["error"] ="password is empty";
    try{
        $stmt=$db->prepare("select * from users where username = :username limit 1");
        $stmt->execute([":username"=>$username]);
        $user=$stmt->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $e){
        die($e->getMessage());
    }
    if (empty($user))
        return  $_SESSION["error"] ="username or password went wrong";
    if (!password_verify($password,$user["password"]))
        return  $_SESSION["error"] ="username or password went wrong";
    $_SESSION["error"]="signed in";
    $_SESSION['id']=$user["id"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/auth/signin.css">
    <title>Signup</title>
</head>
<body>
    <form action="signin.php" method="post">
        <input type="text" name="username" id="username">
        <input type="password" name="password" id="password">
        <input type="submit" name="signin" value="signin">
    </form>
</body>
</html>