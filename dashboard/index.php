<?php
	session_start();
	$role = $_SESSION['role'];
	if($role == "Reseller")
	{
		header("Location: reseller/keys");
		exit();
	}
	else
	{
		header("Location: app/licenses");
		exit();
	}
?>