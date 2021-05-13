<?php
include("includes/header.php");
 
if(isset($_GET['username'])) {
    $username = $_GET['username'];
}
else {
    $username = $user_loggedin;
}
?>

<div class="container">
    <div class="friend_list_container">
       <h5 style="text-align: center; background-color: #f0f0f5; font-family: 'Josefin Sans', sans-serif; height: 40px; padding-top: 10px;">Friend List</h5>
        <?php
          $user_obj = new User($sqlcon, $username);
          $friend_list = $user_obj->getFriendsList();
          if($friend_list){
          foreach($friend_list as $friend) {
 
           $friend_obj = new User($sqlcon, $friend);
           
           if($friend != $user_loggedin){
            $mutual_friends = $user_obj->getMutualFriends($friend)." Mutual friends";
          }
          else{
            $mutual_friends = "";
          }
          echo "<div class= 'friend_list_result_display'>
              <a href= '". $friend ."' style= 'text-decoration: none;'>
              <div class= 'friend_list_profile_pic'>
              <img src='profile_pictures/" . $friend_obj->getProfilePic() . "' width='40px' height='40px' style= 'border-radius: 25px; margin: 5px; object-fit: cover;'>
                
              <span class= 'live_search_text' style='font-size: 16px; color: dodgerblue'>". $friend_obj->getFirstAndLastName() ."</span>
              <span style='font-size: 12px; color: #8c8c8c;'>". $mutual_friends ."</span>
              </div>
              </a>
              </div>";
        }
      }
    else {
        echo "No Friends available";
    }
        
?>
    </div>
</div>
