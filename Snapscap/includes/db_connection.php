<?php

ob_start(); //turns on output buffering
$timezone = date_default_timezone_set("Asia/Kolkata");
$sqlcon = mysqli_connect("localhost","snaphari_harish","!43=!lOveyOu","snaphari_snaps") or die("oops! Cannot connect to the server..");

?>