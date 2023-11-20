<?php
require 'export_excel_template.php';
require 'login_server.php';

//================================================

$sql_export_excel_17 = "
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

function export_excel_file($customerCode, $path_to_save, $name_file ) {
	global $server_198, $usename_198, $password_198, $db_in_198;
	$connect_server_198 = mysqli_connect($server_198, $usename_198, $password_198, $db_in_198)
	or die ("Connection failed: " . mysqli_connect_error());

	global $sql_export_excel_17;

	if ($customerCode == "ALL") {
		template_export_excel($sql_export_excel_17, $connect_server_198, $path_to_save, $name_file);
		$connect_server_198 -> close();
	}

	else {
		$sql_export_excel_17_customer = "
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

		template_export_excel($sql_export_excel_17_customer, $connect_server_198, $path_to_save, $name_file);
		$connect_server_198 -> close();
	}
}

$sql_excel_19 = "
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

function export_excel_file_19($sql, $path_to_save, $name_file ) {
	global $server_198, $usename_198, $password_198, $db_in_198;
	$connect_server_198 = mysqli_connect($server_198, $usename_198, $password_198, $db_in_198)
	or die ("Connection failed: " . mysqli_connect_error());
	global $sql_excel_19;
	template_export_excel($sql_excel_19, $connect_server_198, $path_to_save, $name_file);
	$connect_server_198 -> close();
}

?>