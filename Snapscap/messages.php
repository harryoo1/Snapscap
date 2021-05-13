<?php 
include("includes/header.php");

$message_obj = new Message($sqlcon, $user_loggedin);

if(isset($_GET['u'])){
    $user_to = lcfirst($_GET['u']);
}
else{
    $user_to = $message_obj->getMostRecentUser();
    if($user_to == false){
        $user_to = "new";
    }
}

if($user_to != "new"){
    $user_to_obj = new User($sqlcon, $user_to);
}

if(isset($_POST['post_message'])){
    if(isset($_POST['message_body'])){
        $body = mysqli_real_escape_string($sqlcon, $_POST['message_body']);
        $date = date("y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
    }
}

?>
<div class="container">
    <div class="row navbar-expand-lg">
        <div class="col-md-3" style="margin-bottom: 10px;">
            <button class="navbar-toggler convo_collaps_btn" type="button" data-toggle="collapse" data-target="#navbarResponsive2" aria-controls="navbarResponsive2" aria-expanded="false" aria-label="Toggle navigation">
                <span>Conversation list <i class="fa fa-caret-square-o-down"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive2">
                <div class="conversation-list" id="conversations">
                    <div class="card">
                        <div class="card-header" style="background-color: #fff;">
                            <h6>Conversations</h6>
                        </div>
                        <div class="card-body loaded_conversations">
                            <?php echo $message_obj->getConvos();?>
                        </div>
                        <div class="card-footer" style="text-align: center; background-color: ;">
                            <a href="messages.php?u=new" class="nav-link">New Message</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9" style="margin-bottom: 10px;">
            <div class="main_Chat_Box">
                <?php 
                 if($user_to != "new"){
                     echo "<h5><a href='$user_to' style='text-decoration: none;'><img src='profile_pictures/" . $user_to_obj->getProfilePic() . "' width='40px' height='40px' style= 'border-radius: 25px; margin-right: 5px; object-fit: cover;'>
                     <span style='font-size: 17px; color: #8c8c8c;'>".$user_to_obj->getFirstAndLastName()."</span></a></h5><hr><br>";
                     echo "<div id='get_chat_logs'>";
                     echo $message_obj->getMessage($user_to);
                     echo "</div>";
                 }
                 else{
                     echo "<h6>New Messages</h6>";
                 }
                 ?>

                <div class="post_message">
                    <form action="" method="post">
                        <?php 
                         if($user_to == "new"){
                             echo "<span style='font-size: 14px; margin-bottom: 20px;'>Search for friends..</span>";?>
                        <div class='input-group'>
                            <input type='text' class="form-control" onKeyup='getUser(this.value, "<?php echo $user_loggedin;?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'>
                        </div>
                        <?php echo "<div class='results'></div>";
                         }
                         else{
                             echo "<div class='input-group'>
                                   <input type='text' name='message_body' id='message_textarea' class='form-control' placeholder='Write your message...'>";
                             echo "<div class='input-group-append'>
                                   <button type='submit' name='post_message' class='input-group-text' id='message_submit'><i class='fa fa-paper-plane'></i></button>
                                   </div>
                                   </div>";
                         }
                         ?>
                    </form>
                </div>
            </div>
            <script>
                //for loading chats
                var div = document.getElementById("get_chat_logs");
                div.scrollTop = div.scrollHeight;
                
                //for new message user's search
                function getUser(value, user) {
                    $.post("includes/handlers/ajax_friend_search.php", {
                        query: value,
                        userLoggedIn: user
                    }, function(data) {
                        $(".results").html(data);
                    });
                }

            </script>
        </div>
    </div>


</div>
