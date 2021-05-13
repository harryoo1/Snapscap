<?php 
class User {
    private $user;
    private $con;
    
    public function __construct($con, $user){
        $this->con = $con;
        $user_details_query = "SELECT * FROM `users` WHERE `u_name`= '$user'";
        $user_details_query_result = mysqli_query($con, $user_details_query);
        $this->user = mysqli_fetch_array($user_details_query_result);
    }
    
    public function getUserName(){
        return $this->user['u_name'];
    }
    
    public function getNumPosts(){
        $username = $this->user['u_name'];
        $query = "SELECT `num_posts` FROM `users` WHERE `u_name` = '$username'";
        $result = mysqli_query($this->con, $query);
        $row = mysqli_fetch_array($result);
        return $row['num_posts'];
    }
    
    public function getFirstAndLastName(){
        $username = $this->user['u_name'];
        $query = mysqli_query($this->con, "SELECT `u_fname`, `u_lname` FROM `users` WHERE `u_name` = '$username'");
        $row = mysqli_fetch_array($query);
        return $row['u_fname']." ".$row['u_lname'];
    }
    
    public function getProfilePic(){
        $username = $this->user['u_name'];
        $query = mysqli_query($this->con, "SELECT `profile_pic` FROM `users` WHERE `u_name` = '$username'");
        $row = mysqli_fetch_array($query);
        $profile_pic = $row['profile_pic'];
        return $profile_pic;
    }
    
    public function getFriendArray(){
        $username = $this->user['u_name'];
        $query = mysqli_query($this->con, "SELECT `friend_array` FROM `users` WHERE `u_name` = '$username'");
        $row = mysqli_fetch_array($query);
        $profile_pic = $row['friend_array'];
        return $profile_pic;
    }
    
    public function isClosed(){
        $username = $this->user['u_name'];
        $query = mysqli_query($this->con, "SELECT `status` FROM `users` WHERE `u_name` = '$username'");
        $row = mysqli_fetch_array($query);
        if($row['status'] == 'yes'){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function isFriend($username_to_check){
        $username_to_check = ucfirst($username_to_check);
        $usernameComma = ",".$username_to_check.",";
        if((strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['u_name'])){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function didReceiveRequest($user_from){
        $user_to = $this->user['u_name'];
        $check_request_query = mysqli_query($this->con, "SELECT * FROM `friend_requests` WHERE `user_to` = '$user_to' AND `user_from` = '$user_from'");
        
        if(mysqli_num_rows($check_request_query) > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function didSendRequest($user_to){
        $user_from = $this->user['u_name'];
        $check_request_query = mysqli_query($this->con, "SELECT * FROM `friend_requests` WHERE `user_to` = '$user_to' AND `user_from` = '$user_from'");
        
        if(mysqli_num_rows($check_request_query) > 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function removeFriend($user_to_remove){
        $logged_in_user = $this->user['u_name'];
        $query = mysqli_query($this->con, "SELECT `friend_array` FROM `users` WHERE `u_name` = '$user_to_remove'");
        $row = mysqli_fetch_array($query);
        $friend_array_username = $row['friend_array'];
        
        $new_friend_array = str_replace($user_to_remove.",", "", $this->user['friend_array']);
        $remove_friend = mysqli_query($this->con, "UPDATE `users` SET `friend_array`= '$new_friend_array' WHERE `u_name` = '$logged_in_user'");
        
        $new_friend_array = str_replace($this->user['u_name'].",", "", $friend_array_username);
        $remove_friend = mysqli_query($this->con, "UPDATE `users` SET `friend_array`= '$new_friend_array' WHERE `u_name` = '$user_to_remove'");
    }
    
    public function sendRequest($user_to){
        $user_from = $this->user['u_name'];
        $query = mysqli_query($this->con, "INSERT INTO `friend_requests`(`id`, `user_to`, `user_from`) VALUES (NULL, '$user_to', '$user_from')");
    }
    
    public function getNumberOfFriendRequests(){
        $username = $this->user['u_name'];
        $query = "SELECT * FROM `friend_requests` WHERE `user_to` = '$username'";
        $result = mysqli_query($this->con, $query);
        return mysqli_num_rows($result);
    }
    
    public function getMutualFriends($user_to_check){
        $mutual_friends = 0;
        $user_array = $this->user['friend_array'];
        $user_array_explode = explode(",", $user_array);
        $query = mysqli_query($this->con, "SELECT `friend_array` FROM `users` WHERE `u_name`= '$user_to_check'");
        $row = mysqli_fetch_array($query);
        $user_to_check_array = $row['friend_array'];
        $user_to_check_array_explode = explode(",", $user_to_check_array);
        
        foreach($user_array_explode as $i){
            
            foreach($user_to_check_array_explode as $j){
                
                if($i == $j && $i != ""){
                    $mutual_friends++;
                }
            }
        }
        return $mutual_friends;
        
    }
    
    public function getFriendsList(){
        $friend_array_string1 = $this->user['friend_array'];
        $friend_array_string2 = trim($friend_array_string1, ","); //Remove first and last comma
        $returned_friends = explode(",", $friend_array_string2); //Split to array at each comma
        
        if($friend_array_string1 != ","){
            return $returned_friends;
        }
    }
        
  }
?>
