<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
ob_start(); 
require 'export_excel.php';
require 'send_email.php';
require 'send_messege_telegram.php';
$file_path =  "/var/www/html/task_check_1900_mr_TuanAnh/excelcontainer/";
$file_name_17 = "Report_warning_day_17_" . date('Y-m-d');
$file_name_19 = "Report_warning_day_19_" . date('Y-m-d');

//============HANDLE LOGIC============//

if (check_data_barring("17") == TRUE ) {
    insert_warn_telegram_local();

    //CustomerCode or ALL ===============================
    
    send_message_telegram("warn_day_17", $apiToken , $chat_id , "ALL" );

    // Send excel to telegram ===============================

    export_excel_file("ALL", $file_path , $file_name_17 );

    send_excel_telegram($apiToken, $chat_id , $file_path , $file_name_17 . ".xlsx" , "17");

    //=======================> SEND EMAIL FOR ANY CUSTOMER <+++++++++++++++++++++++++++++++++
    // foreach(get_email_to_send() as $value) {
    // $recipients = [
    //     ['email' => $value["email"], 'name' => $value["name"]],
    // ];
    //     export_excel_file($value["customercode"] , $connect_server_198 , $file_path , $value["customercode"] . "_" . date('Y-m-d') . ".xlsx" );
    //     send_email($file_path, $value["customercode"] . "_" . date('Y-m-d') . ".xlsx" , $recipients, $value["customercode"]);
    // }

    $recipients = [
        ['email' => 'tien412001@gmail.com', 'name' => 'Recipient 1'],
    ];
    send_email($file_path, $file_name_17 . ".xlsx", $recipients, "ALL"); //==============!!!===============

    empty_table_local();

} else {
    echo "Hiện tại không có dữ liệu của ngày 17" . "\n";
}

if (check_data_barring("19") == TRUE) {
    export_excel_file_19($sql_excel_19, $file_path, $file_name_19);
    send_excel_telegram($apiToken, $chat_id , $file_path , $file_name_19 . ".xlsx" , "19");
} 
else {
    echo "Hiện tại không có dữ liệu của ngày 19" . "\n";
}

ob_end_flush(); 

?>
