<?php 
include("includes/header.php");
?>
<div class="container">
    <div class="main-container" style="max-height: 500px; overflow-y: scroll;">
        <h4>Friend Requests</h4>
         <hr>
        <?php 
        $query = mysqli_query($sqlcon, "SELECT * FROM `friend_requests` WHERE `user_to` = '$user_loggedin'");
        
        if(mysqli_num_rows($query) == 0){
            echo "You have no Friend Requests !";
            
        }
        else{
            while($row = mysqli_fetch_array($query)){
                $user_from = $row['user_from'];
                $user_from_obj = new User($sqlcon, $user_from);
                
                echo $user_from_obj->getFirstAndLastName()." "."sent you a Friend Request";
                
                $user_from_friend_array = $user_from_obj->getFriendArray();
                
                if(isset($_POST['accept_request'.$user_from])){
                    $add_friend_query = mysqli_query($sqlcon, "UPDATE `users` SET `friend_array`= CONCAT(friend_array, '$user_from,') WHERE u_name = '$user_loggedin'");
                    $add_friend_query = mysqli_query($sqlcon, "UPDATE `users` SET `friend_array`= CONCAT(friend_array, '$user_loggedin,') WHERE u_name = '$user_from'");
                    
                    $delete_query = mysqli_query($sqlcon, "DELETE FROM `friend_requests` WHERE `user_to` = '$user_loggedin' AND `user_from` = '$user_from'");
                    echo "You are now friends !";
                    header("location: requests.php");
                }
                if(isset($_POST['remove_request'.$user_from])){
                    $delete_query = mysqli_query($sqlcon, "DELETE FROM `friend_requests` WHERE `user_to` = '$user_loggedin' AND `user_from` = '$user_from'");
                    echo "Requests removed !";
                    header("location: requests.php");
                }
                ?>
        <form action="requests.php" method="post" style="margin-top: 10px;">
            <button type="submit" name="accept_request<?php echo $user_from;?>" class="btn btn-outline-success" style="margin-right: 20px;">Confirm</button>
            <button type="submit" name="remove_request<?php echo $user_from;?>" class="btn btn-outline-danger">Delete</button>
        </form>
        <hr>
        <?php
            }
        }
        ?>

    </div>
</div>
</body>

</html>
