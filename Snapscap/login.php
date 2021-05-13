<?php
session_start();
$_SESSION['user_name']= "";
$_SESSION['var']= "";
$errors = array();
if(isset($_POST["login"]))
{
    include ("includes/db_connection.php");
    $uname = strip_tags($_POST["user_name"]);
    $uname = str_replace(' ','',$uname);
    $uname = ucfirst($uname);
    $email = strip_tags($_POST["user_email"]);
    $email = str_replace(' ','',$email);
    $pass = $_POST["password"];
    if(empty($uname)){
    array_push($errors,"User name is required !");
    }
    if(empty($email)){
    array_push($errors,"Email is required !");
    }
    if(filter_var($email,FILTER_VALIDATE_EMAIL)){ // Email validity
    $email = filter_var($email,FILTER_VALIDATE_EMAIL);
    }
    else{
    array_push($errors,"Invalid email !");
    }
    if(empty($pass)){
    array_push($errors,"Password is required !");
    }
    if(count($errors) == 0){
    $password = md5($pass);
    $fetch_query = "SELECT * FROM `users` WHERE `u_name` = '$uname' AND `u_email` = '$email 'AND `u_password` = '$password' LIMIT 1";
    $login_result = mysqli_query($sqlcon,$fetch_query);
    $user_validity = mysqli_num_rows($login_result);
    if($user_validity > 0)
    {
    $checking_online_status = "SELECT * FROM `users_online` WHERE `user_name` = '$uname'";
    $result_online_status = mysqli_query($sqlcon,$checking_online_status);
    $online_status_validity = mysqli_num_rows($result_online_status);
    if ($online_status_validity > 0) {
    setcookie("user_name", $uname, time()+ (86400*1));
    $update_online_status = "UPDATE `users_online` SET `status`='online',`Time`='CURRENT_TIMESTAMP' WHERE `user_name` = '$uname'";
    $result_online_status = mysqli_query($sqlcon,$update_online_status);
    if($login_result -> num_rows >0){
    $_SESSION['user_nam'] = $login_result->fetch_assoc();
    header("Location: index.php");
    }
    }
    else
    {
    $insert_online_query = "INSERT INTO `users_online`(`id`, `user_name`, `status`, `Time`) VALUES (NULL,'$uname','online','CURRENT_TIMESTAMP')";
    $result_insert_online = mysqli_query($sqlcon,$insert_online_query);
    $UserID = mysqli_insert_id($sqlcon);
    mysqli_close($sqlcon);
    setcookie("user_name", $uname, time()+ (86400*1));
    if($login_result -> num_rows >0){
    $_SESSION['user_name'] = $login_result->fetch_assoc() ;
    header("Location: index.php");
    }
    }
    $status_query = "SELECT * FROM `users` WHERE `u_name` = '$uname' AND `u_email` = '$email 'AND `status` = 'inactive' LIMIT 1";
    $status_result = mysqli_query($sqlcon,$status_query);
    if(mysqli_num_rows($status_result) ==1){
    $reopen_acc_query = "UPDATE `users` SET `status`= 'active' WHERE `u_name` = '$uname' AND `u_email` = '$email";
    $reopen_acc_result = mysqli_query($sqlcon,$reopen_acc_query);
    }
    }
   else
   {
   $_SESSION['var'] = "Wrong user name or Password";
   }
 }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" type="text/css" href="css/Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>snapscap.com</title>
</head>

<body>
    <div class="container">
        <div class="reg-form" style="margin-top: 8rem;">
            <form action="login.php" method="post" class="form-group">
                <h1 class="text-center" style="color: #dcdcdc; font-size: 30px; font-family: sans-serif;">Login</h1>
                <div style="margin-top: 20px;">
                    <input type="text" id="uname" name="user_name" class="form-control" placeholder="Enter User name">
                    <?php if(in_array("User name is required !", $errors)){echo "<span class='error'>User name is required !</span>";}?>
                </div>
                <div style="margin-top: 20px;">
                    <input type="email" id="email" name="user_email" class="form-control" placeholder="Email">
                    <?php if(in_array("Email is required !", $errors)){echo "<span class='error'>Email is required !</span>";}?>
                    <?php if(in_array("Invalid email !", $errors)){echo "<span class='error'>Invalid email !</span>";}?>
                </div>
                <div style="margin-top: 20px;">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                    <?php if(in_array("Password is required !", $errors)){echo "<span class='error'>Password is required !</span>";}?>
                </div>
                    <a class="nav-link d-block small text-center" href="register.php">Register for new Account</a>
                <div class="form-group">
                    <button type="submit" name="login" class="form-control btn btn-primary" style="margin-top: 15px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
