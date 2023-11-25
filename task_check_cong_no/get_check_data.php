<?php
require "login_server.php";
require '/var/www/html/export_excel_template.php';
ob_start(); 
date_default_timezone_set('Asia/Ho_Chi_Minh');

//==============================================================================================
$get_before_2_month = date("m", strtotime("-2 month"));
$get_before_2_monthS = ($get_before_2_month < 10) ? substr($get_before_2_month, 1) : $get_before_2_month;

$get_year = date("Y");
$file_path = "/var/www/html/task_check_cong_no/excel_container/";
$file_name = "Missing_debt_T" . $get_before_2_monthS;

$get_last_day_2_month_ago = date("Y-m-t 23:59:59", strtotime("-2 months", strtotime(date("Y-m-d"))));
$get_first_day_2_month_ago = date("Y-m-01 00:00:00", strtotime("-2 months", strtotime(date("Y-m-d"))));

//==============================================================================================

function execute_send_message_telegram($chat_id , $apiToken , $output) {
	$data = [
		'chat_id' => $chat_id,
		'text' => $output,
	];
	$response = file_get_contents("https://api.telegram.org/bot".$apiToken."/sendMessage?" . http_build_query($data) );
	$responseJson = json_decode($response);
	return $responseJson;
}

function send_excel_telegram($apiToken, $chat_id , $file_path , $file_name) {
	global $get_before_2_monthS, $get_year;
	$get_time_day =  date("d-m-Y");
	$excel_file_path = new CURLFile($file_path . $file_name);
	$caption = '';
	$title = 'Báo cáo trả về công nợ tháng ' . $get_before_2_monthS . " năm " . $get_year;
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

$sql_check_contract_missing_debt = "
SELECT DISTINCT
    ContractDetails.ContractCode,
    ContractDetails.CustomerCode,
    Customers.`Name`
FROM
    ContractDetails
    LEFT JOIN Customers ON ContractDetails.CustomerCode = Customers.`Code`
WHERE
    (
        (
            ContractDetails.DateStarted <= '".$get_last_day_2_month_ago."'
            AND ContractDetails.StatusISDN IN ('1', '2')
        ) OR (
            ContractDetails.StatusISDN IN ('3', '5')
            AND ContractDetails.DateEnded >= '".$get_first_day_2_month_ago."'
            AND (
                ContractDetails.DateStarted <= '".$get_first_day_2_month_ago."'
                AND ContractDetails.DateStarted <= ContractDetails.DateEnded
            )
        )
    )
    AND ContractDetails.ContractCode NOT IN (
        SELECT Liabilities.ContractCode
        FROM Liabilities
        WHERE Liabilities.Years = YEAR(NOW())
        AND Liabilities.`Month` = '".$get_before_2_monthS."'
    )
";

/* 
1: Đang kích hoạt.
2: Tạm ngưng -> Tạm ngưng đến ngày thứ 19 thì sẽ đổi sang 3 hoặc 5 tuỳ DateBarring
  => Nếu DateBarring mà bắt đầu từ ngày 15/9 thì vẫn sẽ tính cước từ ngày 1->15.
3: Huỷ Thanh Lý -> Thì không tính cước. 
5: Đang Thanh Lý -> Thì không tính cước

Nếu datebarring bắt đầu từ đầu tháng: 4/9 -> Sau 19 ngày nó sẽ là 5 và sang tháng sẽ là Huỷ Thanh Lý
Nếu datebarring vẫn bắt đầu từ đầu tháng: 11/9 -> Sau 19 ngày nó sẽ là 3 là Huỷ Thanh Lý
=> Vẫn có cước và tính tiền. 

*/

function get_customer_code_in_198($sql) {
	global $server_198, $usename_198, $password_198, $db_in_198;
	$connect_server_198 = mysqli_connect($server_198, $usename_198, $password_198, $db_in_198)
	or die ("Connection failed: " . mysqli_connect_error());

	$result = $connect_server_198->query($sql);
	$array_output = array();
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$array_container = array(
				"Name" => $row["Name"],
				"CustomerCode" => $row["CustomerCode"],
				"ContractCode" => $row["ContractCode"],
				);
			$array_output[] = $array_container;
		}
	} 
	$connect_server_198->close();
	return $array_output;
}
function get_month_debt() {
    global $server_local, $usename_local, $password_local, $db_in_local, 
	$get_before_2_monthS, $get_year, $chat_id, $apiToken, $sql_check_contract_missing_debt;

    $connect_server_local = mysqli_connect($server_local, $usename_local, $password_local, $db_in_local)
        or die("Connection failed: " . mysqli_connect_error());

    $array_output = [];
    foreach (get_customer_code_in_198($sql_check_contract_missing_debt) as $value) {
			if ($value["CustomerCode"] == "DG88888" || $value["CustomerCode"] == "DG08888"
			|| $value["CustomerCode"] == "DG00000" || $value["CustomerCode"] == "DG00095") {
				continue;
			} else {
				$sql_insert = "
					INSERT INTO `VoiceRecord`.report_debt
					(`Name`, `CustomerCode`, `ContractCode`, `Month`)
					VALUES 
					(
						'" . $value["Name"] . "',
						'" . $value["CustomerCode"] . "',
						'" . $value["ContractCode"] . "',
						'" . 'Tháng ' . $get_before_2_monthS . "'
					)
				";
				if ($connect_server_local->query($sql_insert) === TRUE) {
					echo "Đã thêm vào bảng `VoiceRecord`.report_debt với ContractCode : " . $value["CustomerCode"] . "\n";
				} else {
					echo "Error: " . $connect_server_local->error . "\n";
				}

				$output = "";
				$title = "CẢNH BÁO: Chưa có công nợ tháng " . $get_before_2_monthS . " năm " . $get_year . "\n";
				$name = "Khách hàng: " . $value["Name"] . "\n";
				$customerCode = $value["CustomerCode"] . "\n";
				$contractCode = $value["ContractCode"] . "\n";
				$output .= $title . $name . $customerCode . $contractCode;

				if (execute_send_message_telegram($chat_id, $apiToken, $output)->ok == true) {
					echo "Đã gửi tin nhắn thành công đến " . $chat_id . "\n";
				} else {
					echo "Gửi tin nhắn thất bại" . "\n";
				}
			}
		}
    $connect_server_local->close();
}

function empty_table_local($table) {
	global $server_local, $usename_local, $password_local, $db_in_local;
	$connect_server_local = mysqli_connect($server_local, $usename_local, $password_local, $db_in_local)
	or die ("Connection failed: " . mysqli_connect_error());

	$sql_insert = "
	DELETE FROM ".$table."
	";
	if ( $connect_server_local->query($sql_insert) === TRUE) {
		echo "Đã CLEAR bảng " . $table . " thành công" . "\n";
	} else {
		echo "Error: " . $connect_server_local-> error . "\n";
	}
	$connect_server_local -> close();
}

function export_excel_send_telegram($sql) {
	global $server_local, $usename_local, $password_local, $db_in_local , $file_path , $file_name, $apiToken, $chat_id;
	$connect_server_local = mysqli_connect($server_local, $usename_local, $password_local, $db_in_local)
	or die ("Connection failed: " . mysqli_connect_error());

	template_export_excel($sql, $connect_server_local, $file_path, $file_name);
	send_excel_telegram($apiToken, $chat_id , $file_path , $file_name . ".xlsx") ;
	$connect_server_local -> close();
}

$sql_get_data_local = "SELECT * FROM report_debt";

get_month_debt();
export_excel_send_telegram($sql_get_data_local);
empty_table_local("report_debt");
ob_end_flush(); 
?>
