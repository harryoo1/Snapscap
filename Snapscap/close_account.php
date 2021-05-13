<?php
include("includes/header.php");

if(isset($_POST['cancel'])){
    header("location: settings.php");
}

if(isset($_POST['close_account'])){
    $close_query = mysqli_query($sqlcon, "UPDATE `users` SET `status`= 'closed' WHERE `u_name`= '$user_loggedin'");
    session_destroy();
    header("location: login.php");
}
?>
<div class="container">
    <div style="background-color: #fff; padding: 10px; border: 1px solid #dcdcdc; border-radius: 5px; box-shadow: 4px 8px 16px 0px rgba(0, 0, 0, 0.25);">
        <h5><i>Close Account</i></h5>
        <i style="font-size: 14px; font-family: sans-serif; color: #000;">
            <b>Are you sure you want to close your account?</b><br>
            "Closing your account will hide your profile and all your activities from other users. You can Re-open your account at any time by simply loggig in.."<br><br>
        </i>
        <form action="close_account.php" method="post">
            <input type="submit" name="close_account" id="close_account" value="yes! close it!" class="btn btn-outline-success" style="margin-right: 20px;">
            <input type="submit" name="cancel" id="cancel" value="Cancel" class="btn btn-outline-danger">
        </form>
    </div>
</div>
