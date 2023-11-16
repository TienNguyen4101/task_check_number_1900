<?php
require '/var/www/excel_lib/vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//================================================

function export_excel_file($customerCode ,$connection ,$path_to_save ,$name_file ) {
	if ($customerCode == "ALL") {
		$sql_send_report = "
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

		WHERE Number LIKE '1900%' AND 
		StatusISDN = 2 AND DATEDIFF(NOW(), BarringDate) = 17
		";

		$result = $connection->query($sql_send_report);
		if ($result -> num_rows > 0) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			$fields_Name = $result->fetch_fields();
			$columnNames = array_column($fields_Name, 'name');
			$sheet->fromArray([$columnNames], NULL, 'A1');

			$rowIndex = 2;
			while ($row = $result->fetch_assoc()) {
				$rowData = [];
				foreach ($fields_Name as $field) {
					$value = isset($row[$field->name]) ? str_replace(',', '', $row[$field->name]) : '';
					$rowData[] = $value;
				}
				$sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
				$rowIndex++;
			}

			$writer = new Xlsx($spreadsheet);
			$file_name = $path_to_save . $name_file; 
			$writer->save($file_name);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment; filename=$file_name");

			echo "Đã export thành công dữ liệu ".$customerCode." và lưu tại " . $file_name . "\n";
			return TRUE;
		} else {
			echo "Hiện tại chưa có dữ liệu nào trả về trên sql" . "\n";
			return FALSE;
		}
	} else {
		$sql_send_report = "
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

		WHERE CustomerCode = '".$customerCode."' AND 
		Number LIKE '1900%' AND StatusISDN = 2 AND DATEDIFF(NOW(), BarringDate) = 17
		";

		$result = $connection->query($sql_send_report);
		if ($result -> num_rows > 0) {
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			$fields_Name = $result->fetch_fields();
			$columnNames = array_column($fields_Name, 'name');
			$sheet->fromArray([$columnNames], NULL, 'A1');

			$rowIndex = 2;
			while ($row = $result->fetch_assoc()) {
				$rowData = [];
				foreach ($fields_Name as $field) {
					$value = isset($row[$field->name]) ? str_replace(',', '', $row[$field->name]) : '';
					$rowData[] = $value;
				}
				$sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
				$rowIndex++;
			}

			$writer = new Xlsx($spreadsheet);
			$file_name = $path_to_save . $name_file; 
			$writer->save($file_name);

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment; filename=$file_name");

			echo "Đã export thành công dữ liệu của KH ".$customerCode." và lưu tại " . $file_name . "\n";
			return TRUE;
		} else {
			echo "Hiện tại chưa có dữ liệu nào trả về trên sql" . "\n";
			return FALSE;
		}
	}
}

$sql_send_report = "
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

WHERE Number LIKE '1900%' AND StatusISDN = 2 AND DATEDIFF(NOW(), BarringDate) = 19
";

function export_excel_file_19($sql ,$connection ,$path_to_save ,$name_file ) {
	$result = $connection->query($sql);
	if ($result -> num_rows > 0) {
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$fields_Name = $result->fetch_fields();
		$columnNames = array_column($fields_Name, 'name');
		$sheet->fromArray([$columnNames], NULL, 'A1');

		$rowIndex = 2;
		while ($row = $result->fetch_assoc()) {
			$rowData = [];
			foreach ($fields_Name as $field) {
				$value = isset($row[$field->name]) ? str_replace(',', '', $row[$field->name]) : '';
				$rowData[] = $value;
			}
			$sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
			$rowIndex++;
		}

		$writer = new Xlsx($spreadsheet);
		$file_name = $path_to_save . $name_file; 
		$writer->save($file_name);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=$file_name");

		echo "Đã export thành công dữ liệu warn_day_19 và lưu tại " . $file_name . "\n";
		return TRUE;
	} else {
		echo "Hiện tại chưa có dữ liệu nào trả về trên sql" . "\n";
		return FALSE;
	}
}

?>