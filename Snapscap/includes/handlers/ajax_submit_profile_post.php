<?php 
require ("../db_connection.php");
include("../classes/User.php");
include("../classes/Post.php");
include("../classes/Notification.php");

if(isset($_POST['post_text'])){
    $post = new Post($sqlcon, $_POST['user_from']);
    $post->submitPost($_POST['post_text'], $_POST['user_to'], '');
}

?>