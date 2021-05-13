<?php 
    require ("includes/db_connection.php");
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
?>
<!DOCTYPE html>
<html>

<head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="css/Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/main/main.css">
    <style>
        

    </style>
</head>

<body>
    <script>
        function toggle() {
            var element = document.getElementById("comment_section");
            if (element.style.display == "block") {
                element.style.display = "none";
            } else {
                element.style.display = "block";
            }
        }

    </script>
    <?php 
    if(isset($_GET['post_id'])){
        $post_id = $_GET['post_id'];
    }
    
    $user_query = mysqli_query($sqlcon, "SELECT `added_by`, `user_to` FROM `posts` WHERE `id` = '$post_id'");
    $row = mysqli_fetch_array($user_query);
    
    $posted_to = $row['added_by'];
    $user_to = $row['user_to'];
    
    if(isset($_POST["post_Comment".$post_id])){
        $post_body = $_POST["post_body"];
        $post_body = mysqli_escape_string($sqlcon, $post_body);
        $date_time_now = date("y-m-d H:i:s");
        if($post_body != ""){
        $insert_post_query = "INSERT INTO `comments`(`id`, `comment_body`, `posted_by`, `posted_to`, `date_added`, `removed`, `post_id`) VALUES (NULL,'$post_body','$user_loggedin','$posted_to','$date_time_now','no','$post_id')";
        $insert_post_result = mysqli_query($sqlcon, $insert_post_query);
        
        //Update Notification
        if($posted_to != $user_loggedin){
            $notification_obj = new Notification($sqlcon, $user_loggedin);
            $notification_obj->insertNotification($post_id, $posted_to, "comment");
        }
        if($user_to != 'none' && $user_to != $user_loggedin){
            $notification_obj = new Notification($sqlcon, $user_loggedin);
            $notification_obj->insertNotification($post_id, $user_to, "profile_comment");
        }
        
        $get_commenters = mysqli_query($sqlcon, "SELECT * FROM `comments` WHERE `post_id`= '$post_id'");
        $notified_users = array();
        while($row = mysqli_fetch_array($get_commenters)){
            
            if($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to 
                && $row['posted_by'] != $user_loggedin && !in_array($row['posted_by'], $notified_users)){
                
                $notification_obj = new Notification($sqlcon, $user_loggedin);
                $notification_obj->insertNotification($post_id, $row['posted_by'], "comment_non_owner");
                
                array_push($notified_users, $row['posted_by']);
            }
        }  
      }
    }
    ?>
    <hr>
    <form action="comment_frame.php?post_id=<?php echo $post_id;?>" id="comment_form" name="postComment<?php echo $post_id;?>" method="post">
        <textarea name="post_body" placeholder="Write your comment !" class="comment-form" style="overflow-y: hidden;"></textarea>
        <button type="submit" name="post_Comment<?php echo $post_id;?>" class="btn btn-outline-success comment_post_btn" style="position: absolute;">
            <span class="fa fa-share-square" style="font-size: 20px;"></span>
        </button>
    </form>
    <!-- Loading comments-->
    <?php
    $get_comments = mysqli_query($sqlcon, "SELECT * FROM `comments` WHERE `post_id` = '$post_id' ORDER BY `id` ASC");
    $count = mysqli_num_rows($get_comments);
    if($count != 0){
        while($comment = mysqli_fetch_array($get_comments)){
            $comment_body = $comment['comment_body'];
            $posted_to = $comment['posted_to'];
            $posted_by = $comment['posted_by'];
            $date_added = $comment['date_added'];
            $removed = $comment['removed'];
            
            //time frame
            $date_time_now = date("y-m-d H:i:s");
            $start_date = new DateTime($date_added); //Time of post
            $end_date = new DateTime($date_time_now); //Current time
            $interval = $start_date->diff($end_date); //Difference betwen dates
            if($interval->y >= 1){
                if($interval->y = 1){
                    $time_message = $interval->y . "year ago";
                }
                else{
                    $time_message = $interval->y . "years ago";
                }
            }
            else if($interval->m >= 1){
                if($interval->d ==0){
                    $days = " ago";
                }
                else if($interval->d ==1){
                    $days = $interval->d . " day ago";
                }
                else {
                    $days = $interval->d . " days ago";
                }
                
                if($interval->m == 1){
                    $time_message = $interval->m . "month".$days;
                }
                else{
                    $time_message = $interval->m . "months".$days;
                }
             }
             else if($interval->d >= 1){
                if($interval->d ==1){
                    $time_message = "Yesterday";
                }
                else {
                    $time_message = $interval->d . " days ago";
                }
            }
            else if($interval->h >= 1){
               if($interval->h ==1){
                    $time_message = $interval->h . " hour ago";
                }
                else {
                    $time_message = $interval->h . " hours ago";
                } 
            }
            else if($interval->i >= 1){
               if($interval->i ==1){
                    $time_message = $interval->i . " minute ago";
                }
                else {
                    $time_message = $interval->i . " minutes ago";
                } 
            }
            else {
               if($interval->s <= 30){
                    $time_message = "Just now";
                }
                else {
                    $time_message = $interval->s ." seconds ago";
                } 
            }
            
            $user_obj = new User($sqlcon, $posted_by);
            ?>
    <div class="comment_section">
        <a href="<?php echo $posted_by;?>" target="_parent"><img class="comment_dp" src="profile_pictures/<?php echo $user_obj->getProfilePic();?>"></a>&nbsp;
        <a href="<?php echo $posted_by;?>" target="_parent" style="text-decoration: none;"><span class="posted-by_name"><?php echo $user_obj->getFirstAndLastName();?></span></a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo "<span style='font-family: sans-serif; font-size: 10px; color: #acacac;'>$time_message</span><br>"
        ."<span class='comment-body'>$comment_body</span>";?>
    </div>
    <?php
            
        }
    }
    
    ?>


</body>

</html>
