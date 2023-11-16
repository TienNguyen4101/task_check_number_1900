<?php
require 'get_and_insert_data.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
require '/var/www/excel_lib/vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//============================================================================

function send_message_telegram($table, $apiToken, $chat_id, $customerCode) {
	foreach(get_and_sort_data_in_table($table) as $value) {
		if ( $customerCode == $value["CustomerCode"] ) {
			$output = "";
			$title = "Cảnh báo khách hàng:" . "\n";
			$name_customer = $value["Name"] . "\n";
			$title_customer_code = "Mã khách hàng: " . $value["CustomerCode"] . "\n"; 
			$tilte_contract_code = "Mã số hợp đồng là: " . $value["ContractCode"] . "\n";
			$reason = "Lý do tạm ngưng: " . $value["Reason"] . "\n";
			//=======================================
			$numbers = explode(" - ", $value["Number"]);
			$result = "";
			foreach ($numbers as $number) {
					$result .= $number . "\n";
			}
			//=======================================
			$Date_Barring = "Ngày tạm ngưng: " . $value["BarringDate"] . "\n";
			$ending = "Hiện đang tạm ngưng " . $value["Day_Barring"] . " ngày!" . "\n";
			$strim = "=============================" . "\n";
			$output .= $title . $name_customer . $title_customer_code . $tilte_contract_code . $result . $Date_Barring . $ending . $reason . $strim ;

			$data = [
					'chat_id' => $chat_id,
					'text' => $output,
			];
			$response = file_get_contents("https://api.telegram.org/bot".$apiToken."/sendMessage?"
																		. http_build_query($data) );
			$responseJson = json_decode($response);

			if($responseJson->ok == true) {
					echo "=======================" . "Đã gửi tin nhắn đến khách hàng: " . $value["Name"] . "=======================" ."\n";
			} else {
					echo "========================== Không có data được gửi trong tin nhắn !!!! ============================" . "\n";
			}
		} 
		else if ($customerCode == "ALL") {
			$output = "";
			$title = "Cảnh báo khách hàng:" . "\n";
			$name_customer = $value["Name"] . "\n";
			$title_customer_code = "Mã khách hàng: " . $value["CustomerCode"] . "\n"; 
			$tilte_contract_code = "Mã số hợp đồng là: " . $value["ContractCode"] . "\n";
			$reason = "Lý do tạm ngưng: " . $value["Reason"] . "\n";
			//=======================================
			$numbers = explode(" - ", $value["Number"]);
			$result = "";
			foreach ($numbers as $number) {
					$result .= $number . "\n";
			}
			//=======================================
			$Date_Barring = "Ngày tạm ngưng: " . $value["BarringDate"] . "\n";
			$ending = "Hiện đang tạm ngưng " . $value["Day_Barring"] . " ngày!" . "\n";
			$strim = "=============================" . "\n";
			$output .= $title . $name_customer . $title_customer_code . $tilte_contract_code . $result . $Date_Barring . $ending . $reason . $strim ;
	
			$data = [
					'chat_id' => $chat_id,
					'text' => $output,
			];
			$response = file_get_contents("https://api.telegram.org/bot".$apiToken."/sendMessage?"
																	. http_build_query($data) );
			$responseJson = json_decode($response);
			if($responseJson->ok == true) {
					echo "=======================" . "Đã gửi tin nhắn đến ALL".  "======================="  . "\n";
					
			} else echo "==========================Không có data được gửi trong tin nhắn !!!! ============================" . "\n";
		}
	}
}


function send_excel_telegram($apiToken, $chat_id , $file_path , $file_name) {
	$excel_file_path = new CURLFile( $file_path . $file_name);
	$caption = '';
	$title = 'Báo cáo số tạm ngưng 19 ngày!' . "\n";
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
	$error = curl_error($ch); 
	curl_close ($ch);
	$responseJson = json_decode($response);
	if($responseJson->ok == true) {
		echo "Đã gửi thành công báo cáo " . "\n";
		return TRUE;
	}
	else echo $error . "\n";
}



?>