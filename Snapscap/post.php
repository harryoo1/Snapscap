<?php
include("includes/header.php");

if(isset($_GET['id'])){
    $id = $_GET['id'];
}
else{
    $id = 0;
}
?>
<div class="container">
    <div>
        
    </div>
    <div class="post_area">
        <?php
        $post = new Post($sqlcon, $user_loggedin);
        $post->getSinglePost($id);
        ?>
    </div>
</div>
