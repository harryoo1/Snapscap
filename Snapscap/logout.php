<?php
$active_user = $_COOKIE["user_name"];
include ("includes/db_connection.php");
$query = "UPDATE `users_online` SET `status`= 'offline' WHERE `user_name` = '$active_user'";
$result = mysqli_query($sqlcon,$query);
session_start();
session_destroy();
header ("location: login.php");
?>