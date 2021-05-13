<head>
    <style>
        .resultDisplay {
            padding: 5px 5px 0px 5px;
            height: auto;
            border-bottom: 1px solid  #d3d3d3;
        }
        .resultDisplay:hover{
            background-color: #d9d9d9;
            text-decoration: none;
        }
    </style>
</head>
<?php 
include("../db_connection.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];
$names = explode(" ", $query);

if(strpos($query, "_") !== false){
    $userReturned = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE `u_name` LIKE '$query%' AND `status`= 'active' LIMIT 8");
}
else if(count($names) == 2){
    $userReturned = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE (`u_fname` LIKE '%$names[0]%' AND `u_lname` LIKE '%$names[1]%') AND `status`= 'active' LIMIT 8");
}
else{
    $userReturned = mysqli_query($sqlcon, "SELECT * FROM `users` WHERE (`u_fname` LIKE '%$names[0]%' OR `u_lname` LIKE '%$names[0]%') AND `status`= 'active' LIMIT 8");
}
if($query != ""){
    while($row = mysqli_fetch_array($userReturned)){
        $user = new User($sqlcon, $userLoggedIn);
        if($row['u_name'] != $userLoggedIn){
            $mutual_friends = $user->getMutualFriends($row['u_name']) . "  Mutual friends";
        }
        else{
            $mutual_friends = "";
        }
        
        if($user->isFriend($row['u_name'])){
            echo "<div class='resultDisplay'>
                   <a href='messages.php?u=".$row['u_name']."' style='text-decoration: none;'>
                   <div style='margin: 1px 12px 0px 2px; float: left;'>
                   <img src='profile_pictures/".$row['profile_pic']."' width='35px' height='35px' style= 'border-radius: 25px; margin: 1px 12px 0px 2px; float: left;'>
                   </div>
                   <div>
                   <span style='font-size: 12px; color: dodgerblue'>".$row['u_fname']." ".$row['u_lname']."</sapn>
                   <p style='font-size: 11px; color: #8c8c8c;'>".$mutual_friends."</p>
                   </div>
                   </a>
                  </div>";
        }
    }
}

?>
