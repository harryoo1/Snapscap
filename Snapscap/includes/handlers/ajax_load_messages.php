<?php
include("../db_connection.php");
include("../classes/User.php");
include("../classes/Message.php");

$limit = 7;

$message = new Message($sqlcon, $_REQUEST['userLoggedIn']);
echo $message->getConvosDropdown($_REQUEST, $limit);
?>