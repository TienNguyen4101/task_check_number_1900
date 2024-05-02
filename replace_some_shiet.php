<?php
ob_start(); 
require 'login_server.php';
require 'export_excel.php';
require 'send_message_telegram.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

$file_path = "/var/www/html/tool_automation_daily/CDR_HDSAISON/excel_container/";

//==============================================================================================

function template_local() {
    global $server_local,$username_local,$password_local, $db_in_local  ;
    $connect_server_local = mysqli_connect($server_local, $username_local, $password_local, $db_in_local)
        or die ("Connection failed: " . mysqli_connect_error());
        
    $array_output = array();

    $sql = "";

    $result = $connect_server_local->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $array_container = array(
                "Callee" => $row["Callee"],
                "Caller" => $row["Caller"],
            );
            $array_output[] = $array_container;

        }
    } 
    $connect_server_local->close();
    return $array_output;
}

function template_main() {
	global $server_main, $user_main, $password_main, $db_main;
	$connect_server_main = mysqli_connect($server_main, $user_main, $password_main, $db_main)
	or die ("Connection failed: " . mysqli_connect_error());
	$array_output = array();

    $sql = "";

    $result = $connect_server_main->query($sql);
    $connect_server_main->close();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            
            $array_container = array(
                "Callee" => $row["Callee"],
                "Caller" => $row["Caller"],
            );
            $array_output[] = $array_container;

        }
	}
	return $array_output;
}

function template_BK($sql) {
	global $server_BK, $user_BK, $password_BK, $db_BK;
	$connect_server_BK = mysqli_connect($server_BK, $user_BK, $password_BK, $db_BK)
	or die ("Connection failed: " . mysqli_connect_error());

    $array_output = array();

    $sql = "";

	$result = $connect_server_BK->query($sql);

	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {

            $array_container = array(
                "Callee" => $row["Callee"],
                "Caller" => $row["Caller"],
            );
            $array_output[] = $array_container;
		}
	} 
    $connect_server_BK->close();
    return $array_output;
}

function excute_local() {
    global $server_local,$username_local,$password_local, $db_in_local  ;
    $connect_server_local = mysqli_connect($server_local, $username_local, $password_local, $db_in_local)
        or die ("Connection failed: " . mysqli_connect_error());
        $sql = "";
        if ($connect_server_local ->query($sql) === TRUE) {
            echo "success" . "\n";
        } else {
            echo "Error: " . $connect_server_local-> error . "\n";
        }
    
    $connect_server_local->close();
}

function excute_main() {
	global $server_main, $user_main, $password_main, $db_main;
	$connect_server_main = mysqli_connect($server_main, $user_main, $password_main, $db_main)
	or die ("Connection failed: " . mysqli_connect_error());

    $sql = "";

    if ($connect_server_main ->query($sql) === TRUE) {
        echo "success" . "\n";
    } else {
        echo "Error: " . $connect_server_main-> error . "\n";
    }

    $connect_server_main->close();
}
function excute_BK() {
	global $server_BK, $user_BK, $password_BK, $db_BK;
	$connect_server_BK = mysqli_connect($server_BK, $user_BK, $password_BK, $db_BK)
	or die ("Connection failed: " . mysqli_connect_error());

    $sql = "";

    if ($connect_server_BK ->query($sql) === TRUE) {
        echo "success" . "\n";
    } else {
        echo "Error: " . $connect_server_BK-> error . "\n";
    }

    $connect_server_BK->close();
}


//maps function defined
    // template_export_by_array($data, $file_path, $file_name);
    // execute_send_message_telegram($chat_id , $apiToken , $output);
    // send_excel_telegram($apiToken, $chat_id , $file_path , $file_name . ".xlsx" , $title);
//=================================================================================================

// execute_send_message_telegram($chat_id_main , $apiToken_main , 'Test');

$file_name = "CDR_HDSAISON_" . date('Y-m-d', strtotime('-1 days'));

function get_CDR_origin_full($date_check) {
	global $server_main, $user_main, $password_main, $db_main, $file_path, $file_name;
	$connect_server_main = mysqli_connect($server_main, $user_main, $password_main, $db_main)
	or die ("Connection failed: " . mysqli_connect_error());
	$array_output = array();

    $sql = "SELECT Caller,Callee,Time,TimeEnded,Duration,Provider FROM `CDROriginFull` 
            WHERE CallerGW LIKE 'HDSAISON00762_DIGIVOICE_FPT%'
            AND Time BETWEEN '".$date_check." 00:00:00' AND '".$date_check." 23:59:59' ";

    $result = $connect_server_main->query($sql);
    $connect_server_main->close();
    if ($result->num_rows > 0) {
        //file_put_contents($file_path . $file_name . ".csv", '"Caller" , "Callee" , "Time", "TimeEnded", "Duration", "Provider"' . PHP_EOL, FILE_APPEND);
        while ($row = $result->fetch_assoc()) {
            $array_container = array(
                "Caller" => $row["Caller"],
                "Callee" => $row["Callee"],
                "Time" => $row["Time"],
                "TimeEnded" => $row["TimeEnded"],
                "Duration" => $row["Duration"],
                "HangUp Side" => check_reason($row["Provider"]),
            );
            $array_output[] = $array_container;
            // $array_output[] = $row;
            //file_put_contents($file_path . $file_name . ".csv", '"'.$row['Caller'].'" , "'.$row['Callee'].'" , "'.$row['Time'].'", "'.$row['TimeEnded'].'", "'.$row['Duration'].'", "'.$row['Provider'].'"' . PHP_EOL, FILE_APPEND);
        }   
	}
	return $array_output;
}

// -39  By callee
// -7  By caller
// -8  By callee
// 403  By callee
// 480  By callee
// -69  Server
// -34  Server
// 500  By callee
// 603  By callee
// 487  By callee
// 503  By callee
// 408  By callee
// 606  By callee
// 404  By callee
// 502  By callee
// 400  By callee

function check_reason($provider) {
    $array_by_callee = [-39,-8,403,480,500,603,487,503,408,606,404,502,400];
    $array_by_caller = [-7];
    $array_by_server = [-69,-34];
    if (in_array($provider, $array_by_callee)) {
        return "BY CALLEE";
    } else if (in_array($provider, $array_by_caller)) {
        return "BY CALLER";
    } else if (in_array($provider, $array_by_server)) {
        return "BY SERVER";
    } else {
        return "BY CALLEE";
    } 
}

$date_check = date('Y-m-d', strtotime('-1 days'));
$data_export = get_CDR_origin_full($date_check);

template_export_by_array($data_export, $file_path, $file_name);
send_excel_telegram($apiToken_main, $chat_id_main , $file_path , $file_name . ".xlsx" , 'Báo cáo CDR HDSAISON ngày: ' . date('d-m-Y', strtotime('-1 days')));

ob_end_flush(); 
?>
