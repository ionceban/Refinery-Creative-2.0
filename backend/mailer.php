<?php
	$to = 'info@therefinerycreative.com';
	$first_name = $_POST['fname'];
	$last_name = $_POST['lname'];
	$from = $_POST['yemail'];
	$message = $_POST['message'];
	$subject = "Refinery Message";
	$headers = 'From: "' . $first_name  . ' ' . $last_name . '" <' . $from . '>';
	if (!mail($to, $subject, $message, $headers)){
		die("failed");
	}
	
	if ($_POST['self_message']){
		if (!mail($from, $subject, $message, $headers)){
			die("failed");
		}
	}
	
	echo "success";
?>