<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

require '/var/www/excel_lib/vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//============================================================================

function get_email_to_send() {
    $sql = "SELECT DISTINCT Name, Email, CustomerCode FROM warn_day_17";
    $result = $GLOBALS['connect_local']->query($sql);
    $array_output = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $emails = explode(',', $row["Email"]);
            $emails = array_map('trim', $emails);
            foreach ($emails as $email) {
                $array_container = array(
                    "email" => $email,
                    "name" => $row["Name"],
                    "customercode" => $row["CustomerCode"],
                );
                $array_output[] = $array_container;
            }
        }
    }
    return $array_output;
}

function get_data_of_customer($customerCode) {
	$array_output = array();
	foreach (get_and_sort_data_in_table("warn_day_17") as $value) {
		if($customerCode == $value["CustomerCode"]) {
			$array_container = array(
				"Name" => $value["Name"],
				"Email" => $value["Email"],
                "CustomerCode" => $value["CustomerCode"],
				"ContractCode" => $value["ContractCode"],
				"Number" => $value["Number"],
				"Day_Barring" => $value["Day_Barring"],
				"BarringDate" => $value["BarringDate"],
				"SalerName" =>  $value["SalerName"],
				"SalerCode" =>  $value["SalerCode"],
				"Reason" =>  $value["Reason"],
            );
            $array_output[] = $array_container;
			return $array_output;
		}

		else if($customerCode == "ALL"){
			$array_container = array(
				"Name" => $value["Name"],
				"Email" => $value["Email"],
                "CustomerCode" => $value["CustomerCode"],
				"ContractCode" => $value["ContractCode"],
				"Number" => $value["Number"],
				"Day_Barring" => $value["Day_Barring"],
				"BarringDate" => $value["BarringDate"],
				"SalerName" =>  $value["SalerName"],
				"SalerCode" =>  $value["SalerCode"],
				"Reason" =>  $value["Reason"],
            );
            $array_output[] = $array_container;
		}
	}
	return $array_output;
}

function render_data($customer) {
	$result ="";
	foreach(get_data_of_customer($customer) as $value) {
		$get_data_customer = '
		<tr style="background-color: #ffffff">
		<td style="padding: 8px; border: 0.5px solid black; text-align: center">' .$value["Name"]. ' - '.$value["CustomerCode"].' </td>
		<td style="padding: 8px; border: 0.5px solid black">'.$value["Number"].'</td>
		<td style="padding: 8px; border: 0.5px solid black; text-align: center">'.$value["ContractCode"].'</td>
		<td style="padding: 8px; border: 0.5px solid black; text-align: center">'.$value["SalerName"].' - '.$value["SalerCode"].'</td>
		<td style="padding: 8px; border: 0.5px solid black; text-align: center">Do <font style="font-weight:bold"> '.$value["Reason"].' </font></td>
		</tr>
		';
		$result .= $get_data_customer;
	}
	return $result;
}

function send_email($file_path, $file_name , $recipients , $customer) {
    $content = render_data($customer);
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tienxemutube412001@gmail.com';
        $mail->Password = 'bmxpcpdvnbdyvolt';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Subject = '=?UTF-8?B?' . base64_encode('Subject') . '?=';

        $mail->setFrom('tienxemutube412001@gmail.com', 'Leighton Nguyen');

		//======================================================

        foreach ($recipients as $recipient) {
            $mail->addAddress($recipient['email'], $recipient['name']);
        }

		//$mail->addAddress('anh.pt@digitel.org.vn', 'Tien Nguyen');
		
        $mail->addAttachment($file_path . $file_name);
		//======================================================

        $mail->isHTML(true);
        $customHtml = '
<html>
<body>
    <div style="text-align: LEFT; font-weight: bold; font-size: 20px; color: #1c9ad6">
        <center>DANH SÁCH KHÁCH HÀNG CÓ ĐẦU SỐ 1900 ĐÃ QUÁ HẠN TẠM NGƯNG </center>
    </div>
    <div style="margin-top: 20px; line-height: 1.8; font-size: 17px; font-family: Times New Roman, arial, helvetica">
        <br>
        <table>
            <tbody>
                <tr>
                    <td style="text-align: left; font-family: Times New Roman, arial, helvetica; border-bottom: 1px solid #f2f2f2">
                        <i>Kính gửi bộ phận admin, kế toán, đối soát và kỹ thuật của <font style="color:#199cd9;font-weight:650"> DIGITEL</font>.</i>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; font-family: Times New Roman, arial, helvetica; border-bottom: 1px solid #f2f2f2">
                        <font style="border-bottom: 1px solid #f2f2f2">Dưới đây là danh sách những khách hàng quá hạn tạm ngưng đến ngày <font style="font-weight:bold">' . date('Y-m-d H:i:s') . '</font> 
						- Sẽ bị thu hồi vào ngày <font style="font-weight:bold">'. date('Y-m-d', strtotime('+2 days')) .'</font> .</font>
                        <br>
                        <font style="font-weight:500;border-bottom:1px solid #f2f2f2">Danh sách gồm những khách hàng kèm đầu số đã quá hạn tạm ngưng 17 ngày. Xem chi tiết ở File Excel đính kèm. </font>
                    </td>
                    <td><br></td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" style="border-collapse: collapse; padding: 8px">
                            <tbody>
                                <tr>
                                    <th bgcolor="#00BFFF" style="font-weight: bold; padding: 8px; border: 0.5px solid black; text-align: center">MÃ KHÁCH HÀNG</th>
                                    <th bgcolor="#00BFFF" style="font-weight: bold; padding: 8px; border: 0.5px solid black; text-align: center">ĐẦU SỐ</th>
                                    <th bgcolor="#00BFFF" style="font-weight: bold; padding: 8px; border: 0.5px solid black; text-align: center">MÃ HỢP ĐỒNG</th>
                                    <th bgcolor="#00BFFF" style="font-weight: bold; padding: 8px; border: 0.5px solid black; text-align: center">MÃ SALE</th>
                                    <th bgcolor="#00BFFF" style="font-weight: bold; padding: 8px; border: 0.5px solid black; text-align: center">LÝ DO TẠM NGƯNG</th>
                                </tr>
                                <tr style="background-color: #ffffff">';
								//render data ==========================================================================================								
										$customHtml .= $content;
								//render data ==========================================================================================	
								$customHtml .='
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; border-bottom: 1px solid #f2f2f2; font-family: Times New Roman, arial, helvetica; border-bottom: 1px solid #f2f2f2">
                        <font style="border-bottom: 1px solid #f2f2f2">Danh sách này được thực hiện 1 cách tự động, vì vậy nếu có khách hàng nào không nằm trong danh sách trên vui lòng báo lại kỹ thuật để xử lý.</font>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

	<div style="margin-top:20px;margin-right:40px;margin-bottom:10px;font-family:cursive;text-align:right"><span><i><u></u>DIGITEL Forever<u></u></i></span></div>

	<table width="100%" cellpadding="0" cellspacing="0"> 
	<tbody><tr>
		<td style="width:160px;border-right:2px solid #f2f2f2">
			<img width="150px" src="https://ci5.googleusercontent.com/proxy/mun0lp1GsK5zYsuuPCPVUjRrtV9IQAaAOER82cxucfBEhx86DI_7fJs359wlMoVtN6xYcFwb6Hsjq0QQcNBrlI2rfctr7UFDZ77eDXzG7nLTxwxOKai3n9eCg-8=s0-d-e1-ft#http://183.91.185.198/VoiceReport/crond/sendNoteCustomers/LOGODIGITEL.png" class="CToWUd" data-bit="iit">
		</td><td style="text-align:top;margin-left:10px">
			<div style="color:#ffffff;font-size:1px">CÔNG TY CỔ PHẦN HẠ TẦNG VIỄN THÔNG SỐ (DIGITEL)</div>
			<p style="font-weight:bold"><a href="http://digitelgroup.vn/" rel="noreferrer" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://digitelgroup.vn/&amp;source=gmail&amp;ust=1700040086109000&amp;usg=AOvVaw2PG35R-9UMfSYY5fnuTcwN">CÔNG TY CỔ PHẦN HẠ TẦNG  VIỄN THÔNG SỐ (DIGITEL)</a><br><br></p>
			Địa chỉ giao dịch: A24/D7 Khu đô thị mới Cầu Giấy, Phường Dịch Vọng, Quận Cầu Giấy, Thành phố Hà Nội, Việt Nam.<br>		
			Tel: (024-028) 8888 1111 | 1900999990 | <a href="http://digitelgroup.vn" rel="noreferrer" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://digitelgroup.vn&amp;source=gmail&amp;ust=1700040086109000&amp;usg=AOvVaw040QmXNZL8JPuoJOU_yxub">http://digitelgroup.vn</a><br>
			Email: <a href="mailto:admin@digitel.org.vn" rel="noreferrer" target="_blank">admin@digitel.org.vn</a>
			<div style="color:#ffffff;font-size:1px">Tổng công ty viễn thông toàn cầu - Bộ công an</div>
		</td></tr></tbody></table>
</body>

</html>
';
        $mail->Subject = 'DANH SÁCH KHÁCH HÀNG ĐÃ QUÁ HẠN TẠM NGƯNG NGÀY ' . date('Y-m-d H:i:s');
        $mail->Body = $customHtml;

        $mail->send();
        echo 'Email đã được gửi đi' . "\n";
    } catch (Exception $e) {
        echo "Lỗi khi gửi email: {$mail->ErrorInfo}" . "\n";
    }
}

?>