<?php
include("../db_connection.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);
//-------if query contains an Underscore_, assume user is searching for username-----//
if(strpos($query, '_') !== false){
    $userReturnedQuery = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE `u_name` LIKE '$query%' AND `status`= 'active' LIMIT 8");
}
//-------if there are two words, assume they are first and the last name-----------//
else if(count($names) == 2){
    $userReturnedQuery = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE (`u_fname` LIKE '$names[0]%' AND `u_lname` LIKE '$names[1]%') AND `status`= 'active' LIMIT 8");
}
//-------if query has one word only, search first or last name-------------------//
else{
    $userReturnedQuery = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE (`u_fname` LIKE '$names[0]%' OR `u_lname` LIKE '$names[0]%') AND `status`= 'active' LIMIT 8");
}

if($query != ""){
    while($row = mysqli_fetch_array($userReturnedQuery)){
        $user = new User($sqlcon, $userLoggedIn);
        if($row['u_name'] != $userLoggedIn){
            $mutual_friends = $user->getMutualFriends($row['u_name'])." Mutual friends";
        }
        else{
            $mutual_friends = "";
        }
        echo "<div class= 'result_display'>
              <a class= 'nav-link' href= '". $row['u_name'] ."'>
              <div class= 'live_search_profile_pic'>
              <img src='profile_pictures/" . $row['profile_pic'] . "' width='25px' height='25px' style= 'border-radius: 25px; margin-right: 5px; object-fit: cover;'>
              </div>
              <div class= 'live_search_text' style='font-size: 12px; color: dodgerblue'>". $row['u_fname'] ." ". $row['u_lname'] ."</div>
              <p style='font-size: 12px; color: #8c8c8c'>". $row['u_name'] ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <span style='font-size: 11px; color: #8c8c8c;'>". $mutual_friends ."</span></p>
              </a>
              </div>";
    }
}

?>