<?php
include("../db_connection.php");
include("../classes/User.php");
include("../classes/Notification.php");

$limit = 7;

$notification = new Notification($sqlcon, $_REQUEST['userLoggedIn']);
echo $notification->getNotifications($_REQUEST, $limit);
?>