<?php
include("includes/header.php");
//include("includes/settings_handler.php");
$errors = array();
if(isset($_POST['update_password'])){
    $old_password = strip_tags($_POST['old_pass']);
    $new_password = strip_tags($_POST['new_pass']);
    $confirm_password = strip_tags($_POST['confirm_pass']);
    $password_query = mysqli_query($sqlcon, "SELECT `u_password` FROM `users` WHERE `u_name`= '$user_loggedin'");
    $row = mysqli_fetch_array($password_query);
    $db_password = $row['u_password'];
    
    if(md5($old_password) == $db_password){
        
        if($new_password == $confirm_password){
            
        if(preg_match('/[^A-Za-z0-9]/', $new_password)){
        array_push($errors,"Password can only contain alphabets and numbers !");    
        }
        if(strlen($new_password) < 8 || strlen($new_password) >15){
        array_push($errors,"Password length should be between 8 to 15 characters !");  
        }
        if(count($errors) == 0){
        $new_password_enc = md5($new_password);
            $pass_update_query = mysqli_query($sqlcon, "UPDATE `users` SET `u_password`= '$new_password_enc' WHERE `u_name`= '$user_loggedin'");
            $password_message = "Password has been changed!";
        }
        else{
            array_push($errors,"New password didn't matched!");
            echo "";
        }
    }
    else{
            array_push($errors,"Old password is incorrect!");  
        }
}
}
?>
<div class="container">
    <div class="details_container">
        <div class="text-center" style="color: #5383d3; font: normal 25px 'Cookie', sans-serif; margin-top: 20px;">Change your Password</div>
        <hr>
        <div id="get_admin_panel">
            <form action="" method="post" class="form-group">
                <div class="form-group reg-form">
                    <div style="margin-top: 30px;">
                        <input type="password" class="form-control" name="old_pass" placeholder="Enter old password">
                        <?php if(in_array("Old password is incorrect!", $errors)){echo "<span class='error'>Old password is incorrect!</span>";}?>
                    </div>
                    <div style="margin-top: 30px;">
                        <input type="password" class="form-control" name="new_pass" placeholder="Enter new password">
                        <?php if(in_array("New password didn't matched!", $errors)){echo "<span class='error'>New password didn't matched!</span>";}?>
                        <?php if(in_array("Password can only contain alphabets and numbers !", $errors)){echo "<span class='error'>Password can only contain alphabets and numbers !</span>";}?>
                        <?php if(in_array("Password length should be between 8 to 15 characters !", $errors)){echo "<span class='error'>Password length should be between 8 to 15 characters !</span>";}?>
                    </div>
                    <div style="margin-top: 30px; margin-bottom: 30px;">
                        <input type="password" class="form-control" name="confirm_pass" placeholder="Re-enter new Password">
                    </div>
                    <div class="form-group">
                        <button class="form-control btn" style="margin-top: 15px; background-color: #4267b2;" name="update_password">
                            <span style="color: white;">Update Password</span></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
