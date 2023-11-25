<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
ob_start(); 

require '/var/www/excel_lib/vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

function template_export_excel($data, $path_to_save, $name_file)
{
    if (!empty($data)) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $columnNames = array_keys($data[0]);
        $sheet->fromArray([$columnNames], NULL, 'A1');

        $rowIndex = 2;
        foreach ($data as $row) {
            $rowData = [];
            foreach ($columnNames as $columnName) {
                $value = isset($row[$columnName]) ? str_replace(',', '', $row[$columnName]) : '';
                $rowData[] = $value;
            }
            $sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
			//Set odd rows color
            if ($rowIndex % 2 == 1) {
                $highestColumn = $sheet->getHighestColumn();
                $range = 'A' . $rowIndex . ':' . $highestColumn . $rowIndex;
                $sheet->getStyle($range)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ccffff');
            }
            $rowIndex++;
        }

		//set first row to color
        $highestColumn = $sheet->getHighestColumn();
        foreach (range('A', $highestColumn) as $col) {
            $cellCoordinate = $col . '1';
			$sheet->getStyle($cellCoordinate)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('00bfff');
            $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($cellCoordinate)->getFont()->setBold(true);
        }

        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

		$lastRow = $sheet->getHighestRow() + 1;
		$sheet->mergeCells('A' . $lastRow . ':' . $highestColumn . $lastRow);
	
		// Add text to the final rows
		$sheet->setCellValue('A' . $lastRow, "                                                          CÔNG TY CỔ PHẦN HẠ TẦNG VIỄN THÔNG SỐ (DIGITEL)\n"
			. "                                                          Địa chỉ giao dịch: Số 2 ngõ 66 Khúc Thừa Dụ, Phường Dịch Vọng, Quận Cầu Giấy, Thành phố Hà Nội.\n"
			. "                                                          Tel: (024-028) 8888 1111 | 1900999990 | http://digitelgroup.vn\n"
			. "                                                          Email: admin@digitel.org.vn");
		$sheet->getStyle('A' . $lastRow . ':' . $highestColumn . $lastRow)->getFont()->setBold(true);
		$sheet->getRowDimension($lastRow)->setRowHeight(60); 
	
		// Define att text wrap for cell
		$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
		$sheet->getStyle('A' . $lastRow . ':' . $highestColumn . $lastRow)->getAlignment()->setWrapText(true);
		$sheet->getStyle('A' . $lastRow . ':' . $highestColumn . $lastRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f3f3f3');

		// insert logo
		$drawing = new Drawing();
		$drawing->setName('Logo');
		$drawing->setDescription('Logo');
		$drawing->setPath('/var/www/excel_lib/screenshot_1700193829.png');  
		$drawing->setCoordinates('A' . $lastRow);  // Define location logo

		// Define size for logo
		$drawing->setWidth(120);
		$drawing->setHeight(79);
		$drawing->setOffsetX(0); // config location logo in X
		$drawing->setOffsetY(2); // config location logo in Y
		$drawing->setWorksheet($sheet);

        $writer = new Xlsx($spreadsheet);
        $file_name = $path_to_save . $name_file . '.xlsx';
        $writer->save($file_name);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=$file_name");

        echo "Đã export thành công và lưu tại " . $file_name . "\n";
        return TRUE;
    } else {
        echo "Hiện tại chưa có dữ liệu nào trả về trên sql" . "\n";
        return FALSE;
    }
}

ob_end_flush(); 

?>
