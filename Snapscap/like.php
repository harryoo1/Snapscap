<!DOCTYPE html>
<html>

<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="css/Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/main/main.css">
</head>

<body>
    <?php
    require("includes/db_connection.php");
    include("includes/classes/User.php");
    include("includes/classes/Post.php");
    include("includes/classes/Notification.php");
    
    if(isset($_COOKIE['user_name'])){
    $user_loggedin = $_COOKIE['user_name'];
    $user_details_query = "SELECT * FROM `users` WHERE `u_name`='$user_loggedin'";
    $user_details_query_result = mysqli_query($sqlcon,$user_details_query);
    $user = mysqli_fetch_array($user_details_query_result);
    }
    else{
        header("location: login.php");
    } 
    
    if(isset($_GET['post_id'])){
        $post_id = $_GET['post_id'];
    }
    
    $get_likes = mysqli_query($sqlcon, "SELECT `added_by`, `likes` FROM `posts` WHERE `id`= '$post_id'");
    $row = mysqli_fetch_array($get_likes);
    
    $user_liked = $row['added_by'];
    $total_likes = $row['likes'];
    
    $user_details_query = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE `u_name` = '$user_liked'");
    $row = mysqli_fetch_array($user_details_query);
    $user_total_likes = $row['num_likes'];
    
    // Like button
    if(isset($_POST["like_button".$post_id])){
        $total_likes++;
        $query = mysqli_query($sqlcon, "UPDATE `posts` SET `likes`= '$total_likes' WHERE `id` = '$post_id'");
        $user_total_likes++;
        
        $user_likes = mysqli_query($sqlcon, "UPDATE `users` SET `num_likes`= '$user_total_likes' WHERE `u_name`= '$user_liked'");
        
        $insert_query = mysqli_query($sqlcon, "INSERT INTO `likes`(`id`, `user_name`, `post_id`) VALUES ('NULL', '$user_loggedin', '$post_id')");
        
    // Update notification
       if($user_liked != $user_loggedin){
           $notification_obj = new Notification($sqlcon, $user_loggedin);
           $notification_obj->insertNotification($post_id, $user_liked, "like");
      }
    }
    
    //Unlike button
    if(isset($_POST["unlike_button".$post_id])){
        $total_likes--;
        $query = mysqli_query($sqlcon, "UPDATE `posts` SET `likes`= '$total_likes' WHERE `id` = '$post_id'");
        $user_total_likes--;
        
        $user_likes = mysqli_query($sqlcon, "UPDATE `users` SET `num_likes`= '$user_total_likes' WHERE `u_name`= '$user_liked'");
        
        $delete_query = mysqli_query($sqlcon, "DELETE FROM `likes` WHERE `user_name` = '$user_loggedin' AND `post_id` = '$post_id'");
    }
    // check for previous likes
    
    $check_query = mysqli_query($sqlcon, "SELECT * FROM `likes` WHERE `user_name` = '$user_loggedin' AND `post_id` = '$post_id'");
    $num_rows = mysqli_num_rows($check_query);
    
    if($num_rows > 0){
        echo '<form action="like.php?post_id='.$post_id.'" method="post" style="position: absolute; top: 0;">
        <button type="submit" class="post_like" name="unlike_button'.$post_id.'"><i class="fa fa-thumbs-up" aria-hidden="true"></i></button>
        <div class="like_value">'.$total_likes.'Likes 
        </div>
        </form>';
    }
    else{
        echo '<form action="like.php?post_id='.$post_id.'" method="post" style="position: absolute; top: 0;">
        <button type="submit" class="post_like" name="like_button'.$post_id.'"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i></button>
        <div class="like_value">'.$total_likes.'Likes 
        </div>
        </form>';
    }

?>


</body>

</html>
