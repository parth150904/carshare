<?php 
session_start();

unset($_SESSION['id']);

if (!isset($_SESSION['id']))
{
	echo"<script type='text/javascript'>window.location.href = 'login.php';</script>";
}
else {
	echo"<script type='text/javascript'>window.location.href = 'profile-page.php';</script>";
}
?>