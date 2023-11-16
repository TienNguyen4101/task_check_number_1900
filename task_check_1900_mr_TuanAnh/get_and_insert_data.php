<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
require 'login_server.php';
// ===================================================

function check_data_barring($day) {
    $sql = "
	SELECT 
	Customers.`Name`,
	Customers.Address,
	ContractDetailsDVGTGT.CustomerCode, 
	ContractDetailsDVGTGT.ContractCode, 
	ContractDetailsDVGTGT.Number, 
	ContractDetailsDVGTGT.BarringDate,
	DATEDIFF(NOW(), ContractDetailsDVGTGT.BarringDate) as Day_Barring 

	FROM ContractDetailsDVGTGT 
	LEFT JOIN Customers ON ContractDetailsDVGTGT.CustomerCode = Customers.`Code`

	WHERE Number LIKE '1900%' AND StatusISDN = 2 AND DATEDIFF(NOW(), BarringDate) = $day";

    $result = $GLOBALS['connect_server_198']->query($sql);
    $array_output = array();
    if ($result->num_rows == 0) {
		return FALSE;
    }
    else if ($result->num_rows > 0) {
		return TRUE;
    }
}

function get_data_barring($day) {
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

	WHERE Number LIKE '1900%' AND StatusISDN = 2 AND DATEDIFF(NOW(), BarringDate) = $day";

    $result = $GLOBALS['connect_server_198']->query($sql);
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
    }
	return $array_output;
}

function insert_warn_telegram_local() {
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

		if ( $GLOBALS['connect_local']->query($sql_insert) === TRUE) {
			echo "Đã thêm vào bảng `ReportDaily`.`warn_day_17` số: ". $value["Number"] ."\n";
		} else {
			echo "Error: " . $GLOBALS['connect_local']-> error . "\n";
		}
	}
}

function get_and_sort_data_in_table($table_warn) {
    $sql = "SELECT * FROM " . $table_warn . " ";
    $result = $GLOBALS['connect_local']->query($sql);
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
    return $array_output;
}

function empty_table_local() {
	$sql_insert = "
	DELETE FROM warn_day_17
	";
	if ( $GLOBALS['connect_local']->query($sql_insert) === TRUE) {
		echo "Đã CLEAR bảng warn_day_17 thành công" . "\n";
	} else {
		echo "Error: " . $GLOBALS['connect_local']-> error . "\n";
	}
}

?>