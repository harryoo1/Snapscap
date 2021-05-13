<?php
include("../db_connection.php");
include("../classes/User.php");
include("../classes/Post.php");

$limit = 10; //number of posts loaded each time
$post = new Post($sqlcon, $_REQUEST['userLoggedIn']);
$post->loadPostsFriends($_REQUEST, $limit);
?>