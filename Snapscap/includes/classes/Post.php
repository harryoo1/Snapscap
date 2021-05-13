<?php 
class Post {
    private $user_obj;
    private $con;
    
    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }
    
    public function submitPost($body, $user_to, $imageName){
        $body = strip_tags($body);
        $body = mysqli_real_escape_string($this->con, $body);
        $body = str_replace('\r\n', '\n', $body);
        $body = nl2br($body);
        $check_empty = preg_replace('/\s+/', '', $body);
        
        if($check_empty != ""){
            
            $body_array = preg_split("/\s+/", $body);
            foreach($body_array as $key =>$value){
                if(strpos($value, "www.youtube.com/watch?v=") !== false){
                    $link = preg_split("!&!", $value);
                    $value = preg_replace("!watch\?v=!", "embed/", $link[0]);
                    $value = "<br><iframe width= \'288\' height= \'288\' src= \'". $value ."\' id=\'youtube_frame\' frameborder=\'0\'></iframe><br>";
                    $body_array[$key] = $value;
                }
            }
            $body = implode(" ", $body_array);
            
            $date_added = date("y-m-d H:i:s"); // current date and time
            
            $added_by = $this->user_obj->getUserName(); //get user name
            
            if($user_to == $added_by){ //if user is not on own profile, user_to is 'none'
                $user_to = "none";
            }
            
            //Update post
            $query = "INSERT INTO `posts`(`id`, `body`, `added_by`, `user_to`, `date_added`, `user_closed`, `deleted`, `likes`, `image`) VALUES 
                     (NULL,'$body','$added_by','$user_to','$date_added','no','no','0','$imageName')";
            $result = mysqli_query($this->con, $query);
            $returned_id = mysqli_insert_id($this->con);
            
            //Update notifications
            if($user_to != "none"){
                $notification = new Notification($this->con, $added_by);
                $notification->insertNotification($returned_id, $user_to, "profile_post");
            }
            
            //Update post count for User
            $num_posts = $this->user_obj->getNumPosts();
            $num_posts ++;
            $update_query = mysqli_query($this->con, "UPDATE `users` SET `num_posts`= '$num_posts' WHERE `u_name`= '$added_by'");

        }
    }
    
    public function loadPostsFriends($data, $limit){
        
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUserName();
        if($page == 1){
            $start = 0;
        }
        else{
            $start = ($page - 1) * $limit;
        }
        
        $str = ""; //string to return
        $data_query = mysqli_query($this->con, "SELECT * FROM `posts` WHERE `deleted` = 'no' ORDER BY `id` DESC");
        
        if(mysqli_num_rows($data_query) > 0){       
            
            $num_iterations = 0; //number of results checked(not necessarily posted)
            $count = 1;
            
        while($row = mysqli_fetch_array($data_query)){
            $id = $row['id'];
            $body = $row['body'];
            $body = str_replace('\r\n', '\n', $body);
            $body = nl2br($body);
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];
            $imagePath = $row['image'];
            
            // prepare user_to string so it can be included even if not posted to a user
            if($row['user_to'] == 'none'){
                $user_to = "";
            }
            else{
                $user_to_obj = new User($this->con, $row['user_to']);
                $user_to_name = $user_to_obj->getFirstAndLastName();
                $user_to = "to <a href='".$row['user_to']."'>".$user_to_name."</a>";
            }
            
            // check if user who posted, has their account closed
            $added_by_obj = new User($this->con, $added_by);
            if($added_by_obj->isClosed()){
                continue;
            }
            
            //$user_logged_obj = new User($this->con, $userLoggedIn);
            //if($user_logged_obj->isFriend($added_by)){
            
            if($num_iterations++ < $start){
                continue;
            }
            if($count > $limit){
                break;
            }
            
            else{
                $count = $count++;
            }
                
            if($userLoggedIn == $added_by){
                $delete_button = "<button class='btn delete_post_btn' id='post$id' title='Delete this post'><i class='fa fa-trash'></i></button>";
            }
            else{
                $delete_button = "";    
            }
            
            $user_details_query = mysqli_query($this->con, "SELECT `u_fname`, `u_lname`, `profile_pic` FROM `users` WHERE `u_name` = '$added_by'");
            $user_row = mysqli_fetch_array($user_details_query);
            $first_name = $user_row['u_fname'];
            $last_name = $user_row['u_lname'];
            $profile_pic = $user_row['profile_pic'];
?>
<script>
    function toggle<?php echo $id;?>() {
        var target = $(Event.target);
        if (!target.is("a")) {
            var element = document.getElementById("toggleComment<?php echo $id;?>");
            if (element.style.display == "block") {
                element.style.display = "none";
            } else {
                element.style.display = "block";
            }
        }
    }

</script>
<?php       
                
     $comments_check = mysqli_query($this->con, "SELECT * FROM `comments` WHERE `post_id` = '$id'");
     $comments_check_num = mysqli_num_rows($comments_check);
                
            //time frame
            $date_time_now = date("y-m-d H:i:s");
            $start_date = new DateTime($date_time); //Time of post
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
                
                $imageDiv = "";
                
                if($imagePath != ''){
                    $imageDiv = "<div class='postImage'>
                                 <img class='posted_img' src='$imagePath'>
                                 </div>";
                }
                else{
                    $imagePath = "";
                }
                
                $img_and_post_body = "";
                $post_body = "";
                
                if($imageDiv != ""){
                         $img_and_post_body =  "<div id='post_body' class='post_body'>$body<br>$imageDiv</div>";
                     }
                     else{
                         
                         $post_body = "<div id='post_body' class='post_body'>$body</div>";
                     }
                
            $str.= " <div><div class='main-container' onClick='javascript:toggle$id()'>
                     <div class='user_det'>
                     <div class='post_profile_pic'>
                     <img src='profile_pictures/$profile_pic' width='35' height='35'>
                     </div>
                     <div class='posted_by' style='color: #acacac;'>
                     <a href='$added_by' style='text-decoration: none;'><span style='font-size: 16px;'>$first_name $last_name</span></a> $user_to       
                     <span style='font-size: 11px;'>$time_message</span>$delete_button
                     </div></div><hr>
                     <div id='post_body' class='post_body'>$img_and_post_body</div>
                     <div id='post_body' class='post_body'>$post_body</div>
                     <br>
                     <div class='newsfeed_options'>
                     <iframe src='like.php?post_id=$id' scrolling='no' id='like_iframe' frameborder='0'></iframe>
                     <div style='float: right; margin-top: 10px; margin-right: 10px;'>
                     <i class='fa fa-comments-o' aria-hidden='true'><span class='comment-span'>comments($comments_check_num)</span></i>
                     </div>
                     </div>
                     <div class='post_comment' id='toggleComment$id' style='display: none;'>
                     <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                     </div>
                     </div>";
               //}
            ?>
<script>
    $(document).ready(function() {
        $('#post<?php echo $id;?>').on('click', function() {
            bootbox.confirm("Are you sure to delete this post ?", function(result) {
                $.post("delete_post.php?post_id=<?php echo $id;?>", {
                    result: result
                });
                if (result) {
                    location.reload();
                }
            });
        });
    });
</script>
<?php
            }
            if($count > $limit){
                $str.="<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                          <input type='hidden' class='noMorePosts' value='false'>";
            }
            else{
                $str.="<input type='hidden' class= 'noMorePosts' value='true'> <p style='text-align: center;'>No more posts !</p>";
            }
        }
        echo $str;
    }
    
    public function loadPostsProfile($data, $limit){
        
        $page = $data['page'];
        $profileUser = $data['profileUsername'];
        $userLoggedIn = $this->user_obj->getUserName();
        if($page == 1){
            $start = 0;
        }
        else{
            $start = ($page - 1) * $limit;
        }
        
        $str = ""; //string to return
        $data_query = mysqli_query($this->con, "SELECT * FROM `posts` WHERE `deleted`= 'no' AND ((`added_by`= '$profileUser' AND `user_to`= 'none') OR `user_to`= '$profileUser') ORDER BY `id` DESC");
        
        if(mysqli_num_rows($data_query) > 0){       
            
            $num_iterations = 0; //number of results checked(not necessarily posted)
            $count = 1;
            
        while($row = mysqli_fetch_array($data_query)){
            $id = $row['id'];
            $body = $row['body'];
            $body = str_replace('\r\n', '\n', $body);
            $body = nl2br($body);
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];
            $imagePath = $row['image'];
            
            if($num_iterations++ < $start){
                continue;
            }
            if($count > $limit){
                break;
            }
            
            else{
                $count = $count++;
            }
                
            if($userLoggedIn == $added_by){
                $delete_button = "<button class='btn delete_post_btn' id='post$id' title='Delete this post'><i class='fa fa-trash'></i></button>";
            }
            else{
                $delete_button = "";    
            }
            
            $user_details_query = mysqli_query($this->con, "SELECT `u_fname`, `u_lname`, `profile_pic` FROM `users` WHERE `u_name` = '$added_by'");
            $user_row = mysqli_fetch_array($user_details_query);
            $first_name = $user_row['u_fname'];
            $last_name = $user_row['u_lname'];
            $profile_pic = $user_row['profile_pic'];
?>
<script>
    function toggle<?php echo $id;?>() {
        var target = $(Event.target);
        if (!target.is("a")) {
            var element = document.getElementById("toggleComment<?php echo $id;?>");
            if (element.style.display == "block") {
                element.style.display = "none";
            } else {
                element.style.display = "block";
            }
        }
    }

</script>
<?php       
                
     $comments_check = mysqli_query($this->con, "SELECT * FROM `comments` WHERE `post_id` = '$id'");
     $comments_check_num = mysqli_num_rows($comments_check);
                
            //time frame
            $date_time_now = date("y-m-d H:i:s");
            $start_date = new DateTime($date_time); //Time of post
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
                
                $imageDiv = "";
                
                if($imagePath != ''){
                    $imageDiv = "<div class='postImage'>
                                 <img class='posted_img' src='$imagePath'>
                                 </div>";
                }
                else{
                    $imagePath = "";
                }
                
                $img_and_post_body = "";
                $post_body = "";
                
                if($imageDiv != ""){
                         $img_and_post_body =  "<div id='post_body' class='post_body'>$body<br>$imageDiv</div>";
                     }
                     else{
                         
                         $post_body = "<div id='post_body' class='post_body'>$body</div>";
                     }
                
            $str.= " <div><div class='main-container' onClick='javascript:toggle$id()'>
                     <div class='user_det'>
                     <div class='post_profile_pic'>
                     <img src='profile_pictures/$profile_pic' width='35' height='35'>
                     </div>
                     <div class='posted_by' style='color: #acacac;'>
                     <a href='$added_by' style='text-decoration: none;'><span style='font-size: 16px;'>$first_name $last_name</span></a>      
                     <span style='font-size: 11px;'>$time_message</span>$delete_button
                     </div></div><hr>
                     <div id='post_body' class='post_body'>$img_and_post_body</div>
                     <div id='post_body' class='post_body'>$post_body</div>
                     <br>
                     <div class='newsfeed_options'>
                     <iframe src='like.php?post_id=$id' scrolling='no' id='like_iframe'></iframe>
                     <div style='float: right; margin-top: 10px; margin-right: 10px;'><i class='fa fa-comments-o' aria-hidden='true'><span class='comment-span'>comments($comments_check_num)</span></i></div>
                     </div>
                     <div class='post_comment' id='toggleComment$id' style='display: none;'>
                     <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                     </div>
                     </div>";
            ?>
<script>
    $(document).ready(function() {
        $('#post<?php echo $id;?>').on('click', function() {
            bootbox.confirm("Are you sure to delete this post ?", function(result) {
                $.post("delete_post.php?post_id=<?php echo $id;?>", {
                    result: result
                });
                if (result) {
                    location.reload();
                }
            });
        });
    });

</script>
<?php
            }
            if($count > $limit){
                $str.="<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                          <input type='hidden' class='noMorePosts' value='false'>";
            }
            else{
                $str.="<input type='hidden' class= 'noMorePosts' value='true'> <p style='text-align: center; margin-top: 20px;'>No more posts !</p>";
            }
        }
        echo $str;
    }
    
    public function getSinglePost($post_id){
        
        $userLoggedIn = $this->user_obj->getUserName();
        
        $opened_query = mysqli_query($this->con, "UPDATE `notifications` SET `opened`= '' WHERE `user_to`= '$userLoggedIn' AND `link` LIKE '%=$post_id'");
        
        $str = ""; //string to return
        $data_query = mysqli_query($this->con, "SELECT * FROM `posts` WHERE `deleted` = 'no' AND `id`= '$post_id' ORDER BY `id` DESC");
        
        if(mysqli_num_rows($data_query) > 0){      
            
            $row = mysqli_fetch_array($data_query);
            $id = $row['id'];
            $body = $row['body'];
            $body = str_replace('\r\n', '\n', $body);
            $body = nl2br($body);
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];
            $imagePath = $row['image'];
            
            // prepare user_to string so it can be included even if not posted to a user
            if($row['user_to'] == 'none'){
                $user_to = "";
            }
            else{
                $user_to_obj = new User($sqlcon, $row['user_to']);
                $user_to_name = $user_to_obj->getFirstAndLastName();
                $user_to = "to <a href='".$row['user_to']."'>".$user_to_name."</a>";
            }
            
            // check if user who posted, has their account closed
            $added_by_obj = new User($this->con, $added_by);
            if($added_by_obj->isClosed()){
                return;
            }
            
            $user_logged_obj = new User($this->con, $userLoggedIn);
            if($user_logged_obj->isFriend($added_by)){
                
            if($userLoggedIn == $added_by){
                $delete_button = "<button class='btn delete_post_btn' id='post$id' title='Delete this post'><i class='fa fa-trash'></i></button>";
            }
            else{
                $delete_button = "";    
            }
            
            $user_details_query = mysqli_query($this->con, "SELECT `u_fname`, `u_lname`, `profile_pic` FROM `users` WHERE `u_name` = '$added_by'");
            $user_row = mysqli_fetch_array($user_details_query);
            $first_name = $user_row['u_fname'];
            $last_name = $user_row['u_lname'];
            $profile_pic = $user_row['profile_pic'];
?>
<script>
    function toggle<?php echo $id;?>() {
        var target = $(Event.target);
        if (!target.is("a")) {
            var element = document.getElementById("toggleComment<?php echo $id;?>");
            if (element.style.display == "block") {
                element.style.display = "none";
            } else {
                element.style.display = "block";
            }
        }
    }

</script>
<?php       
                
     $comments_check = mysqli_query($this->con, "SELECT * FROM `comments` WHERE `post_id` = '$id'");
     $comments_check_num = mysqli_num_rows($comments_check);
                
            //time frame
            $date_time_now = date("y-m-d H:i:s");
            $start_date = new DateTime($date_time); //Time of post
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
                
                $imageDiv = "";
                
                if($imagePath != ''){
                    $imageDiv = "<div class='postImage'>
                                 <img class='posted_img' src='$imagePath'>
                                 </div>";
                }
                else{
                    $imagePath = "";
                }
                
                $img_and_post_body = "";
                $post_body = "";
                
                if($imageDiv != ""){
                         $img_and_post_body =  "<div id='post_body' class='post_body'>$body<br>$imageDiv</div>";
                     }
                     else{
                         
                         $post_body = "<div id='post_body' class='post_body'>$body</div>";
                     }
                
            $str.= " <div><div class='main-container' onClick='javascript:toggle$id()'>
                     <div class='user_det'>
                     <div class='post_profile_pic'>
                     <img src='profile_pictures/$profile_pic' width='35' height='35'>
                     </div>
                     <div class='posted_by' style='color: #acacac;'>
                     <a href='$added_by' style='text-decoration: none;'><span style='font-size: 16px;'>$first_name $last_name</span></a> $user_to       
                     <span style='font-size: 11px;'>$time_message</span>$delete_button
                     </div></div><hr>
                     <div id='post_body' class='post_body'>$img_and_post_body
                     <div id='post_body' class='post_body'>$post_body
                     </div>
                     </div><br>
                     <div class='newsfeed_options'>
                     <iframe src='like.php?post_id=$id' scrolling='no' id='like_iframe'></iframe>
                     <div style='float: right; margin-top: 10px; margin-right: 10px;'><i class='fa fa-comments-o' aria-hidden='true'><span class='comment-span'>comments($comments_check_num)</span></i></div>
                     </div>
                     <div class='post_comment' id='toggleComment$id' style='display: none;'>
                     <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                     </div>
                     </div>";
            ?>
<script>
    $(document).ready(function() {
        $('#post<?php echo $id;?>').on('click', function() {
            bootbox.confirm("Are you sure to delete this post ?", function(result) {
                $.post("delete_post.php?post_id=<?php echo $id;?>", {
                    result: result
                });
                if (result) {
                    location.reload();
                }
            });
        });
    });

</script>
<?php
    }
    else{
        echo "<p>You are not friend with this user!</p>";
        return;
      }
    }
    else{
        echo "<p>No post found! Link may be broken..</p>";
        return;
    }
    echo $str;
  }  
}
?>
