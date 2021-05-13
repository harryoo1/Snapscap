<?php
$selected_user_name = $_COOKIE["user_name"];
include ("includes/db_connection.php");
$fetch_query = "SELECT * FROM `users` WHERE `u_name` = '$selected_user_name'";
$result = mysqli_query($sqlcon,$fetch_query);
while($row = mysqli_fetch_assoc($result))
   {
if($row["profile_pic"] == "")
  {
    echo "<img class='my_profile_pic' src='profile_pictures/default.png' title='Click to change profile picture'/>";
  }
    else
      {
        $picture_holder = $row["profile_pic"];
        echo "<img class='my_profile_pic' src='profile_pictures/$picture_holder' title='Click to change profile picture'/>";
      }
   }
mysqli_close($sqlcon);
?>

