<?php
include("includes/header.php");

if(isset($_GET['q'])){
    $query = $_GET['q'];
}
else{
    $query = "";
}

if(isset($_GET['type'])){
    $type = $_GET['type'];
}
else{
    $type = "name";
}
?>
<div class="container" style= "background-color: #fff;">
    <?php
    if($query == ""){
        echo "You must enter something in the search box !";
    }
    else{
    
    //-------if query contains an Underscore_, assume user is searching for username-----//
    if($type == "u_name"){
        $userReturnedQuery = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE `u_name` LIKE '$query%' AND `status`= 'active' LIMIT 8");
    }
    else{
        $names = explode(" ", $query);
    
    if(count($names) == 3){
        $userReturnedQuery = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE (`u_fname` LIKE '$names[0]%' AND `u_lname` LIKE '$names[2]%') AND `status`= 'active'");
    }
    
    else if(count($names) == 2){
        $userReturnedQuery = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE (`u_fname` LIKE '$names[1]%' AND `u_lname` LIKE '$names[2]%') AND `status`= 'active'");
    }
    else{
        $userReturnedQuery = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE (`u_fname` LIKE '$names[0]%' OR `u_lname` LIKE '$names[0]%') AND `status`= 'active'");
    }
       
    }
        
    // check if results found
        if(mysqli_num_rows($userReturnedQuery) == 0){
            echo "User not found with ". $type ." like ". $query;
        }
        else{
            echo mysqli_num_rows($userReturnedQuery)."results found : <br> <br>";
        }
        
        echo "<p id= 'grey'>Try searching for..</p>";
        echo "<a href= 'search.php?q=". $query ."&type=name' style= 'text-decoration: none;'>Names</a> <a href= 'search.php?q=". $query ."&type=username' style= 'text-decoration: none;'>Usernames</a><br><br><hr>";
        while($row= mysqli_fetch_array($userReturnedQuery)){
            $user_obj = new User($sqlcon, $user['u_name']);
            $button = "";
            $mutual_friends = "";
            if($user['u_name'] != $row['u_name']){
                // Generating button depending on friendship status 
                if($user_obj->isFriend($row['u_name'])){
                    $button = "<button type= 'submit' name= '". $row['u_name'] ."' class= 'friend_button'>Remove Friend</button>";
                }
                else if($user_obj->didReceiveRequest($row['u_name'])){
                    $button = "<button type= 'submit' name= '". $row['u_name'] ."' class= 'friend_button'>Respond to request</button>";
                }
                else if($user_obj->didSendRequest($row['u_name'])){
                    $button = "<button type= 'submit' class= 'friend_button'>Request sent</button>";
                }
                else{
                    $button = "<button type= 'submit' name= '". $row['u_name'] ."' class= 'friend_button'>Add Friend</button>";
                }
                
                $mutual_friends = $user_obj->getMutualFriends($row['u_name'])." Mutual friends";
                
                //Button forms
                if(isset($_POST[$row['u_name']])){
                    if($user_obj->isFriend($row['u_name'])){
                        $user_obj->removeFriend($row['u_name']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }
                    else if($user_obj->didReceiveRequest($row['u_name'])){
                        header("location: requests.php");
                    }
                    else if($user_obj->didReceiveRequest($row['u_name'])){
                        
                    }
                    else{
                        $user_obj->sendRequest($row['u_name']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }
                }
            }
            
            echo "<div class= 'search_result'>
                    <div class= 'searchPageFriendsButton'>
                       <form action= '' method= 'post'>". $button ."<br></form>
                    </div>
                    <a href= '". $row['u_name'] ."' style= 'text-decoration: none;'>
                    <img class= 'result_profile_pic' src= 'profile_pictures/" . $row['profile_pic'] . "' width='45px' height='45px' style= 'border-radius: 5px; margin-right: 5px;'>
                    </a>
                    <a href= '". $row['u_name'] ."' style= 'text-decoration: none;'>". $row['u_fname'] ." ". $row['u_lname'] ."<br>
                    <span id= 'grey'>". $row['u_name'] ."</span>
                    </a><br>
                    <span id= 'grey'>". $mutual_friends ."</span>
                  </div>";
        }
    }
    ?>
</div>
