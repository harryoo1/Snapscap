<head>
    <style>
        .user_details {
            background-color: #fff;
            padding: 10px;
            border: 1px solid #030303;
            border-radius: 5px;
            box-shadow: 4px 8px 16px 0px rgba(0, 0, 0, 0.25);
            z-index: 1;
        }

        #my_profile_holder {
            float: left;
            margin: 10px !important;
        }

        .user_picture {
            border-radius: 10px;
            height: 90px;
            width: 90px;
            box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);
        }

        #details_holder {
            float: right;
            text-align: left;
            margin-top: 5px;
            width: 140px;

        }

        .word-styling {
            font-size: .8em;
            font-family: sans-serif;
        }

        .heading-style {
            color: rgba(69, 162, 255, 0.93);
            font-size: .9em;
        }

    </style>
</head>
<?php
$username = $_COOKIE["user_name"];
include("includes/db_connection.php");
$get_user_profile_query = "SELECT * FROM `users` WHERE `u_name` = '$username' LIMIT 1";
$result = mysqli_query($sqlcon,$get_user_profile_query);
$get_individual_data = mysqli_fetch_assoc($result);

$user_first_name = $get_individual_data["u_fname"];
$user_last_name = $get_individual_data["u_lname"];
$user_gender = $get_individual_data["u_gender"];
$user_email = $get_individual_data["u_email"];
$user_phone = $get_individual_data["u_phone"];
$joining_date = $get_individual_data["date"];
$posts_count = $get_individual_data["num_posts"];
$likes_count = $get_individual_data["num_likes"];
$account_status = $get_individual_data["status"];
$user_profile_pic = $get_individual_data["profile_pic"];

?>
