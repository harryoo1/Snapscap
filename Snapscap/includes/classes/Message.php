<?php 

class Message {
    private $user_obj;
    private $con;
    
    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }
    
    public function getMostRecentUser(){
        $userLoggedIn = $this->user_obj->getUserName();
        
        $query = mysqli_query($this->con, "SELECT `user_to`, `user_from` FROM `messages` WHERE `user_to` = '$userLoggedIn' OR `user_from` = '$userLoggedIn' ORDER BY `id` DESC LIMIT 1");
        if(mysqli_num_rows($query) == 0){
            return false;
        }
        $row = mysqli_fetch_array($query);
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];
        
        if($user_to != $userLoggedIn){
            return $user_to;
        }
        else{
            return $user_from;
        }
    }
    
    public function sendMessage($user_to, $body, $date){
        
        if($body != ""){
            
            $userLoggedIn = $this->user_obj->getUserName();
            
            $user_logged_obj = new User($this->con, $userLoggedIn);
            
            if($user_logged_obj->isFriend($user_to)){
                if($userLoggedIn != $user_to){
                    $query = mysqli_query($this->con, "INSERT INTO `messages`(`id`, `user_to`, `user_from`, `message_body`, `date`, `opened`, `viewed`, `deleted`) VALUES (NULL, '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
                }
            }
            else{
                header("location: temp.php?user_to=$user_to");
            }
        }
    }
    
    public function getMessage($otherUser){
        $userLoggedIn = $this->user_obj->getUserName();
        $data = "";
        $query = mysqli_query($this->con, "UPDATE `messages` SET `opened`= 'yes' WHERE `user_to`= '$userLoggedIn' AND `user_from`= '$otherUser'");
        $get_messages_query = mysqli_query($this->con, "SELECT * FROM `messages` WHERE (`user_to`= '$userLoggedIn' AND `user_from`= '$otherUser') OR (`user_from`= '$userLoggedIn' AND `user_to`= '$otherUser')");
        
        while($row = mysqli_fetch_array($get_messages_query)){
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];
            $message_body = $row['message_body'];            
            $div_top = ($user_from == $userLoggedIn) ? "<div class='message' id='sender'>" : "<div class='message' id='receiver'>";
            $data = $data . $div_top . $message_body . "</div><br><br>";
        }
        return $data;
    }
    
    public function getLatestMessage($userLoggedIn, $user2){
        $details_array = array();
        $user_to = "";
        $message_body = "";
        $date_time = "";
        $query = mysqli_query($this->con, "SELECT * FROM `messages` WHERE (`user_to`= '$userLoggedIn' AND `user_from`= '$user2') OR (`user_from`= '$userLoggedIn' AND `user_to`= '$user2') ORDER BY `id` DESC LIMIT 1");
        
        while($row = mysqli_fetch_array($query)){
            
             $user_to = $row['user_to'];
             $message_body = $row['message_body'];
             $date_time = $row['date'];
            
          }
        //$sent_by = ($user_to == $userLoggedIn) ? "They said : " : "You said : ";
        
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
                    $time_message = $interval->i . " min ago";
                }
                else {
                    $time_message = $interval->i . " min ago";
                } 
            }
        else {
               if($interval->s <= 30){
                    $time_message = "Just now";
                }
                else {
                    $time_message = $interval->s ." sec ago";
                } 
            }
        
        //array_push($details_array, $sent_by);
        array_push($details_array,$message_body);
        array_push($details_array, $time_message);
        
        return $details_array;
    }
    
    public function getConvos(){
        $userLoggedIn = $this->user_obj->getUserName();
        $return_string = "";
        $convos = array();
        
        $query = mysqli_query($this->con, "SELECT `user_to`, `user_from` FROM `messages` WHERE `user_from`= '$userLoggedIn' ORDER BY `id` DESC");
        
        while($row = mysqli_fetch_array($query)){
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
            if(!in_array($user_to_push, $convos)){
                array_push($convos, $user_to_push);
            }
        }
        
        foreach($convos as $username){
            $user_found_obj = new User($this->con, $username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);
            
            $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;
            $return_string .= "<a href='messages.php?u=$username' class='nav-link'>
                               <div class='user_found_messages'>
                               <img src='profile_pictures/" . $user_found_obj->getProfilePic() . "' width='35px' height='35px' style= 'border-radius: 25px; margin-right: 5px; object-fit: cover;'>
                               <span>". $user_found_obj->getUserName() ."</span>
                               <span class= 'timestamp_smaller' id='grey'>" . $latest_message_details[1] . "</span>
                               </div>
                               </a>";
        }
        return $return_string;
    }
    
    public function getOnlineConvos(){
        
        $userLoggedIn = $this->user_obj->getUserName();
        $profile_pic = "";
        $return_string = "";
        $convos = array();
        
        $count_online_query = mysqli_query($this->con, "SELECT COUNT(*) AS total FROM `users_online` WHERE `status`= 'online'");
        $total_online_users = mysqli_fetch_array($count_online_query);
        
        $fetch_online_query = mysqli_query($this->con, "SELECT * FROM `users_online` WHERE `status` = 'online' ORDER BY id ASC");
        $user_id = 0;
        
        while($row = mysqli_fetch_array($fetch_online_query)){
        $user_id++;   
        $online_username = $row['user_name'];
            echo "<div>";
            echo "<ul>";
            
            $user_logged_obj = new User($this->con, $userLoggedIn);
            if($user_logged_obj->isFriend($online_username)){
                
              if($online_username != $userLoggedIn){
                 $fetch_query = mysqli_query($this->con, "SELECT * FROM `users` WHERE `u_name` = '$online_username'");
            
                 while($row = mysqli_fetch_array($fetch_query)){
            
                    $username_picture = $row['profile_pic']; 
                    $return_string .= "<a href='messages.php?u=$online_username' class='nav-link' style= 'padding-left: 0px;'>
                                  <li class='user_found_online' id='$user_id' name='$online_username' style= 'padding-left: 0px;'>
                                  <img src='profile_pictures/" . $username_picture . "' width='35px' height='35px' style= 'border-radius: 25px; margin-right: 5px; object-fit: cover;'>"."
                                  <span class= 'online_badge'></span>
                                  <span style= 'margin-left: 0px; vertical-align: middle;'>".ucfirst($online_username)."</span>
                                  </li>
                                  </a>";
                }
              }
            }
            echo "</ul>";
            echo "</div>";
        }
        return $return_string;
    }
    
    function getConvosDropdown($data, $limit){
        $style = "";
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUserName();
        $return_string = "";
        $convos = array();
        
        if($page == 1){
            $start = 0;
        }
        else{
            $start = ($page - 1)* $limit;
        }
        
        $set_viewed_query = mysqli_query($this->con, "UPDATE `messages` SET `viewed`= 'yes' WHERE `user_to`= '$userLoggedIn'");
        
        $query = mysqli_query($this->con, "SELECT `user_to`, `user_from` FROM `messages` WHERE `user_from`= '$userLoggedIn' ORDER BY `id` DESC");
        
        while($row = mysqli_fetch_array($query)){
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
            if(!in_array($user_to_push, $convos)){
                array_push($convos, $user_to_push);
            }
        }
        
        $num_iterations = 0;
        $count = 1;
    
        foreach($convos as $username){
            
            if($num_iterations++ < $start){
                continue;
            }
            if($count > $limit){
                break;
            }
            else{
                $count = $count++;
            }
        
            $is_unread_query = mysqli_query($this->con, "SELECT `opened` FROM `messages` WHERE `user_to`= '$userLoggedIn' AND `user_from`= '$username' ORDER BY `id` DESC");
            while($row = mysqli_fetch_array($is_unread_query)){
                $style = ($row['opened'] == 'no')? "background-color: #DDEDFF;" : "";
            }
            
            $user_found_obj = new User($this->con, $username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);
            
            $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;
            $return_string .= "<a href='messages.php?u=$username' class='nav-link'>
                               <div class='user_found_messages' style= '". $style ."'>
                               <img src='profile_pictures/" . $user_found_obj->getProfilePic() . "' width='35px' height='35px' style= 'border-radius: 25px; margin-right: 5px; object-fit: cover;'>
                               <span>". $user_found_obj->getUserName() ."</span>
                               <span class= 'timestamp_smaller' id='grey'>" . $latest_message_details[1] . "</span>
                               </div>
                               </a>";
        }
        if($count > $limit){
            $return_string .= "<input type= 'hidden' class= 'nextPageDropdownData' value= '". ($page +1) ."'>
                               <input type= 'hidden' class= 'noMoreDropdownData' value= 'false'>";
        }
        else{
            $return_string .= "<input type= 'hidden' class= 'noMoreDropdownData' value= 'true'>
                               <p style= 'font-size: 12px; text-align: center;'>No more messages!</p>";
        }
        return $return_string;
    }
    
    public function getUnreadNumber(){
        $userLoggedIn = $this->user_obj->getUserName();
        $query = mysqli_query($this->con, "SELECT * FROM `messages` WHERE `viewed`= 'no' AND `user_to`= '$userLoggedIn'");
        return mysqli_num_rows($query);
        
    }
}
?>
