<?php

function execute_send_message_telegram($chat_id , $apiToken , $output)
{
	$data = [
		'chat_id' => $chat_id,
		'text' => $output,
	];
	$response = file_get_contents("https://api.telegram.org/bot".$apiToken."/sendMessage?" . http_build_query($data));
	$responseJson = json_decode($response);
	return $responseJson;
}

function send_excel_telegram($apiToken, $chat_id , $file_path , $file_name , $title)
{
	$excel_file_path = new CURLFile($file_path . $file_name);
	$caption = '';
	$caption .= $title;
	$data = [
		'chat_id' => $chat_id, 
		'document' => $excel_file_path,
		'caption' => $caption ,
	];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,"https://api.telegram.org/bot".$apiToken."/sendDocument");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	curl_close ($ch);
	$responseJson = json_decode($response);
	if($responseJson->ok == true) {
		echo "Đã gửi thành công báo cáo " . $chat_id . "\n";
		return TRUE;
	}
}

?>