<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

require '/var/www/excel_lib/vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
//============================================================================

function send_email($recipients)
{
	global $username_email, $password_email , $username_email , $subject_email, $from_name_email ;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->Subject = '=?UTF-8?B?' . base64_encode('Subject') . '?=';

		//===============================================

		$mail->Subject = $subject_email;
		$mail->Username = $username_email;
        $mail->Password = $password_email;
        $mail->setFrom($username_email, $from_name_email);

		//======================================================

        foreach ($recipients as $recipient) {
            $mail->addAddress($recipient['email'], $recipient['name']);
        }
		//======================================================

        $mail->isHTML(true);
		$customHtml = content_body($recipient['name']);
        $mail->Body = $customHtml;
        $mail->send();

        echo 'Email đã được gửi đi' . "\n";
    } catch (Exception $e) {
        echo "Lỗi khi gửi email: {$mail->ErrorInfo}" . "\n";
    }
}

function content_body($name_customer) {
	$content = '
	<div
	style="margin-top:20px;line-height:1.8;font-size:24px;font-family:Times New Roman,arial,helvetica;color: black;">
	<div style="text-align: LEFT; font-weight: bold; font-size: 22px; color: black; font-style: italic;">
		<center> Kính gửi Quý Khách Hàng: '.$name_customer.'</center>
	</div>
	<table
	  style="border-collapse:collapse;border-spacing:30px;width:100%"
	  cellpadding="10">
	  <tbody>
	  <tr>
		<th><img
		  src="https://lh3.googleusercontent.com/fife/AGXqzDlQHFBzGEUUeGHrYhlRZbWOr8HER4z02zIDNSkB6dLjQsqMleFrHnlOMV848_fkjA3xd2SEbpx8OLYOs3VZ0GqjOGePNOelRmwLh38WD8loryY8XsKynBncTbbUZKCHg-Xdcv5Nc3-LgsvlRPZ8nezKsRbqbWdvPJy8equ0jb_PEQrx-IGIuy_GZOfzBfCW_7S9ZnKHJB-9hbL994-q_UM7HFEmgIQKPYbRFYTot2nofubtNOLwK2C6aFNdLMUSSAyIYJfX4Y6nu7k6KesHw7TYJoPdpnGwa5Haxz89k7kDkwHU4UeXl-sJe24CH_LFyy1hcuZvwhyhjnKdEUB7RPwu3S5TlJMklVssEppOmO0kEBpiSm2NGXpale36wQQ-_-WIdUasRSd0ix4gN2g_-cxq0WnqGPZZdGiyGI65Y2whHRXEO4n38ChKRLVjG7TsZVDSnOpDz7ZvclrQcPignSA2Yl_4vcbeWsQ-I6b2ooVc3f_a3rq2ERMe41RoVyIzIcv9C5Cbf_ap869F0vYff2plV-gm12_M7ygVbCrKFGuvyHKSqGvrT_ZtO_42sJaGU_42AnAC911A27fB-K6JtoVD0_xngSTQSjjSgWDO-WDHaF64PdqOH9M4WOJIUR6NCfDO10z7EQvb7PocyNmYQ8yK2-sIx3s4fO_6aOmIWW51tJ4VdwHds-rfvRwC97Owa_bZMZYqARjk9pI8y4PuBNIDchPvDIg7urmNDNITST3_A7E1rJVQi_nekPO7egxdmMdosNM0XXA5CVxpIxAsSyoz1DxIVaOhwXjAUtTa8h02q3cE5kwXxptXEy30sBX35Q-A0yio6R5kusxOYHVQ_o5nNrLOIG3ltaKNA6kcDs6gXJo4QK2ZA9IbEbjj6_A3Yd-sJo6qcck1KWJ8dSu7YdQ4rzp4GTmW3VyxFikNUR-j9vIszwqQm0tj7TOd154jvD334j_I8f1V0_eZxX9GzrNmZEWLXwuz4ECXCrfyqVnAhYaZDNDgLtmqt8v8CVsLbYc-p60y1tZUp1Tq8zlQ4VQcrF_ATrPfRBPNzoqy8EZFpB44keJAZrBVRAgRgxFHGwxzXudkL2ph-8MreDiybWp9qV_1Ljuhw-AiqovOHGqPYRnxrdaA8DLLgBHUNBbbdP6v8qfHk37AxZ_rQyilhnQOmzKZgiaUCgUju_VMS7T1v-qoAnMfCHpW1z1LhYyac8xHrw4wXzV9a6vR2Dh4eCTaEumEsXJS_5xRLA1KMbW5zppHNiwcVhnbWH8bE1mML1Ix-Hwi_mKERb3Xyn98oZlzp-9It2nD6tQ7UjO-HGMia77H0izfZYTaQHP9syHrbvN5ty1MkfAVfZYy034TAlDgz1CuTX7c2BUPE4jeU99T4l8K69pCur7etEDk0GyVYeHmpnnrICIMvcNkMU88r782ZID9wXFcIXM3yE4Iv5BpwjtnEzp5X0lCoXDq2cnojGJDp_EaKyZQOfKuVHnWjEldsqx3Y0YAZnjTSl4tsSXPmnRCRK2ZLt79feosWFE8ycqLE6pI1lfEVnp_BS8_ssVMlpp5h3_zgqmd8gtMcKsyX5mUJeLv91AcvQAbG5Ma_MW1gNZ13Rt7sZNXRs6YSy2pjMijlKs1ugKkdP8Yy6I_2ZaoDgqGGlzRt5ll7WHQ1nruzmUcBNo2POT3-PwzpR_sJT8=w1920-h911"
		  class="CToWUd a6T" data-bit="iit" tabindex="0"
			style="
				width: 100%;
				height: auto;
				max-width: 100%;
				display: block;
			"
		  >
		</th>
	  </tr>
	  </tbody>
	</table>
	</div>
	<div style="margin-bottom:10px;margin-top:20px;">
	<span style="font-size:16px; color: black;text-align: justify;"><i>Trong thời gian nghỉ lễ, Công ty vẫn bố trí các
	  chuyên viên, nhân viên trực và làm việc. Nếu Quý
	  khách hàng, Quý đối tác cần hỗ trợ bất kỳ dịch vụ
	  nào xin vui lòng liên hệ hotline: <b> 19009379 </b>.</i></span>
	</div>
	<div style="margin-bottom:10px;margin-top:20px">
	<span style="font-size:16px; color: black; text-align: justify;" ><i>DIGITEL xin trân trọng cảm ơn Quý khách hàng,
	  đối tác đã tin tưởng chúng tôi trong suốt thời gian
	  qua. Một lần nữa kính chúc Quý khách hàng, Quý đối tác
	  có một kỳ nghỉ lễ nhiều sức khỏe, niềm vui bên gia
	  đình, người thân và bạn bè.</i></span></div>
	<div style="margin-bottom:10px;margin-top:20px">
	<span style="font-size:16px; color: black;text-align: justify;"><i>Trân trọng cảm ơn Quý khách hàng đã sử dụng dịch
	  vụ của DIGITEL!</i></span></div>
	<table>
		<tbody>
		<tr>
			<td
				style="width:160px;border-right:2px solid #f2f2f2;">
				<img width="160"
				src="https://lh3.googleusercontent.com/fife/AGXqzDk-TDN7QRp7Hb_z4EGQrre4_kHvVMtMn621HEfhhN2mwfG8pXbp2VrDUPPSTo2Zg6XStCArD5J8EcM_ntY0lXeGMNcFtF1AFc44wdZ9u8DCTBQpirMv01fyu38VnjSXX_6aMVB3yuvFOtL4CWoq47kZQmxPWzvBUTk1qhvk5Fz1CJXp6HDZBL7392wywV16NXuSYFLPGah8EtVhjZ0GxbLGQlzZEKB-ypdTq9nGCXtfH2YA8dblToQIfMFZj3xesjkzVAezUE-0yUKwtVZnrpComQ33Lszrlndiczf3o0J4__3k5ABj8iNGP7RkZV04lrth9BP5IaBjr4UNAkKkWpK4prKWuzjUCrhZTa5ji0IJx3KonFZ46XG5Pjc9hhqjxcZL52xs8Tbpf_4XJNwSCz6H1wEXUS9YdC6_CdCP3Vmq7_yEps67Ge0vSnO_Jone2b7isON3WdS0HQjKQtjH5gZb2I7GpOVmC1zJnN2th73dSvwsdxmgU0DLWvEeh0fMgY3fn4xUEzsOl7A4mKee65gKlUC8MmRNHBFQe0BZPLOnBYm0CUIk4BdV2XqtniPRDMiQvCCB8NGINnGf-L631LZLsVT-ZrpRT-I09_d3QhxRo7resqpuuypVv8AWbbJAsoLxzZuEmUM1uvGUiHbgumTu2285wvpCEJFCCxBepNx394vEtyHVjow-S0p38ErGVueY3aIuaoln7J1W0DMEMZ_WO6mCP296uwm8PD0W7DnFQtS1a8t4dV5dE2mXLKmnw6m2_QzkJVXkFdxQEYrcMfFf310AL4AvbNt5fSO0coHMEMG2ZAQ1y3fyTp0ZSkqEmPc3Zx0kwgZAXIQO1WNP1GfEh9_6AlUR-SigdDcYEapuGqBge1fscG9cR5aoJBXx6sYCj3KVENtaA8bD0ZRJIquhEW42Re1TfAY_UBqI_40Ywsr0FyHrNsKzi9BF4xb76EfVZNwWgLSpPjZNusfV96lz_wsg8IyCj25KDs9e4F5pEmwekinRnUxLMa_2tDg8qFMFu-SvROM13-pMPmnW5B47OhFykUsyjuUyxMAb7KcRj7cbZfDJxjj9K1uKvz1Ohfcw_ZWvkXRq6RMVhyIFxuDMMcXghGGIIb39D7offlSNwTU61-jWPL53pkvvnhqFMRKabuy7AskvjRHR6TkLQm2HVUXzcsibWhWMF55WNkNENefQE1EQpCl6-Y6Qt-RMBqpT9YpfKqq7_mfrozfX02Nr2vnW6vepPmj8xAkCkZMzM_xl80u6D2GmlMrnddSklIN3rM0UIgg07TgJA4p3YGZ1jnjoOidXrUUVKRH16wkL9uzaFTFcdyVFwJUq3ojheHRG7efzWx_IiUDv7VGly6_eH0G9nnpQhlZ6mQEeMFQcVRDVwIJOd3eL0f5bD3OTiALnWrDGrEHtB1kMEnBG2Gug_GvtSz-CE5-9GJCwHRIw5xwHOyshcC2OksBaW8en2iNHIdxaUSGw0E5KBYSArga3XXpUUFzSsKk8V_wmMdKOeYfgRnrXjiZ8xQbCYd1NuDOoCCcg9QWhgraaGFvPNwtEr7G9WaqnpmUPVM5cxpTEA8G2ZvH7HbrrRuBNmxXLQxojMF1sWOkUjpHzAksmWeHgIfXEgi48FNv6QKl1oXFqnF9ULRrWXjG83ccwSMyRz8Ej_EGMaRHmQvw8W0_Qy4fJhw=w1920-h911"
				class="CToWUd a6T" data-bit="iit" tabindex="0">
			</td>

			<td>
				<div style="margin-left:12px;">
					<div style="color:#ffffff;font-size:12px">DIGITEL - CÔNG TY CỔ PHẦN HẠ TẦNG VIỄN THÔNG SỐ </div>
					<p style="font-weight:bold"><a
					href="https://billing.DIGITEL.com.vn/"
					rel="noreferrer" target="_blank"
					data-saferedirecturl="http://digitelgroup.vn/">DIGITEL - CÔNG TY CỔ PHẦN HẠ TẦNG VIỄN THÔNG SỐ</a><br></p>

					<span style="color:black;"> Địa chỉ giao dịch: Số OF03-19 toà OF Vinhomes West Point, Phạm Hùng, Phường Mễ Trì, Quận Nam Từ Liêm, Thành phố Hà Nội. </span><br>
					<span style="color:black;"> Tel: (024-028) 8888 1111 | 19009379 </span> <br>
					<span style="color:black;"> Email: <a href="mailto:admin@digitel.org.vn"
					rel="noreferrer"
					target="_blank">admin@digitel.org.vn</a> </span>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
	';
	
	return $content;
}

?>
