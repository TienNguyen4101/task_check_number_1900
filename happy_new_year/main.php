<?php
ob_start(); 
require 'handle_logic/login_server.php';
require 'handle_logic/export_excel.php';
require 'handle_logic/send_message_telegram.php';
require 'handle_logic/form_email.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

$file_path = "../excel_container/";
$file_name = "test" ;

//==============================================================================================
//maps function defined
    // template_export_by_array($data, $file_path, $file_name);
    // execute_send_message_telegram($chat_id , $apiToken , $output);
    // send_excel_telegram($apiToken, $chat_id , $file_path , $file_name . ".xlsx" , $title);
//=================================================================================================

$sql_get_all_customer = "
    SELECT DISTINCT Customers.`Name`,Customers.Email

    FROM ContractDetails INNER JOIN Customers ON ContractDetails.CustomerCode = Customers.`Code`

    WHERE ContractDetails.StatusISDN = 1
";

function get_all_email_customer_active($sql) {
	global $server_main, $user_main, $password_main, $db_main;
	$connect_server_main = mysqli_connect($server_main, $user_main, $password_main, $db_main)
	or die ("Connection failed: " . mysqli_connect_error());
	$array_output = array();

    $result = $connect_server_main->query($sql);
    $connect_server_main->close();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $emails = explode(',', $row["Email"]);
            $emails = array_map('trim', $emails);
            foreach ($emails as $email) {
                $array_container = array(
                    "email" => $email,
                    "name" => $row["Name"],
                );
                $array_output[] = $array_container;
            }
        }
	}
	return $array_output;
}

//prodution 
//==================================================================

// $all_email_customer = get_all_email_customer_active($sql_get_all_customer);
// foreach($all_email_customer as $value) {
//     $recipients_for_customer = [
//         ['email' => $value["email"], 'name' => $value["name"]],
//     ];

//     // send_email($recipients_for_customer);
// }

//==================================================================

//testing
$recipients_for_admin = [
    ['email' => 'tien412001@gmail.com', 'name' => 'Tien Nguyen'],
    // ['email' => 'anh.pt@digitel.org.vn', 'name' => 'Phạm Tuấn Anh'],
    ['email' => 'tien.0401@starlento.vn', 'name' => 'Tien Nguyen'],
];
send_email($recipients_for_admin);

ob_end_flush(); 
?>