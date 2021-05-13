<?php
require ("db_connection.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");

if(isset($_COOKIE['user_name'])){
    $user_loggedin = $_COOKIE['user_name'];
    $user_details_query = "SELECT * FROM `users` WHERE `u_name`='$user_loggedin'";
    $user_details_query_result = mysqli_query($sqlcon,$user_details_query);
    $user = mysqli_fetch_array($user_details_query_result);
}
else{
    header("location: login.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <script data-ad-client="ca-pub-5326947347718784" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" type="text/css" href="css/Bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/main/main.css">
    <link rel="stylesheet" type="text/css" href="css/style/style.css">
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootbox.min.js"></script>
    <script src="js/snapbook.js"></script>
    <title>snapscap.com</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-nav" style="min-width: 360px; min-height: 94px;">
        <div class="container-fluid">
            <h3><a href="index.php" class="logo" style="color: aliceblue; text-decoration: none;">SnapScap<span style="color: #000; text-decoration: none; font-family: 'Lobster', serif; font-size: 23px;">.com</span></a></h3>

            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="fa fa-bars" style="color: #fff;"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <div class="live_search">
                    <form action="search.php" method="get" name="search_form" style="margin-right: 50px; min-width: 304px;">
                        <input type="text" name="q" class="form-control" id="search_text_input" onkeyup="getLiveSearchUsers(this.value, '<?php echo $user_loggedin;?>')" placeholder="Search here..." autocomplete="off">
                        <button class="live_search_btn btn btn-outline-success" type="submit" style="height: 35px;">
                            <span class="fa fa-search" style="font-size: 25px;"></span>
                        </button>
                    </form>
                    <div style="position: absolute; z-index: 1;">
                        <div class="search_results"></div>
                        <div class="search_results_footer_empty" style="position: absolute;"></div>
                    </div>
                </div>
                <div class="ml-auto" id="top-navigation-username" style="min-width: 174px;">
                    <span id="my_profile_picture"></span>
                    <div id="dp_form_holder">
                        <form method="post" action="index.php" enctype="multipart/form-data">
                            <input type="file" name="dp-file" id="dp-file" />
                            <input type="submit" name="submit_file" id="submit_file" />
                        </form>
                    </div>
                    <?php
                        if(isset($_POST["submit_file"]))
                        {
                        include("db_connection.php");
                        $selected_username = $_COOKIE["user_name"];
                        $upload_file_dir = "profile_pictures/";
                        $file_tmp_path = $_FILES["dp-file"]["tmp_name"];
                        $file_name = $_FILES["dp-file"]["name"];
                        $path = $upload_file_dir.$file_name;
                        move_uploaded_file($file_tmp_path,$path);
                        $update_query = "UPDATE `users` SET `profile_pic` = '$file_name' WHERE `u_name` = '$selected_username'";
                        $result = mysqli_query($sqlcon,$update_query);
                        }
                     ?>
                    <span style="color: #ffffff; font: normal 20px; margin: 0;">Welcome </span>
                    <span style="margin-top:3px; color: #ffffff; font-family: 'Lobster', serif;"><?php echo ucfirst($user_loggedin);?></span>
                </div>
                <div style="min-width: 323px;">
                    <ul class="navbar nav ml-auto">
                        <li class="dropdown" style="padding-left: 0;">
                            <a class="nav-link  dropdown-toggle" id="dropdown01" style="color: #fff; margin-right: 10px; cursive; cursor: pointer;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Account</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown01">
                                <a class="dropdown-item nav-link" style="font: normal 15px 'Cookie', sans-serif; color: #6c757d;" href="profile.php?profile_username=<?php echo $user_loggedin;?>"><i class="fa fa-user" style="color: #1e90ff; margin-right:15px; margin-left: 10px;"></i>My Profile</a>
                                <a class="dropdown-item nav-link" style="font: normal 15px 'Cookie', sans-serif; color: #6c757d;" href="settings.php"><i class="fa fa-briefcase" style="color: #1e90ff; margin-right:15px; margin-left: 10px;"></i>Account Setting</a>
                                <a class="dropdown-item nav-link" style="font: normal 15px 'Cookie', sans-serif; color: #6c757d;" href="friend_list.php"><i class="fa fa-heart" style="color: #1e90ff; margin-right:15px; margin-left: 10px;"></i>Friends</a>
                                <a class="dropdown-item nav-link" style="font: normal 15px 'Cookie', sans-serif; color: #6c757d;" href="logout.php"><i class="fa fa-power-off" style="color: #1e90ff; margin-right:15px; margin-left: 10px;"></i>Logout</a>
                            </div>
                        </li>
                        <li>
                            <a class="nav-icons" href="index.php"><i class="fa fa-home"></i></a>
                        </li>
                        <?php 
                        // unread messages
                        $messages = new Message($sqlcon, $user_loggedin);
                        $num_messages = $messages->getUnreadNumber();
                        
                        // unread notifications
                        $notification = new Notification($sqlcon, $user_loggedin);
                        $num_notification = $notification->getUnreadNumber();
                        
                        // Requests count
                        $user_obj = new User($sqlcon, $user_loggedin);
                        $num_requests = $user_obj->getNumberOfFriendRequests();
                        
                        ?>
                        <li>
                            <a class="nav-icons" href="javascript:void(0);" onclick='getDropdownData("<?php echo $user_loggedin; ?>", "message")'>
                                <i class="fa fa-envelope"></i>
                                <?php
                                if($num_messages > 0){
                                    echo '<span class="notification_badge" id="unread_messages">' .$num_messages. '</span>';
                                }
                                ?>
                            </a>
                        </li>
                        <li>
                            <a class="nav-icons" href="javascript:void(0);" onclick='getDropdownData("<?php echo $user_loggedin; ?>", "notification")'>
                                <i class="fa fa-bell"></i>
                                <?php
                                if($num_notification > 0){
                                    echo '<span class="notification_badge" id="unread_notifications">' .$num_notification. '</span>';
                                }
                                ?>
                            </a>
                        </li>
                        <li>
                            <a class="nav-icons" href="requests.php"><i class="fa fa-users"></i>
                                <?php
                                if($num_requests > 0){
                                    echo '<span class="notification_badge" id="unread_requests">' .$num_requests. '</span>';
                                }
                                ?>
                            </a>
                        </li>
                        <li>
                            <a class="nav-icons" href="settings.php"><i class="fa fa-cogs"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="dropdown_message_window"></div>
    <input type="hidden" id="dropdown_message_type" value="">

    <script>
        //-----------------Live Users Search------------------//
        $(document).ready(function() {
            /*$('#search_text_input').focus(function() {
                if (window.matchMedia("(min-width: 800px)").matches) {
                    $(this).animate({
                        width: '250px'
                    }, 600);
                }
            });*/
            $('.live_search_btn').on('click', function() {
                document.search_form.submit();
            });
        });

        $(document).click(function(e) {
            if (e.target.class != "search_results" && e.target.id != "search_text_input") {
                $(".search_results").html("");
                $('.search_results_footer').html("");
                $('.search_results_footer').toggleClass("search_results_footer_empty");
                $('.search_results_footer').toggleClass("search_results_footer");
            }

            if (e.target.class != "dropdown_message_window") {
                $(".dropdown_message_window").html("");
                $(".dropdown_message_window").css({
                    "padding": "0px",
                    "height": "0px"
                });
            }
        });

        function getLiveSearchUsers(value, user) {
            $.post("includes/handlers/ajax_live_search.php", {
                query: value,
                userLoggedIn: user
            }, function(data) {
                if ($('.search_results_footer_empty')[0]) {
                    $('.search_results_footer_empty').toggleClass("search_results_footer");
                    $('.search_results_footer_empty').toggleClass("search_results_footer_empty");
                }
                $('.search_results').html(data);
                $('.search_results_footer').html("<a href='search.php?q=" + value + "'>See all results..");
                if (data == "") {
                    $('.search_results_footer').html("");
                    $('.search_results_footer').toggleClass("search_results_footer_empty");
                    $('.search_results_footer').toggleClass("search_results_footer");
                }
            });
        }
        //----------Profile picture in navigation bar---------//
            $(document).ready(function() {
                $("#my_profile_picture").load("get-dp.php");
                $("#my_profile_picture").click(function() {
                    $("#dp-file").trigger("click");
                });
                $("#dp-file").change(function() {
                    $("#submit_file").trigger("click");
                });
                $("#submit_file").click(function() {
                    $(this).submit();
                });
                $("#submit_file").submit(function() {
                    $("#my_profile_picture").load("get-dp.php");
                });
            });
        //----------Message-box dropdown-----------//
        function getDropdownData(user, type) {
            if ($(".dropdown_message_window").css("height") == "0px") {

                var pageName;

                if (type == 'notification') {
                    pageName = "ajax_load_notifications.php";
                    $("span").remove("#unread_notifications");
                } else if (type == 'message') {
                    pageName = "ajax_load_messages.php";
                    $("span").remove("#unread_messages");
                }
                var ajaxreq = $.ajax({
                    url: "includes/handlers/" + pageName,
                    type: "POST",
                    data: "page=1&userLoggedIn=" + user,
                    cache: false,

                    success: function(response) {
                        $(".dropdown_message_window").html(response);
                        $(".dropdown_message_window").css({
                            "padding": "0px",
                            "height": "380px",
                            "border": "1px solid rgba(0, 0, 0, 0.2)"
                        });
                        $("#dropdown_message_type").val(type);
                    }
                });
            } else {
                $(".dropdown_message_window").html("");
                $(".dropdown_message_window").css({
                    "padding": "0px",
                    "height": "0px",
                    "border": "none"
                });
            }
        }
        //--------infinite scrolling of new messages-------------//
        var userLoggedIn = '<?php echo $user_loggedin; ?>';
        $(document).ready(function() {
            $('.dropdown_message_window').scroll(function() {
                var inner_height = $('.dropdown_message_window').innerHeight();
                var scroll_top = $('.dropdown_message_window').scrollTop();
                var page = $('.dropdown_message_window').find('.nextPageDropdownData').val();
                var noMoreData = $('.dropdown_message_window').find('.noMoreDropdownData').val();

                if ((scroll_top + inner_height >= $('.dropdown_message_window')[0].scrollHeight) && noMoreData == 'false') {

                    var pageName;
                    var type = $('#dropdown_message_type').val();
                    if (type == 'notification') {
                        pageName = "ajax_load_notifications.php";
                        $("span").remove("#unread_notifications");
                    } else if (type == 'message') {
                        pageName = "ajax_load_messages.php";
                        $("span").remove("#unread_messages");
                    }

                    var ajaxReq = $.ajax({
                        url: "includes/handlers/" + pageName,
                        type: "POST",
                        data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                        cache: false,

                        success: function(response) {
                            $('.dropdown_message_window').find('.nextPageDropdownData').remove(); // removes current next page
                            $('.dropdown_message_window').find('.noMoreDropdownData').remove(); // removes current next page

                            $('.dropdown_message_window').append(response);
                        }
                    });
                } // end if
                return false;
            }); // end (window).scroll(function()
        });

    </script>

    <hr style="margin-top: 100px;">
