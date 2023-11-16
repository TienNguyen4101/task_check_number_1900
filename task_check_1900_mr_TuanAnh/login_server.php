<?php
	$server_198 = "183.91.185.198";
	$usename_198 = "User99";
	$password_198 = "Ngoctien9";
	$db_in_198 = "VoiceReport";
	$connect_server_198 = mysqli_connect($server_198, $usename_198, $password_198, $db_in_198);
	
	if (!$connect_server_198) {
	  die("Connection failed: " . mysqli_connect_error());
	}
	echo "Connected successfully: connect_server_198 \n";


	$server_local = "localhost";
	$usename_local = "User99";
	$password_local = "Chiru@123";
	$db_in_local = "ReportDaily";
	$connect_local = mysqli_connect($server_local, $usename_local, $password_local, $db_in_local);
	
	if (!$connect_local) {
	  die("Connection failed: " . mysqli_connect_error());
	}
	echo "Connected successfully: connect_local \n";

	$apiToken= "6369084280:AAFxZhzm4CrUznvj7ZAENCznn3AR-JKqg7M";
	$chat_id = "@test_message123";
?>
