<?php
	$server_198 = "some_info_198";
	$usename_198 = "some_info_198";
	$password_198 = "some_info_198";
	$db_in_198 = "VoiceReport";
	$connect_server_198 = mysqli_connect($server_198, $usename_198, $password_198, $db_in_198);
	
	if (!$connect_server_198) {
	  die("Connection failed: " . mysqli_connect_error());
	}
	echo "Connected successfully: connect_server_198 \n";


	$server_local = "some_info_local";
	$usename_local = "some_info_local";
	$password_local = "some_info_local";
	$db_in_local = "ReportDaily";
	$connect_local = mysqli_connect($server_local, $usename_local, $password_local, $db_in_local);
	
	if (!$connect_local) {
	  die("Connection failed: " . mysqli_connect_error());
	}
	echo "Connected successfully: connect_local \n";

	$apiToken= "some_api_in_here";
	$chat_id = "@some_channel_in_here";
?>
