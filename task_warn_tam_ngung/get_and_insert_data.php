<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require 'login_server.php';
// ===================================================

function get_data_barring($day) {
	global $server_198, $usename_198, $password_198, $db_in_198;
	$connect_server_198 = mysqli_connect($server_198, $usename_198, $password_198, $db_in_198)
	or die ("Connection failed: " . mysqli_connect_error());

	$sql = "
		SELECT 
		Customers.`Name`,
		Customers.Email,
		ContractDetailsDVGTGT.CustomerCode, 
		ContractDetailsDVGTGT.ContractCode, 
		ContractDetailsDVGTGT.Number, 
		ContractDetailsDVGTGT.SalerCode,
		Salers.`Name` as SalerName,
		ContractDetailsDVGTGT.BarringDate,
		ContractDetailsDVGTGT.PauseReasonNote AS Reason,

		DATEDIFF(NOW(), ContractDetailsDVGTGT.BarringDate) as Day_Barring 

		FROM ContractDetailsDVGTGT 
		LEFT JOIN Customers ON ContractDetailsDVGTGT.CustomerCode = Customers.`Code`
		LEFT JOIN Salers ON ContractDetailsDVGTGT.SalerCode = Salers.`Code`

		WHERE Number LIKE '1900%' AND StatusISDN = 2 AND DATEDIFF(NOW(), BarringDate) = $day
	";
    $result = $connect_server_198->query($sql);
    $array_output = array();
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
            $array_container = array(
				"Name" => $row["Name"],
				"Email" => $row["Email"],
               			"CustomerCode" => $row["CustomerCode"],
				"ContractCode" => $row["ContractCode"],
				"Number" => $row["Number"],
				"Day_Barring" => $row["Day_Barring"],
				"BarringDate" => $row["BarringDate"],
				"SalerName" =>  $row["SalerName"],
				"SalerCode" =>  $row["SalerCode"],
				"Reason" =>  $row["Reason"],
            );
            $array_output[] = $array_container;
        }
    } else {
		return FALSE;
	}

	$connect_server_198->close();
	return $array_output;
}

function insert_warn_telegram_local() {
	global $server_local, $usename_local, $password_local, $db_in_local;
	$connect_server_local = mysqli_connect($server_local, $usename_local, $password_local, $db_in_local)
	or die ("Connection failed: " . mysqli_connect_error());

	foreach(get_data_barring("17") as $value) {
		$sql_insert = "
		INSERT INTO `ReportDaily`.`warn_day_17`
		(`Name`, `Email`, `CustomerCode`, `ContractCode`, `Number`, `SalerCode`, `SalerName`, `BarringDate`, `Reason`, `Day_Barring`) 
		VALUES 
		(
		 '".$value["Name"]."',
		 '".$value["Email"]."', 
		 '".$value["CustomerCode"]."', 
		 '".$value["ContractCode"]."', 
		 '".$value["Number"]."', 
		 '".$value["SalerCode"]."', 
		 '".$value["SalerName"]."', 
		 '".$value["BarringDate"]."', 
		 '".$value["Reason"]."', 
		 '".$value["Day_Barring"]."'
		 )
		";

		if ( $connect_server_local->query($sql_insert) === TRUE) {
			echo "Đã thêm vào bảng `ReportDaily`.`warn_day_17` số: ". $value["Number"] ."\n";
		} else {
			echo "Error: " . $connect_server_local -> error . "\n";
		}
	}
	$connect_server_local -> close();
}

function get_and_sort_data_in_table($table_warn) {

	global $server_local, $usename_local, $password_local, $db_in_local;
	$connect_server_local = mysqli_connect($server_local, $usename_local, $password_local, $db_in_local)
	or die ("Connection failed: " . mysqli_connect_error());

    $sql = "SELECT * FROM " . $table_warn . " ";
    $result = $connect_server_local->query($sql);
    $array_output = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $key = $row["Name"] . "_" . $row["ContractCode"];
            if (array_key_exists($key, $array_output)) {
                $array_output[$key]['Number'] .= " - " . $row["Number"];
            } else {
                $array_output[$key] = array(
					"Name" => $row["Name"],
					"Email" => $row["Email"],
					"CustomerCode" => $row["CustomerCode"],
					"ContractCode" => $row["ContractCode"],
					"Day_Barring" => $row["Day_Barring"],
					"BarringDate" => $row["BarringDate"],
					"SalerName" =>  $row["SalerName"],
					"SalerCode" =>  $row["SalerCode"],
					"Reason" =>  $row["Reason"],
					"Number" => $row["Number"],
                );
            }
        }
    }
    $array_output = array_values($array_output);
	$connect_server_local -> close();
    return $array_output;
}

function empty_table_local() {

	global $server_local, $usename_local, $password_local, $db_in_local;
	$connect_server_local = mysqli_connect($server_local, $usename_local, $password_local, $db_in_local)
	or die ("Connection failed: " . mysqli_connect_error());

	$sql_insert = "
	DELETE FROM warn_day_17
	";
	if ( $connect_server_local->query($sql_insert) === TRUE) {
		echo "Đã CLEAR bảng warn_day_17 thành công" . "\n";
	} else {
		echo "Error: " . $connect_server_local-> error . "\n";
	}
	$connect_server_local -> close();
}

?>
