<?php 
include("includes/header.php");

if(isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    $username_chat = lcfirst($_GET['profile_username']);
    $user_details_query = "SELECT * FROM `users` WHERE `u_name` = '$username' LIMIT 1";
    $result = mysqli_query($sqlcon, $user_details_query);
    $get_individual_data = mysqli_fetch_assoc($result);
    
    $user_first_name = $get_individual_data["u_fname"];
    $user_last_name = $get_individual_data["u_lname"];
    $user_gender = $get_individual_data["u_gender"];
    $user_email = $get_individual_data["u_email"];
    $user_phone = $get_individual_data["u_phone"];
    $joining_date = $get_individual_data["date"];
    $posts_count = $get_individual_data["num_posts"];
    $likes_count = $get_individual_data["num_likes"];
    $account_status = $get_individual_data["status"];
    $user_profile_pic = $get_individual_data["profile_pic"];
    $num_friends = (substr_count($get_individual_data['friend_array'], ",")) - 1;
}

$logged_in_user_object = new User($sqlcon, $user_loggedin);

if(isset($_POST['remove_friend'])){
    $user = new User($sqlcon, $user_loggedin);
    $user->removeFriend($username);
}
if(isset($_POST['respond_friend'])){
    header("location: requests.php");
}
if(isset($_POST['add_friend'])){
    $user = new User($sqlcon, $user_loggedin);
    $user->sendRequest($username);
}

//session_destroy();
 $message_obj = new Message($sqlcon, $user_loggedin);
 if(isset($_POST['post_message'])){
   if(isset($_POST['message_body'])){
       $body = mysqli_real_escape_string($sqlcon, $_POST['message_body']);
       $date = date("y-m-d H:i:s");
       $message_obj->sendMessage($username_chat, $body, $date);
   }
       $link = '#myTab a[href="#messages_div"]';
       echo "<script>
               $(function(){
                 $('".$link."').tab('show');
               });
             </script>";
 }
?>
<div class="container">
    <div class="row navbar-expand-lg">
        <div class="col-md-3">
            <button class="navbar-toggler profile_collaps_btn" type="button" data-toggle="collapse" data-target="#navbarResponsive1" aria-controls="navbarResponsive1" aria-expanded="false" aria-label="Toggle navigation">
                <span>Profile <i class="fa fa-caret-square-o-down"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive1">
                <div class="card user_details">
                    <a href="<?php echo $username;?>">
                        <?php echo "<div id='my_profile_holder'>";
                           if($user_profile_pic == ""){
                           echo "<img class='user_picture' src='profile_pictures/default.png'/>";
                           }else{
                           echo "<img class='user_picture' src='profile_pictures/$user_profile_pic'/>";
                           }
                           echo "</div>"; 
                         ?>
                    </a>
                    <div class="card-body">
                        <?php echo "<div id='details_holder'>";

                      echo "<span><strong class='heading-style'>Name :</strong>  <span class='word-styling'>".$user_first_name." &nbsp;".$user_last_name."</span></span><hr>";
                      echo "<span><strong class='heading-style'>Gender :</strong>  <span class='word-styling'>".$user_gender."</span></span><hr>";
                      echo "<span><strong class='heading-style'>Email :</strong>  <span class='word-styling'>".$user_email."</span></span><hr>";
                      if($user_loggedin != $username){
                         if($logged_in_user_object->isFriend($username)){    
                           echo "<span><strong class='heading-style'>Phone :</strong>  <span class='word-styling'>".$user_phone."</span></span><hr>";
                         }
                      }
                      echo "<span><strong class='heading-style'>Activation Date :</strong>  <span class='word-styling'>".$joining_date."</span></span><hr>";
                      echo "<span><strong class='heading-style'>Total Posts :</strong>  <span class='word-styling'>".$posts_count."</span></span><hr>";
                      echo "<span><strong class='heading-style'>Total Likes :</strong>  <span class='word-styling'>".$likes_count."</span></span><hr>";
                      echo "<span><strong class='heading-style'>Account Status :</strong>  <span class='word-styling'>".$account_status."</span></span><hr>";
                      echo "<span><strong class='heading-style'>Friends :</strong>  <span class='word-styling'>".$num_friends."</span></span>";
                      echo "</div>";
                    ?>
                    </div>
                    <div>
                        <form action="<?php echo $username;?>" style="text-align: center;" method="post">
                            <?php
                               $profile_user_object = new User($sqlcon, $username);
                            
                               if($profile_user_object->isClosed()){
                                   header("location: user_closed.php");
                               }
                            
                               if($user_loggedin != $username){
                                   
                                   if($logged_in_user_object->isFriend($username)){
                                      echo '<button type="submit" name="remove_friend" class="friend_button">Remove Friend</button><br>'; 
                                   }
                                   else if($logged_in_user_object->didReceiveRequest($username)){
                                       echo '<button type="submit" name="respond_friend" class="friend_button">Accept Request</button><br>';
                                   }
                                   else if($logged_in_user_object->didSendRequest($username)){
                                       echo '<button type="submit" name="" class="friend_button">Request sent</button><br>';
                                   }
                                   else{
                                       echo '<button type="submit" name="add_friend" class="friend_button">Add Friend</button><br>';
                                   }
                               }
                              ?>

                        </form>
                    </div>
                    <?php 
                    if($user_loggedin != $username){
                        echo '<div class="profile_info_bottom">';
                        echo "<span><strong class='heading-style'>Mutual Friends :</strong>  <span class='word-styling'>".$logged_in_user_object->getMutualFriends($username)."</span></span><hr>";
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-9" style="background-color: #fff; padding-top: 5px;">

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" id="timeline-tab" data-toggle="tab" href="#newsfeed_div" role="tab" aria-controls="newsfeed_div" aria-selected="true">Timeline</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" id="chat-tab" data-toggle="tab" href="#messages_div" role="tab" aria-controls="messages_div" aria-selected="false">Chat</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent" style="margin-top: 20px;">
                <div class="tab-pane fade" id="newsfeed_div" role="tabpanel" aria-labelledby="timeline-tab">
                    <div class="card">
                        <div class="card-header">
                        </div>
                        <div class="card-body">
                            <input type="submit" id="myMod_p" class="form-control post_form_btn" value="Write something to <?php echo $username;?>..." data-toggle="modal" data-target="myModal_p">
                        </div>
                    </div>
                    <div class="post_area"></div>
                    <div style="text-align: center;">
                        <img id="loading" src="images/post_loader.gif" style="width: 30px; height: 30px; ">
                    </div>
                </div>

                <div class="tab-pane fade show active" id="messages_div" role="tabpanel" aria-labelledby="chat-tab">
                    <div class="main_Chat_Box">
                        <?php
                            echo "<h5><a href='".$username_chat."' style='text-decoration: none;'><img src='profile_pictures/" . $profile_user_object->getProfilePic() . "' width='40px' height='40px' style= 'border-radius: 25px; margin-right: 5px; object-fit: cover;'>
                            <span style='font-size: 17px; color: #8c8c8c;'>".$profile_user_object->getFirstAndLastName()."</span></a></h5><hr><br>";
                            echo "<div id='get_chat_logs'>";
                            echo $message_obj->getMessage($username_chat);
                            echo "</div>";
                        ?>
                        <div class="post_message">
                            <form action="" method="post">
                                <div class='input-group'>
                                    <input type='text' name='message_body' id='message_textarea' class='form-control' placeholder='Write your message...' autofocus>
                                    <div class='input-group-append'>
                                        <button type='submit' name='post_message' class='input-group-text' id='message_submit'><i class='fa fa-paper-plane'></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <script>
                            var div = document.getElementById("get_chat_logs");
                            div.scrollTop = div.scrollHeight;

                            $(document).ready(function() {
                                $("#message_submit").click(function() {
                                    location.reload(true);
                                });
                            });

                        </script>
                    </div>
                </div>
            </div>

            <div id="myModal_p" class="modal" aria-labelledby="postModalLabel">
                <div class="p-form-container">
                    <div class="card card-register mx-auto mt-5">
                        <div class="card-header"><span class="close_p">&times;</span></div>
                        <div class="card-body">
                            <form action="" method="post" class="profile_post">
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <?php include("get-dp.php");?>
                                        <textarea name="post_text" id="post_form" placeholder="Write something here..." class="form-control post_form" style="overflow-y: hidden;"></textarea>
                                        <input type="hidden" name="user_from" value="<?php echo $user_loggedin;?>">
                                        <input type="hidden" name="user_to" value="<?php echo $username;?>">
                                    </div>
                                </div>
                                <button type="submit" id="submit_profile_post" class="post_btn btn btn-block">Post</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                var modal_p = document.getElementById("myModal_p");
                var a = document.getElementById("myMod_p");
                var span = document.getElementsByClassName("close_p")[0];

                a.onclick = function() {
                    modal_p.style.display = "block";
                }
                span.onclick = function() {
                    modal_p.style.display = "none";
                }
                window.onclick = function(event) {
                    if (event.target == modal_p) {
                        modal_p.style.display = "none";
                    }
                }

            </script>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        //Button for submit post
        $('#submit_profile_post').click(function() {

            $.ajax({
                type: "POST",
                url: "includes/handlers/ajax_submit_profile_post.php",
                data: $('form.profile_post').serialize(),
                success: function(msg) {
                    $("#myModal_p").modal('hide');
                    location.reload();
                },
                error: function() {
                    alert('failure');
                }
            });
        });
    });

    var userLoggedIn = '<?php echo $user_loggedin;?>';
    var profileUsername = '<?php echo $username;?>';
    $(document).ready(function() {
        $('#loading').show();
        //Ajax requst for loading posts..

        $.ajax({
            url: "includes/handlers/ajax_load_profile_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
            cache: false,

            success: function(data) {
                $('#loading').hide();
                $('.post_area').html(data);
            }
        });

        $(window).scroll(function() {
            var height = $('.post_area').height();
            var scroll_top = $(this).scrollTop();
            var page = $('.post_area').find('.nextPage').val();
            var noMorePosts = $('.post_area').find('.noMorePosts').val();

            if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                $('#loading').show();
                alert('Check');
                var ajaxReq = $.ajax({
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                    cache: false,

                    success: function(response) {
                        $('.post_area').find('.nextPage').remove(); // removes current next page
                        $('.post_area').find('.noMorePosts').remove(); // removes current next page

                        $('#loading').hide();
                        $('.post_area').append(response);
                    }
                });
            } // end if
            return false;
        }); // end (window).scroll(function()
    });

</script>
</body>

</html>
