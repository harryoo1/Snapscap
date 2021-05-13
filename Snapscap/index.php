<?php 
include("includes/header.php");


if(isset($_POST['submit_post'])){
    
    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $errorMessage = "";
    
    if($imageName != ""){
        $targetDir = "images/posts/";
        $imageName = $targetDir.uniqid().basename($imageName);
        $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);
        
        if($_FILES['fileToUpload']['size'] > 10000000){
            $errorMessage = "Sorry ! file size exceeds 10 MB";
            $uploadOk = 0;
        }
        if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "png"){
            $errorMessage = "Check the file type !";
            $uploadOk = 0;
        }
        
        if($uploadOk){
            if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)){
    
            }
            else{
                $uploadOk = 0;
            }
        }
    }
    
    if($uploadOk){
        $post = new Post($sqlcon, $user_loggedin);
        $post->submitPost($_POST['post_text'], 'none', $imageName);
        header("location: index.php");
    }
    else{
        echo "<div style='text-align: center;' class='alert alert-danger'>
                 $errorMessage;
              </div>";
    }
}
//session_destroy();

$message_obj = new Message($sqlcon, $user_loggedin);

if(isset($_GET['u'])){
    $user_to = $_GET['u'];
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
        <div class="col-sm-3">
            <button class="navbar-toggler side_panel_btn" type="button" data-toggle="collapse" data-target="#navbarResponsive2" aria-controls="navbarResponsive2" aria-expanded="false" aria-label="Toggle navigation">
                <span><i class="fa fa-bars"></i></span>
            </button>
            <div class="collapse navbar-collapse side_panel" id="navbarResponsive2">
                <div class="card" style="border: none;">
                    <div class="card-body">
                        <ul class="ml-auto">
                            <li>
                                <a class="list_items" href="index.php"><i class="fa fa-home" style="margin-right: 5px;"></i>Home</a>
                            </li>
                            <li>
                                <?php echo "<a class= 'list_items' href= 'profile.php?profile_username=$user_loggedin'><i class='fa fa-user' style='margin-right: 5px;'></i>My Profile</a>" ?>
                            </li>
                            <li>
                                <?php echo "<a class= 'list_items' href= 'messages.php?u=new'><i class='fa fa-envelope' style='margin-right: 5px;'></i>Messanger</a>" ?>
                            </li>
                            <li>
                                <a class="list_items" href="settings.php"><i class="fa fa-cogs" style="margin-right: 5px;"></i>Account Setting</a>
                            </li>
                            <li>
                                <a class="list_items" href="index.php"><i class="fa fa-power-off" style="margin-right: 5px;"></i>Logout</a>
                            </li>
                        </ul>
                        <div style="text-align: center; border-bottom: 1px solid #d3d3d3; margin-bottom: 5px;">
                            <h6>Friends online</h6>
                        </div>
                        <div class="online_users_list"><span id="myMod_m"><?php echo $message_obj->getOnlineConvos();?></span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="card" style="margin-bottom: 2px;">
                <div class="card-header post_btn_card-header">
                    Create Post
                </div>
                <div class="card-body">
                    <input type="submit" id="myMod_s" class="form-control post_form_btn" value="Write something here...">
                </div>
            </div>
            <div id="myModal_s" class="modal">
                <div class="s-form-container">
                    <div class="card card-register mx-auto mt-5">
                        <div class="card-header post_btn_card-header"><span style="margin-top: 40px;">Create a post</span><span class="close_s">&times;</span></div>
                        <div class="card-body">
                            <form action="index.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <?php include("get-dp.php");?>
                                        <textarea name="post_text" id="post_text" placeholder="Write something here..." class="form-control post_form" style="overflow-y: hidden;"></textarea>
                                    </div>
                                </div>
                                <div class="form-group" style="text-align: center;">
                                    <div class="form-label-group">
                                        <i class="fa fa-file-image-o" style="color: #00FF7F; font-size: 25px;">
                                            <image src="images/images.png" width="35" height="35"></image>
                                            <input type="file" name="fileToUpload" id="fileToUpload">
                                        </i>
                                    </div>
                                </div>
                                <button type="submit" name="submit_post" class="post_btn btn btn-block">Post</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                var modal_s = document.getElementById("myModal_s");
                var a = document.getElementById("myMod_s");
                var span = document.getElementsByClassName("close_s")[0];

                a.onclick = function() {
                    modal_s.style.display = "block";
                }
                span.onclick = function() {
                    modal_s.style.display = "none";
                }
                window.onclick = function(event) {
                    if (event.target == modal_s) {
                        modal_s.style.display = "none";
                    }
                }

            </script>

            <div class="post_area"></div>
            <div style="text-align: center;">
                <img id="loading" src="images/post_loader.gif" style="width: 30px; height: 30px; ">
            </div>
            <script>
                var userLoggedIn = '<?php echo $user_loggedin; ?>';
                $(document).ready(function() {
                    $('#loading').show();
                    //Ajax requst for loading posts..

                    $.ajax({
                        url: "includes/handlers/ajax_load_posts.php",
                        type: "POST",
                        data: "page=1&userLoggedIn=" + userLoggedIn,
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
                            var ajaxReq = $.ajax({
                                url: "includes/handlers/ajax_load_posts.php",
                                type: "POST",
                                data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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
        </div>
        <script>
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
</body>

</html>
