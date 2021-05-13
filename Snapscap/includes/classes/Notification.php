<?php

class Notification {
    private $user_obj;
    private $con;
    
    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }
    
    public function insertNotification($post_id, $user_to, $type){
        
        $userLoggedIn = $this->user_obj->getUserName();
        $userLoggedInName = $this->user_obj->getFirstAndLastName();
        $date_time = date("y-m-d H:i:s");
        
        switch($type){
            case "comment":
                $message = $userLoggedInName . "commented on your post";
                break;
            
            case "like":
                $message = $userLoggedInName . "liked your post";
                break;
                
            case "profile_post":
                $message = $userLoggedInName . "posted on your timeline";
                break;
            
            case "comment_non_owner":
                $message = $userLoggedInName . "commented on your post you commented on";
                break;
            
            case "profile_comment":
                $message = $userLoggedInName . "commented on your profile post";
                break;
        }
        
        $link = "post.php?id=".$post_id;
        
        $insert_query = mysqli_query($this->con, "INSERT INTO `notifications`(`id`, `user_to`, `user_from`, `message`, `link`, `date_time`, `opened`, `viewed`) 
                                     VALUES (NULL, '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
        
        echo "Error: " . mysqli_error($this->con);
    }
    
    public function getNotifications($data, $limit){
        $style = "";
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUserName();
        $return_string = "";
        
        if($page == 1){
            $start = 0;
        }
        else{
            $start = ($page - 1)* $limit;
        }
        
        $set_viewed_query = mysqli_query($this->con, "UPDATE `notifications` SET `viewed`= 'yes' WHERE `user_to`= '$userLoggedIn'");
        
        $query = mysqli_query($this->con, "SELECT * FROM `notifications` WHERE `user_to`= '$userLoggedIn' ORDER BY `id` DESC");
        
        if(mysqli_num_rows($query) == 0){
            echo "<p style= 'font-size: 12px; text-align: center; margin-top: 15px;'>No new Notifications!</p>";
            return;
        }
        
        $num_iterations = 0;
        $count = 1;
    
        while($row = mysqli_fetch_array($query)){
            
            if($num_iterations++ < $start){
                continue;
            }
            if($count > $limit){
                break;
            }
            else{
                $count = $count++;
            }
            
            $user_from = $row['user_from'];
            $date_time = $row['date_time'];
            
            $user_data_query = mysqli_query($this->con, "SELECT * FROM `users` WHERE `u_name`= '$user_from'");
            $user_data = mysqli_fetch_array($user_data_query);
            
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
        
            $opened = $row['opened'];
            $style = ($opened == 'no')? "background-color: #DDEDFF;" : "";
            
            $return_string .= "<a href='".$row['link']."' class='nav-link'>
                               <div class='resultDisplay user_found_notifications' style= '". $style ."'>
                               <img src='profile_pictures/" . $user_data['profile_pic'] . "' width='35px' height='35px' style= 'border-radius: 25px; margin-right: 5px; float: left;'>
                               <div>
                               <span class= 'notification_message'>" .$user_data['u_name']." ".$row['message'] . "</span>
                               <span class= 'timestamp_smaller' id='grey'>" . $time_message . "</span>
                               </div>
                               </div>
                               </a>";
        }
        if($count > $limit){
            $return_string .= "<input type= 'hidden' class= 'nextPageDropdownData' value= '". ($page +1) ."'>
                               <input type= 'hidden' class= 'noMoreDropdownData' value= 'false'>";
        }
        else{
            $return_string .= "<input type= 'hidden' class= 'noMoreDropdownData' value= 'true'>
                               <p style= 'font-size: 12px; text-align: center; margin-top: 15px;'>No more Notifications!</p>";
        }
        return $return_string;
    }
    
    public function getUnreadNumber(){
        $userLoggedIn = $this->user_obj->getUserName();
        $query = mysqli_query($this->con, "SELECT * FROM `notifications` WHERE `viewed`= 'no' AND `user_to`= '$userLoggedIn'");
        return mysqli_num_rows($query);
        
    }
}
?>
