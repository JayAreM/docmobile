

<?php
	session_start();
	
	require_once("../includes/database.php");
	require_once("../interface/sheets.php");
	require_once("../includes/vstr.php");

	$defaultYear = 2023;

	if(!isset($_SESSION['employeeNumber'])) {
			
		$referer = explode('/', $_SERVER['HTTP_REFERER']);
		$location = strtoupper($referer[sizeof($referer) - 1]);
	
		if($location == 'DOCTRACKPUBLICSEARCH.PHP' || $location == 'LOGIN.PHP') {
		}else {
			$link = "<script>window.open('../../citydoc".$defaultYear."/interface/login.php','_self')</script>";
			echo $link;
			die();
		}
		
	}
	
	$dt = time();
	$dateEncoded = date('Y-m-d h:i A', $dt);
	if(isset($_SESSION['officeCode'])){
		$userOffice = $_SESSION['cbo'];
		$employeeNumber = $_SESSION['employeeNumber'];
		$accountType = $_SESSION['accountType'];
	}

	if(isset($_GET['fujxyza'])){
		
		$crypt = $database->charEncoder($_GET['xaXvsfTs']);
		
		$arr = explode('~!~', $database->vArrange($crypt));
		
		$employeeNumber = substr($arr[0],0,6);
		$mt = substr($arr[0],6);
		
		$passText = $arr[1];

		$password = md5($passText);

		$record = $database->LoginUser($employeeNumber,$password);		
		$count = $database->num_rows($record);

		//echo $mt;
		//echo $ip;

		if($count >= 1){
			$data = $database->fetch_array($record);
			$registrationState = $data['RegistrationState'];
			if($registrationState == 1){
				
				$id = $data['UserId'];
				$print = $data['Prints'];
				
				if($mt <= $print){
					$sql = "Update  citydoc.users set Logcker = '" . $dateEncoded . "' where Id  = '" . $id . "' limit 1";
					$database->query($sql);
					echo 4;
				}else{
					$sql = "Update  citydoc.users set LoginState = 1, DateLog = '" . $dateEncoded . "',Prints = '" . $mt . "', IpMan = '" . $ip . "' where Id  = '" . $id . "' limit 1";
					$database->query($sql);
					
					$_SESSION['employeeNumber'] =  $data['EmployeeNumber'];
					$employeeNumber = $_SESSION['employeeNumber'];
					$sql1 = "Select * from citydoc.userposition where EmployeeNumber = '$employeeNumber'";
					$result1 = $database->query($sql1);		
					$data1 = $database->fetch_array($result1);
					$positiontitle = $data1['Title'];
					$_SESSION['PositionTitle'] = $positiontitle;
					$fullName = $data['FirstName']. ' ' . $data['MiddleName'] . ' ' . $data['LastName'];
					$office = trim($data['OfficeCode']);
					$accountType = $data['AccountType'];
					$officeName = $data['Name'];
					$firstName = $data['FirstName'];
					$lastName = $data['LastName'];
					$_SESSION['officeCode'] = $data['OfficeCode'];
					$_SESSION['gso'] = $data['OfficeCode'];
					$_SESSION['cbo'] = $data['OfficeCode'];
					$_SESSION['firstName'] = $database->charEncoder(utf8_encode($firstName));
					$_SESSION['fullName'] =  $database->charEncoder(utf8_encode($fullName));
					// $_SESSION['fullName'] =  $database->charEncoder(mb_convert_encoding($fullName, 'UTF-8', 'ISO-8859-1'));
					$_SESSION['officeName'] = $officeName;
					$_SESSION['accountType'] = $accountType;
					$_SESSION['FkyhaXs'] = $id;
					$_SESSION['perm'] = $data['Permission'];
					$_SESSION['privy'] = $data['Privilege'];
					//$_COOKIE['nakaLogin'] = 1;
					
					if($accountType == 1){
						$_SESSION['position'] = "Officer";
					}else if($accountType == 2){
						$_SESSION['position'] = "DTS  Officer";
					}elseif($accountType == 3){
						$_SESSION['position'] = "Doctrack Administrator";
					}elseif($accountType == 4){
						$_SESSION['position'] = "Master Receiver";
					}elseif($accountType == 5){
						$_SESSION['position'] = "Master Releaser";
					}elseif($accountType == 6){
						$_SESSION['position'] = "Pending Master";
					}elseif($accountType == 7){
						$_SESSION['position'] = "Programmer";
					}elseif($accountType == 8){
						$_SESSION['position'] = "SLP Master";
					}elseif($accountType == 9){
						$_SESSION['position'] = "Master Adviser";
					}elseif($accountType == 10){
						$_SESSION['position'] = "BAC Officer";
					}
					
					/*$smsControl = 0;
					$sql = "select * from citydoc.sms where office = '" . $office . "' and employeenumber = '" . $employeeNumber . "' limit 1";
					$record = $database->query($sql);
					$count = $database->num_rows($record);
					if($count > 0){
						$data = $database->fetch_array($record);
						$_SESSION['smsControl'] = $data['Control'];
						$_SESSION['smsStatuses'] = $data['Statuses'];
						$_SESSION['smsNumbers'] = $data['Numbers'];
					}*/
					
					echo 1;
				}
			}else{
				echo 2;
			}
		}else{
			echo 3;
		}
	}

	if(isset($_GET['logout'])){
		
		$sql = "Update  citydoc.users set LoginState = 0 where Id  = " . $_SESSION['FkyhaXs'] . " limit 1";
		$database->query($sql);
		
		//$_COOKIE['nakaLogin'] = 2;
		unset($_SESSION['fullName']);
		unset($_SESSION['officeName']);
		unset($_SESSION['officeCode']);
		unset($_SESSION['position']);
		unset($_SESSION['poster']);
		unset($_SESSION['employeeNumber']);
		unset($_SESSION['accountType']);
		unset($_SESSION['asdf']);
		unset($_SESSION['privy']);
	
		session_destroy();
		
		$database->close_connection();
	}


	if (isset($_GET['searchTrackingNumber'])) {
		$trackingNumber = isset($_GET['trackingNumber']) ? $database->charEncoder($_GET['trackingNumber']) : "";
		$userOffice = isset($_GET['officeCode']) ? $database->charEncoder($_GET['officeCode']) : "";
		$trackingyear = isset($_GET['trackingyear']) ? $database->charEncoder($_GET['trackingyear']) : "";

		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );

		echo $sheet->CreateTrackerInterfaceResult($trackingNumber, $newRecord,$trackingyear);
	}


	if (isset($_GET['revertTrackingNumberQR'])) {
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$userOffice = $database->charEncoder($_GET['officeCode']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);

		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );

		$sql= "SELECT * FROM citydoc$trackingyear.voucherhistory
			WHERE TrackingNumber = '$trackingNumber'
			ORDER BY ID DESC
			LIMIT 1 OFFSET 1";

		$result = $database->query($sql);

		$data = $database->fetch_array($result);

		$status = $data['Status'];
		$status = str_replace("Changed to : ", "", $status);
		$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
		$completion = '';
		$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
		$database->UpdateVoucherHistory($trackingNumber,$trackingyear);   
		$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
	
		echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
	
	}


	if (isset($_GET['receiveTrackingNumberQR'])) {
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$userOffice = $database->charEncoder($_GET['officeCode']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);

		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear);

			if ($newRecord['TrackingType'] == 'PR') {


				if($userOffice == '1071'){  // CBO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'Encoded' || $newRecord['Status'] == 'Pending Released - CBO') {
						$status = 'CBO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}

				
				if($userOffice == '1061'){  // GSO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'CBO Received' || $newRecord['Status'] == 'Pending Released - GSO') {
						$status = 'GSO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}

				if($userOffice == '1091'){  // CTO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'GSO Released' || $newRecord['Status'] == 'Pending Released - CTO') {
						$status = 'Admin Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}


				if($userOffice == '1031'){  // ADMIN RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'CTO Received' || $newRecord['Status'] == 'Pending Released - Admin') {
						$status = 'Admin Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
						$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}

			
			}

			if ($newRecord['TrackingType'] == 'PO') {

				if($userOffice == '1061'){  // GSO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'Encoded' || $newRecord['Status'] == 'Pending Released - GSO') {
						$status = 'GSO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else if ($newRecord['Status'] == 'CAO Received'){
						$successMessage = "<div style='text-align: center;'>
											<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif; display: inline-block;'>
												Already Received
											</span>
										</div>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}

				if($userOffice == '1031'){  // ADMIN RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'GSO Received' || $newRecord['Status'] == 'Pending Released - Admin') {
						$status = 'Admin Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
						$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
						$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);
						
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}
			
			}


			if ($newRecord['TrackingType'] == 'PX') {

				if($userOffice == '1061'){ 
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'For Tagging' || $newRecord['Status'] == 'Pending Released - Inspection') {  // GSO RECEIVED Voucher Received - Inspection
						$status = 'Voucher Received - Inspection';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else if ($newRecord['Status'] == 'Voucher Received - Inspection' || $newRecord['Status'] == 'Pending Released - Inventory') {
						$status = 'Voucher Received - Inventory';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}

				

				if($userOffice == '1081'){  // CAO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'Voucher Received - Inventory' || $newRecord['Status'] == 'Pending Released - CAO') {
						$status = 'CAO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else if ($newRecord['Status'] == 'CAO Received'){
						$successMessage = "<div style='text-align: center;'>
											<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif; display: inline-block;'>
												Already Received
											</span>
										</div>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}



				if($userOffice == '1031'){  // ADMIN RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if($newRecord['Status']  == "Check Preparation - CTO"){
						if($_SESSION['accountType'] == 2 && $_SESSION['gso'] == '1031' && $_SESSION['privy'] == 5){
							$status = 'Check Received - Operation';
							$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
							$completion = '';
							$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
							$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
							$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
							$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);
						}
						elseif($_SESSION['accountType'] == 2 && $_SESSION['gso'] == '1031' && $_SESSION['privy'] == 8){
							$status = 'Check Received - Administration';
							$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
							$completion = '';
							$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
							$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
							$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
							$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);

						}

					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}
			
			}

			if ($newRecord['TrackingType'] == 'PY') {

				if($userOffice == '1031'){  // ADMIN RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'Encoded' || $newRecord['Status'] == 'Pending Released - Admin Operation' ) {
						$status = 'Admin Operation Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
						$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
						
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}

				if($userOffice == '1071'){  // CBO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'Admin Operation Received' || $newRecord['Status'] == 'Pending Released - CBO') {
						$status = 'CBO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else if ($newRecord['Status'] == 'CAO Received'){
						$successMessage = "<div style='text-align: center;'>
											<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif; display: inline-block;'>
												Already Received
											</span>
										</div>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}

				if($userOffice == '1081'){  // CAO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'CBO Released' || $newRecord['Status']  == 'Pending Released - CAO') {
						$status = 'CAO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else if ($newRecord['Status']  == 'CAO Received'){
						$successMessage = "<div style='text-align: center;'>
											<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif; display: inline-block;'>
												Already Received
											</span>
										</div>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
		
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );

				}
			}

			if ($newRecord['TrackingType'] == 'IP') {

				if($userOffice == '1081'){  // CAO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status']== 'Encoded') {
						$status = 'CAO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else if ($newRecord['Status'] == 'CAO Received'){
						$successMessage = "<div style='text-align: center;'>
											<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif; display: inline-block;'>
												Already Received
											</span>
										</div>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}

					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}


			}

			
			if ($newRecord['TrackingType'] == 'NF') {


				if($userOffice == '1031'){  // ADMIN RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';
					// echo $newRecord['Status'];
					// echo $_SESSION['accountType'];

					if($newRecord['Status']  == "Preparation of Plans, PoW, and Detailed Estimates"){
						if(($_SESSION['accountType'] == 2 || $_SESSION['accountType'] == 7)&& $_SESSION['gso'] == '1031'){
							$status = 'Plans, PoW, and Detailed Estimates for Approval';
							
							$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
							$completion = '';
							$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
							$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
							$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
							$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);
						}	

					}else if($newRecord['Status']  == "Plans, PoW, and Detailed Estimates for Approval"){
						if(($_SESSION['accountType'] == 2 || $_SESSION['accountType'] == 7)&& $_SESSION['gso'] == '1031'){
							$status = 'Plans, PoW, and Detailed Estimates Signed';
							
							$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
							$completion = '';
							$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
							$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
							$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
							$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);
						}	

					}else if($newRecord['Status']  == "Notice of Award for Transmit"){
						if(($_SESSION['accountType'] == 2 || $_SESSION['accountType'] == 7)&& $_SESSION['gso'] == '1031'){
							$status = 'Notice of Award for Admin Signature';
							
							$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
							$completion = '';
							$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
							$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
							$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
							$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);
						}	

					}else if($newRecord['Status']  == "Notice of Award for Admin Signature"){
						if(($_SESSION['accountType'] == 2 || $_SESSION['accountType'] == 7)&& $_SESSION['gso'] == '1031'){
							$status = 'Notice of Award Admin Signed';
							
							$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
							$completion = '';
							$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
							$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
							$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
							$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);
						}	

					}else if($newRecord['Status']  == "Contract and NTP Transmit to Admin"){
						if(($_SESSION['accountType'] == 2 || $_SESSION['accountType'] == 7)&& $_SESSION['gso'] == '1031'){
							$status = 'Contract and NTP for Admin Signature';
							
							$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
							$completion = '';
							$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
							$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
							$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
							$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);
						}	

					}else if($newRecord['Status']  == "Contract and NTP for Admin Signature"){
						if(($_SESSION['accountType'] == 2 || $_SESSION['accountType'] == 7)&& $_SESSION['gso'] == '1031'){
							$status = 'Document for Pick-up - Admin';
							
							$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
							$completion = '';
							$database->UpdateTrackingStatus($updateCase, $trackingNumber,$trackingyear);
							$database->UpdateVoucherHistory($trackingNumber,$trackingyear);            
							$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion,$trackingyear);
							$database->updateRealtimeLookup($trackingyear, $trackingNumber, $status, $dateEncoded);
						}	

					}

					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}




				

				if($userOffice == '1071'){  // CBO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'OBR Implementing Office Signed' || $newRecord['Status'] == 'Pending Released - CBO') {
						$status = 'CBO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else if ($newRecord['Status'] == 'CAO Received'){
						$successMessage = "<div style='text-align: center;'>
											<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif; display: inline-block;'>
												Already Received
											</span>
										</div>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
				}


				if($userOffice == '1081'){  // CAO RECEIVED
					$successMessage = '';
					$unsuccessMessage = '';

					if ($newRecord['Status'] == 'Document Filing' ) {
						$status = 'CAO Received';
						$updateCase = "Status = '" . $status . "', ModifiedBy = '" . $employeeNumber . "', DateModified = '" . $dateEncoded . "'";
						$completion = '';
						$database->UpdateTrackingStatus($updateCase, $trackingNumber);
						$database->UpdateVoucherHistory($trackingNumber);            
						$database->InsertToVoucherHistory($trackingNumber,$employeeNumber,$dateEncoded,$status,$completion);
						$successMessage = "<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif;'>Received Successful</span>";
					}else if ($newRecord['Status'] == 'CAO Received'){
						$successMessage = "<div style='text-align: center;'>
											<span style='color: white; background-color: green; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif; display: inline-block;'>
												Already Received
											</span>
										</div>";
					}else {
						$unsuccessMessage = "<span style='color: white; background-color: red; padding: 5px 10px; border-radius: 2px; font-weight: bold; font-family: Arial, sans-serif;'>Receive Unsuccessful</span>";
					}
		
					echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );

				}
			}
			
		
	}

	
?>




















