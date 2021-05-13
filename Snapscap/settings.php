<?php
include("includes/header.php");
//include("includes/settings_handler.php");
$errors = array();
if(isset($_POST['update_details'])){
    $first_name = $_POST['u_fname'];
    $last_name = $_POST['u_lname'];
    $user_name = $_POST['u_name'];
    $email = $_POST['u_email'];
    $phone = $_POST['u_phone'];
    $user_check = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE `u_email`= '$user_name'");
    $row = mysqli_fetch_array($user_check);
    $matched_user = $row['u_name'];
    
    if($matched_user == "" || $matched_user == $user_loggedin){
        $query = mysqli_query($sqlcon, "UPDATE `users` SET `u_fname`= '$first_name', `u_lname`= '$last_name', `u_email`= '$email',`u_phone`= '$phone' WHERE `u_name`= '$user_loggedin'");
        header("location: settings.php");
    }
    else{
        array_push($errors,"Username is already in use!");  
    }
}

if(isset($_POST['close_account'])){
    header("location: close_account.php");
}
?>
<div class="container" style="">
    <div class="details_container">
        <div class="text-center" style="color: #5383d3; font: normal 25px 'Cookie', sans-serif; margin-top: 20px;">Edit Your Profile</div>
        <hr>
        <form action="settings.php" method="post" class="form-group" style="color: #384047; font: normal 15px 'Josefin Sans', sans-serif;">
            <div class="form-group reg-form">
                <input type="hidden" value="<?php echo $user['id']?>" name="id">
                <div class="form-row" style="margin-top: 10px;">
                    <div class="col-md-6" style="margin-bottom: 10px;">
                       <label>First Name :</label>
                        <input type="text" class="form-control" value="<?php echo $user['u_fname']?>" name="u_fname" placeholder="First name" style="color: #384047; font: normal 15px 'Josefin Sans', sans-serif;">
                    </div>
                    <div class="col-md-6" style="margin-bottom: 10px;">
                       <label>Last Name :</label>
                        <input type="text" class="form-control" value="<?php echo $user['u_lname']?>" name="u_lname" placeholder="Last name" style="color: #384047; font: normal 15px 'Josefin Sans', sans-serif;">
                    </div>
                </div>
                <div style="margin-top: 10px;">
                   <label>Username :</label>
                    <input type="text" class="form-control" value="<?php echo $user['u_name']?>" name="u_name" placeholder="User name" style="color: #384047; font: normal 15px 'Josefin Sans', sans-serif;">
                    <?php if(in_array("Username is already in use!", $errors)){echo "<span class='error'>Username is already in use!</span>";}?>
                </div>
                <div style="margin-top: 20px;">
                    <label style="color: #696969;"><strong>Gender</strong></label><br>
                    <input type="radio" name="gender" <?php if($user['u_gender']=='Male' ){echo 'checked' ;}?> value="Male"><span style="color: #696969; font-size: 14px;">Male</span><br>
                    <input type="radio" name="gender" <?php if($user['u_gender']=='Female' ){echo 'checked' ;}?> value="Female"><span style="color: #696969; font-size: 14px;">Female</span></div>
                <div style="margin-top: 20px;">
                   <label>Email :</label>
                    <input type="email" class="form-control" value="<?php echo $user['u_email']?>" name="u_email" placeholder="Email" style="color: #384047; font: normal 15px 'Josefin Sans', sans-serif;">
                </div>
                <div style="margin-top: 20px; margin-bottom: 10px;">
                   <label>Phone :</label>
                    <input type="text" class="form-control" value="<?php echo $user['u_phone']?>" name="u_phone" placeholder="Phone" style="color: #384047; font: normal 15px 'Josefin Sans', sans-serif;">
                </div>
                    <a href="change_password.php" class="nav-link" style="float: left;">Update password</a>
                        <input type="submit" name="close_account" id="close_account" value="Close account" title="Close Account" style="border: none; background-color: transparent; float: right; color: #FF6347; font-size: 15px; margin-top: 10px; margin-right: 20px;">
                <div class="form-group">
                    <button class="form-control btn" style="margin-top: 15px; background-color: #4267b2;" name="update_details"><span style="color: white;">Update</span></button>
                </div>
            </div>
        </form>
    </div>
</div>
