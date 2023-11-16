<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
ob_start(); 
require 'export_excel.php';
require 'send_email.php';
require 'send_messege_telegram.php';
$file_path =  "/var/www/html/task_check_1900_mr_TuanAnh/excelcontainer/";

//============HANDLE LOGIC============//

if (check_data_barring("17") == TRUE ) {
    insert_warn_telegram_local();

    //CustomerCode or ALL ===============================
    
    send_message_telegram("warn_day_17", $apiToken ,$chat_id , "ALL" );

    // Send excel to telegram ===============================

    $file_name = "report_barring_date_17_ALL_" . date('Y-m-d') . ".xlsx" ;

    export_excel_file("ALL" , $connect_server_198, $file_path , $file_name );

    send_excel_telegram($apiToken, $chat_id , $file_path , $file_name );


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
    send_email($file_path, $file_name, $recipients, "ALL"); //==============!!!===============

    empty_table_local();

    if (check_data_barring("19") == TRUE) {
        export_excel_file_19($sql_send_report, $connect_server_198, $file_path , "report_barring_date_19_ALL_" . date('Y-m-d') . ".xlsx" );
        send_excel_telegram($apiToken, $chat_id , $file_path , "report_barring_date_19_ALL_" . date('Y-m-d') . ".xlsx" );
    } 
    else {
        echo "Hiện tại không có dữ liệu của ngày 19" . "\n";
    }

} else {
    echo "Hiện tại không có dữ liệu của ngày 17" . "\n";
}

ob_end_flush(); 
$connect_server_198->close();
$connect_local->close();

?>
