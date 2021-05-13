<?php
session_start();
$date = "";
$errors = array();
if(isset($_POST["submit"]))
{
    require ("includes/db_connection.php");
    $first_name = strip_tags($_POST["user_fname"]); //Remove html tags or extra code
    $first_name = str_replace(' ','',$first_name); //Remove Spaces
    $first_name = ucfirst(strtolower($first_name)); //Capitalize only first letter and convert all other letters to small
    $_SESSION['user_fname'] = $first_name;
    $last_name = strip_tags($_POST["user_lname"]);
    $last_name = str_replace(' ','',$last_name);
    $last_name = ucfirst(strtolower($last_name));
    $_SESSION['user_lname'] = $last_name;
    $uname = strip_tags($_POST["user_name"]);
    $uname = str_replace(' ','',$uname);
    $uname = ucfirst($uname);
    $_SESSION['user_name'] = $uname;
    $gender = $_POST["gender"];
    $email = strip_tags($_POST["user_email"]);
    $email = str_replace(' ','',$email);
    $_SESSION['user_email'] = $email;
    $phone = strip_tags($_POST["phone"]);
    $phone = str_replace(' ','',$phone);
    $_SESSION['phone'] = $phone;
    $date = date("y-m-d");
    $pass = $_POST["password"];
        if(empty($first_name)){
        array_push($errors,"First name is required !");
        }
        if(empty($last_name)){
        array_push($errors,"Last name is required !");
        }
        if(empty($uname)){
        array_push($errors,"User name is required !");
        }
        if(empty($gender)){
        array_push($errors,"Gender is required !");
        }
        if(empty($email)){
        array_push($errors,"Email is required !");
        }
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){ // Email validity
        $email = filter_var($email,FILTER_VALIDATE_EMAIL);
        // Check if email is already exist!
        $email_check = mysqli_query($sqlcon, "SELECT `u_email` FROM users WHERE `u_email`= '$email'");
        $num_rows = mysqli_num_rows($email_check);
        if($num_rows > 0){
        array_push($errors,"Email already in use !");
        }
        }
        else{
        array_push($errors,"Invalid email !");
        }
        if(empty($phone)){
        array_push($errors,"Phone Number is required !");
        }
        if(empty($pass)){
        array_push($errors,"Password is required !");
        }
        if(preg_match('/[^A-Za-z0-9]/', $pass)){
        array_push($errors,"Password can only contain alphabets and numbers !");    
        }
        if(strlen($pass) < 8 || strlen($pass) >15){
        array_push($errors,"Password length should be between 8 to 15 characters !");  
        }
        if(count($errors) == 0){
        $password = md5($pass);
        $insert_query = "INSERT INTO `users`(`u_id`, `u_fname`, `u_lname`, `u_name`, `u_gender`, `u_email`, `u_phone`, `u_password`, `date`, `profile_pic`, `num_posts`, `num_likes`, `status`,  
                        `friend_array`) VALUES (NULL, '$first_name', '$last_name', '$uname', '$gender', '$email', '$phone', '$password', '$date', 'default.png', '', '', 'active', 
                         ',')";
        $register_result =  mysqli_query($sqlcon,$insert_query);
        echo "<script>alert('Successfully Registered');</script>";
        header("location: login.php");
        $_SESSION['user_fname'] = "";
        $_SESSION['user_lname'] = "";
        $_SESSION['user_name'] = "";
        $_SESSION['user_email'] = "";
        $_SESSION['phone'] = "";
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
        <div class="reg-form">
            <h1 class="text-center" style="color: #dcdcdc; font-size: 30px; font-family: sans-serif;">Register</h1>
            <form action="register.php" method="post" class="form-group">
                <div class="form-row" style="margin-bottom: 10px;">
                    <div class="col-md-6" style="margin-bottom: 5px;">
                        <input type="text" id="firstname" name="user_fname" class="form-control" value="<?php if(isset($_SESSION['user_fname'])){echo $_SESSION['user_fname'];}?>" placeholder="First name">
                        <?php if(in_array("First name is required !", $errors)){echo "<span class='error'>First name is required !</span>";}?>
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="lastname" name="user_lname" class="form-control" value="<?php if(isset($_SESSION['user_lname'])){echo $_SESSION['user_lname'];}?>" placeholder="Last name">
                        <?php if(in_array("Last name is required !", $errors)){echo "<span class='error'>Last name is required !</span>";}?>
                    </div>
                </div>
                <div style="margin-top: 10px;">
                    <input type="text" id="uname" name="user_name" class="form-control" value="<?php if(isset($_SESSION['user_name'])){echo $_SESSION['user_name'];}?>" placeholder="Username">
                    <?php if(in_array("User name is required !", $errors)){echo "<span class='error'>User name is required !</span>";}?>
                </div>
                <div style="margin-top: 10px;">
                    <label style="color: #dcdcdc;"><strong>Gender</strong></label><br>
                    <input type="radio" name="gender" value="Male" style="margin: 5px;" checked><span style="color: #dcdcdc; font-size: 14px;">Male</span><br>
                    <input type="radio" name="gender" value="Female" style="margin: 5px;"><span style="color: #dcdcdc; font-size: 14px;">Female</span><br>
                    <?php if(in_array("Gender is required !", $errors)){echo "<span class='error'>Gender is required !</span>";}?>
                </div>
                <div style="margin-top: 10px;">
                    <input type="email" id="email" name="user_email" class="form-control" value="<?php if(isset($_SESSION['user_email'])){echo $_SESSION['user_email'];}?>" placeholder="Email">
                    <?php if(in_array("Email is required !", $errors)){echo "<span class='error'>Email is required !</span>";}?>
                    <?php if(in_array("Email already in use !", $errors)){echo "<span class='error'>Email already in use !</span>";}?>
                    <?php if(in_array("Invalid email !", $errors)){echo "<span class='error'>Invalid email !</span>";}?>
                </div>
                <div style="margin-top: 20px;">
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php if(isset($_SESSION['phone'])){echo $_SESSION['phone'];}?>" placeholder="Phone">
                    <?php if(in_array("Phone Number is required !", $errors)){echo "<span class='error'>Phone Number is required !</span>";}?>
                </div>
                <div style="margin-top: 20px;">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                    <?php if(in_array("Password is required !", $errors)){echo "<span class='error'>Password is required !</span>";}?>
                    <?php if(in_array("Password can only contain alphabets and numbers !", $errors)){echo "<span class='error'>Password can only contain alphabets and numbers !</span>";}?>
                    <?php if(in_array("Password length should be between 8 to 15 characters !", $errors)){echo "<span class='error'>Password length should be between 8 to 15 characters !</span>";}?>
                </div>
                <a class="nav-link d-block small text-center" href="login.php">Login to your Account</a>
                <div class="form-group">
                    <button type="submit" name="submit" class="form-control btn btn-primary" style="margin-top: 10px;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
