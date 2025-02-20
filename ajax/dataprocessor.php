

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
					$CEOCHECKER = $data['CEOCHECKER'];
					$CEOPDD = $data['CEOPDD'];
					$_SESSION['CEOCHECKER'] = $CEOCHECKER;
					$_SESSION['CEOPDD'] = $CEOPDD;
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


	if(isset($_GET['UpdatePreConDateVisit'])){
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$datevisit = $database->charEncoder($_GET['datevisit']);
		$container = $database->charEncoder($_GET['container']);
		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );

		$sql = "Update  citydoc$trackingyear.infrauploads set DateVisit = '" . $datevisit . "' where trackingNumber  = '" . $trackingNumber . "' ";
		$database->query($sql);

		if($container == 'searchcontainer'){
			echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}
	}


	if(isset($_GET['UpdateLocation'])){
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$location = $database->charEncoder($_GET['location']);
		$container = $database->charEncoder($_GET['container']);

		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );

		$sql = "Update  citydoc$trackingyear.infra set Location = '" . $location . "' where trackingNumber  = '" . $trackingNumber . "' ";
		$database->query($sql);
		// echo $container;
		if($container == 'searchcontainer'){
			echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}

	}


	if(isset($_GET['UpdateMap'])){
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$map = $database->charEncoder($_GET['map']);
		$container = $database->charEncoder($_GET['container']);

		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );

		$sql = "Update  citydoc$trackingyear.infra set Map = '" . $map . "' where trackingNumber  = '" . $trackingNumber . "' ";
		$database->query($sql);
		// echo $container;
		if($container == 'searchcontainer'){
			echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}

	}


	if(isset($_GET['UpdateCoordinates'])){
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$coordinates = $database->charEncoder($_GET['coordinates']);
		$container = $database->charEncoder($_GET['container']);

		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );

		$sql = "Update  citydoc$trackingyear.infra set Coordinates = '" . $coordinates . "' where trackingNumber  = '" . $trackingNumber . "' ";
		$database->query($sql);
		// echo $container;
		if($container == 'searchcontainer'){
			echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}

	}

	if(isset($_GET['Updatevideolink'])){
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$videolink = $database->charEncoder($_GET['videolink']);
		$container = $database->charEncoder($_GET['container']);
		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );

		// Check if a record with Type = 'Video' exists
		$checkSql = "SELECT COUNT(*) as count FROM citydoc$trackingyear.infrauploads WHERE trackingNumber = '$trackingNumber' AND Type = 'Video'";
		$result = $database->query($checkSql);
		$row = $result->fetch_assoc();

		if ($row['count'] > 0) {
			// If the record exists, update it
			$sql = "UPDATE citydoc$trackingyear.infrauploads 
					SET Filename = '$videolink' 
					WHERE trackingNumber = '$trackingNumber' AND Type = 'Video'";
		} else {
			// If no record exists, insert a new one
			$sql = "INSERT INTO citydoc$trackingyear.infrauploads (trackingNumber, Type, Filename) 
					VALUES ('$trackingNumber', 'Video', '$videolink')";
		}

		// Execute the query
		$database->query($sql);

		if($container == 'searchcontainer'){
			echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}else if($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}

	}
	if(isset($_GET['UpdateStatus'])){
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$status = $database->charEncoder($_GET['status']);
		$container = $database->charEncoder($_GET['container']);
		// echo $container;

		$sql = "Update  citydoc$trackingyear.vouchercurrent set Status = '" . $status . "' where trackingNumber  = '" . $trackingNumber . "' ";
		$database->query($sql);

		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );
		if($container == 'searchcontainer'){
			echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
		}else if ($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}else if ($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}
	}

	if(isset($_GET['UpdateBrgy'])){
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$brgy = $database->charEncoder($_GET['brgy']);
		$container = $database->charEncoder($_GET['container']);


		$sql = "Update  citydoc$trackingyear.infra set Barangay = '" . $brgy . "' where trackingNumber  = '" . $trackingNumber . "' ";
		$database->query($sql);
		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );
		if($container == 'searchcontainer'){
			echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
		}else if ($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}else if ($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}

	}

	if(isset($_GET['editprogrammer'])){
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$container = $database->charEncoder($_GET['container']);
		$role = $database->charEncoder($_GET['role']);
		// echo $role;
		if($role == 'PoW Lead Engineer'){
			$sql = "
					SELECT i.LastName, i.FirstName, i.Suffix, i.EmployeeNumber, i.MiddleName, i.Title,
						pm.EmployeeNumber AS Assigned, pm.Function
					FROM citydoc$trackingyear.inframanpower i
					LEFT JOIN citydoc$trackingyear.projectmanpower pm
					ON i.EmployeeNumber = pm.EmployeeNumber 
					AND pm.TrackingNumber = '$trackingNumber'
					AND pm.Function = 'Pos2' where i.Type = 'Infra'
					group by i.EmployeeNumber 
					ORDER BY i.LastName, i.FirstName ASC
				";

				$result = $database->query($sql);

				$programmerListHtml = "";

				// Generate checkboxes dynamically
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_array()) {

						$empnum = $row['EmployeeNumber'];
						$lname = $row['LastName'];
						$fname = $row['FirstName'];
						$mname = $row['MiddleName'];
						$suff = $row['Suffix'];
						$title = $row['Title'];
						$manFunct = $row['Function'];

						if(strlen(trim($mname)) > 0) {
							$mname = $row['MiddleName'][0].".";
						}

						if(strlen(trim($suff)) > 0) {
							$suff = ", ".$row['Suffix'];
						}
						
						$fullName = $lname.' '.$fname.' '.$mname.$suff;
		
						$id = "programmer_" . md5($fullName); // Unique ID for each checkbox
						$empNum = $row["EmployeeNumber"];

						// If Assigned is not NULL, the checkbox should be checked
						$checked = $row["Assigned"] !== null ? "checked" : "";

						// $programmerListHtml .= '<div style="display:flex;align-items:center;">
						// 	<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
						// 		data-empnum="' . htmlspecialchars($empNum) . '" style="">
						// 	<label for="' . $id . '" style="margin-left:.3rem; cursor:pointer;">' . htmlspecialchars($fullName) . '</label>
						// </div>';

						$programmerListHtml .= '
							<table border="0">
								<tr>
								<td style="vertical-align:top;width:1px;padding-top:0.21rem;">
									<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
									data-empnum="' . htmlspecialchars($empNum) . '" style="">
								</td> 
								<td style="padding-left:.3rem;">
									<label for="' . $id . '" style=" cursor:pointer;text-align:left;font-size:1.1em;">' . htmlspecialchars($fullName) . '</label>
								</td>
								</tr>
							</table>';
					}
				} else {
					$programmerListHtml = "<p>No programmers found.</p>";
				}

			echo '<div id="editprogrammerModal" class="uploader-overlay">
					<div class="uploader-content">
						<span class="close-btn" onclick="closeEditRole(\'editprogrammerModal\')">&times;</span>
						<h2>Assign Programmer</h2>

						<div id="programmerList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 2px 5px;font-size:1em;">
							' . $programmerListHtml . '
						</div>

						<button class="btn btn-primary" onclick="assignProgrammers(\'' . $trackingNumber . '\', \'' . $trackingyear . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($empNum) . '\',\'' . htmlspecialchars($fullName) . '\',\'' . htmlspecialchars($container) . '\')">Assign</button>
					</div>
			</div>';
		}


		if($role == 'PoW Civil Engineer'){
			$sql = "
					SELECT i.LastName, i.FirstName, i.Suffix, i.EmployeeNumber, i.MiddleName, i.Title,
						pm.EmployeeNumber AS Assigned, pm.Function
					FROM citydoc$trackingyear.inframanpower i
					LEFT JOIN citydoc$trackingyear.projectmanpower pm
					ON i.EmployeeNumber = pm.EmployeeNumber 
					AND pm.TrackingNumber = '$trackingNumber'
					AND pm.Function = 'Pos1' where i.Type = 'Infra'
					group by i.EmployeeNumber 
					ORDER BY i.LastName, i.FirstName ASC
				";

				$result = $database->query($sql);

				$programmerListHtml = "";

				// Generate checkboxes dynamically
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_array()) {

						$empnum = $row['EmployeeNumber'];
						$lname = $row['LastName'];
						$fname = $row['FirstName'];
						$mname = $row['MiddleName'];
						$suff = $row['Suffix'];
						$title = $row['Title'];
						$manFunct = $row['Function'];

						if(strlen(trim($mname)) > 0) {
							$mname = $row['MiddleName'][0].".";
						}

						if(strlen(trim($suff)) > 0) {
							$suff = ", ".$row['Suffix'];
						}
						
						$fullName = $lname.' '.$fname.' '.$mname.$suff;
		
						$id = "programmer_" . md5($fullName); // Unique ID for each checkbox
						$empNum = $row["EmployeeNumber"];

						// If Assigned is not NULL, the checkbox should be checked
						$checked = $row["Assigned"] !== null ? "checked" : "";

						// $programmerListHtml .= '<div style="display:flex;align-items:center;">
						// 	<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
						// 		data-empnum="' . htmlspecialchars($empNum) . '" style="">
						// 	<label for="' . $id . '" style="margin-left:.3rem; cursor:pointer;">' . htmlspecialchars($fullName) . '</label>
						// </div>';

						$programmerListHtml .= '
							<table border="0">
								<tr>
								<td style="vertical-align:top;width:1px;padding: top 0.21em;rem;">
									<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
									data-empnum="' . htmlspecialchars($empNum) . '" style="">
								</td> 
								<td style="padding-left:.3rem;">
									<label for="' . $id . '" style=" cursor:pointer;text-align:left;font-size:1.1em;">' . htmlspecialchars($fullName) . '</label>
								</td>
								</tr>
							</table>';
					}
				} else {
					$programmerListHtml = "<p>No programmers found.</p>";
				}

			echo '<div id="editprogrammerModal" class="uploader-overlay">
					<div class="uploader-content">
						<span class="close-btn" onclick="closeEditRole(\'editprogrammerModal\')">&times;</span>
						<h2>Assign Programmer</h2>

						<div id="programmerList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 2px 5px;font-size:1em;">
							' . $programmerListHtml . '
						</div>

						<button class="btn btn-primary" onclick="assignProgrammers(\'' . $trackingNumber . '\', \'' . $trackingyear . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($empNum) . '\',\'' . htmlspecialchars($fullName) . '\',\'' . htmlspecialchars($container) . '\')">Assign</button>
					</div>
			</div>';
		}

		if($role == 'Project Surveyor'){
			$sql = "
					SELECT i.LastName, i.FirstName, i.Suffix, i.EmployeeNumber, i.MiddleName, i.Title,
						pm.EmployeeNumber AS Assigned, pm.Function
					FROM citydoc$trackingyear.inframanpower i
					LEFT JOIN citydoc$trackingyear.projectmanpower pm
					ON i.EmployeeNumber = pm.EmployeeNumber 
					AND pm.TrackingNumber = '$trackingNumber'
					AND pm.Function = 'Surveyor' where i.Type = 'Infra'
					group by i.EmployeeNumber 
					ORDER BY i.LastName, i.FirstName ASC
				";

				$result = $database->query($sql);

				$programmerListHtml = "";

				// Generate checkboxes dynamically
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_array()) {

						$empnum = $row['EmployeeNumber'];
						$lname = $row['LastName'];
						$fname = $row['FirstName'];
						$mname = $row['MiddleName'];
						$suff = $row['Suffix'];
						$title = $row['Title'];
						$manFunct = $row['Function'];

						if(strlen(trim($mname)) > 0) {
							$mname = $row['MiddleName'][0].".";
						}

						if(strlen(trim($suff)) > 0) {
							$suff = ", ".$row['Suffix'];
						}
						
						$fullName = $lname.' '.$fname.' '.$mname.$suff;
		
						$id = "programmer_" . md5($fullName); // Unique ID for each checkbox
						$empNum = $row["EmployeeNumber"];

						// If Assigned is not NULL, the checkbox should be checked
						$checked = $row["Assigned"] !== null ? "checked" : "";

						// $programmerListHtml .= '<div style="display:flex;align-items:center;">
						// 	<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
						// 		data-empnum="' . htmlspecialchars($empNum) . '" style="">
						// 	<label for="' . $id . '" style="margin-left:.3rem; cursor:pointer;">' . htmlspecialchars($fullName) . '</label>
						// </div>';

						$programmerListHtml .= '
							<table border="0">
								<tr>
								<td style="vertical-align:top;width:1px;padding: top 0.21em;rem;">
									<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
									data-empnum="' . htmlspecialchars($empNum) . '" style="">
								</td> 
								<td style="padding-left:.3rem;">
									<label for="' . $id . '" style=" cursor:pointer;text-align:left;font-size:1.1em;">' . htmlspecialchars($fullName) . '</label>
								</td>
								</tr>
							</table>';
					}
				} else {
					$programmerListHtml = "<p>No programmers found.</p>";
				}

			echo '<div id="editprogrammerModal" class="uploader-overlay">
					<div class="uploader-content">
						<span class="close-btn" onclick="closeEditRole(\'editprogrammerModal\')">&times;</span>
						<h2>Assign Programmer</h2>

						<div id="programmerList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 2px 5px;font-size:1em;">
							' . $programmerListHtml . '
						</div>

						<button class="btn btn-primary" onclick="assignProgrammers(\'' . $trackingNumber . '\', \'' . $trackingyear . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($empNum) . '\',\'' . htmlspecialchars($fullName) . '\',\'' . htmlspecialchars($container) . '\')">Assign</button>
					</div>
			</div>';
		}

		if($role == 'Project Architect'){
			$sql = "
					SELECT i.LastName, i.FirstName, i.Suffix, i.EmployeeNumber, i.MiddleName, i.Title,
						pm.EmployeeNumber AS Assigned, pm.Function
					FROM citydoc$trackingyear.inframanpower i
					LEFT JOIN citydoc$trackingyear.projectmanpower pm
					ON i.EmployeeNumber = pm.EmployeeNumber 
					AND pm.TrackingNumber = '$trackingNumber'
					AND pm.Function = 'Pos3' where i.Type = 'Infra'
					group by i.EmployeeNumber 
					ORDER BY i.LastName, i.FirstName ASC
				";

				$result = $database->query($sql);

				$programmerListHtml = "";

				// Generate checkboxes dynamically
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_array()) {

						$empnum = $row['EmployeeNumber'];
						$lname = $row['LastName'];
						$fname = $row['FirstName'];
						$mname = $row['MiddleName'];
						$suff = $row['Suffix'];
						$title = $row['Title'];
						$manFunct = $row['Function'];

						if(strlen(trim($mname)) > 0) {
							$mname = $row['MiddleName'][0].".";
						}

						if(strlen(trim($suff)) > 0) {
							$suff = ", ".$row['Suffix'];
						}
						
						$fullName = $lname.' '.$fname.' '.$mname.$suff;
		
						$id = "programmer_" . md5($fullName); // Unique ID for each checkbox
						$empNum = $row["EmployeeNumber"];

						// If Assigned is not NULL, the checkbox should be checked
						$checked = $row["Assigned"] !== null ? "checked" : "";

						// $programmerListHtml .= '<div style="display:flex;align-items:center;">
						// 	<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
						// 		data-empnum="' . htmlspecialchars($empNum) . '" style="">
						// 	<label for="' . $id . '" style="margin-left:.3rem; cursor:pointer;">' . htmlspecialchars($fullName) . '</label>
						// </div>';

						$programmerListHtml .= '
							<table border="0">
								<tr>
								<td style="vertical-align:top;width:1px;padding: top 0.21em;rem;">
									<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
									data-empnum="' . htmlspecialchars($empNum) . '" style="">
								</td> 
								<td style="padding-left:.3rem;">
									<label for="' . $id . '" style=" cursor:pointer;text-align:left;font-size:1.1em;">' . htmlspecialchars($fullName) . '</label>
								</td>
								</tr>
							</table>';
					}
				} else {
					$programmerListHtml = "<p>No programmers found.</p>";
				}

			echo '<div id="editprogrammerModal" class="uploader-overlay">
					<div class="uploader-content">
						<span class="close-btn" onclick="closeEditRole(\'editprogrammerModal\')">&times;</span>
						<h2>Assign Programmer</h2>

						<div id="programmerList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 2px 5px;font-size:1em;">
							' . $programmerListHtml . '
						</div>

						<button class="btn btn-primary" onclick="assignProgrammers(\'' . $trackingNumber . '\', \'' . $trackingyear . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($empNum) . '\',\'' . htmlspecialchars($fullName) . '\',\'' . htmlspecialchars($container) . '\')">Assign</button>
					</div>
			</div>';
		}

		if($role == 'Project Electrical Engineer'){
			$sql = "
					SELECT i.LastName, i.FirstName, i.Suffix, i.EmployeeNumber, i.MiddleName, i.Title,
						pm.EmployeeNumber AS Assigned, pm.Function
					FROM citydoc$trackingyear.inframanpower i
					LEFT JOIN citydoc$trackingyear.projectmanpower pm
					ON i.EmployeeNumber = pm.EmployeeNumber 
					AND pm.TrackingNumber = '$trackingNumber'
					AND pm.Function = 'Pos4' where i.Type = 'Infra'
					group by i.EmployeeNumber 
					ORDER BY i.LastName, i.FirstName ASC
				";

				$result = $database->query($sql);

				$programmerListHtml = "";

				// Generate checkboxes dynamically
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_array()) {

						$empnum = $row['EmployeeNumber'];
						$lname = $row['LastName'];
						$fname = $row['FirstName'];
						$mname = $row['MiddleName'];
						$suff = $row['Suffix'];
						$title = $row['Title'];
						$manFunct = $row['Function'];

						if(strlen(trim($mname)) > 0) {
							$mname = $row['MiddleName'][0].".";
						}

						if(strlen(trim($suff)) > 0) {
							$suff = ", ".$row['Suffix'];
						}
						
						$fullName = $lname.' '.$fname.' '.$mname.$suff;
		
						$id = "programmer_" . md5($fullName); // Unique ID for each checkbox
						$empNum = $row["EmployeeNumber"];

						// If Assigned is not NULL, the checkbox should be checked
						$checked = $row["Assigned"] !== null ? "checked" : "";

						// $programmerListHtml .= '<div style="display:flex;align-items:center;">
						// 	<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
						// 		data-empnum="' . htmlspecialchars($empNum) . '" style="">
						// 	<label for="' . $id . '" style="margin-left:.3rem; cursor:pointer;">' . htmlspecialchars($fullName) . '</label>
						// </div>';

						$programmerListHtml .= '
							<table border="0">
								<tr>
								<td style="vertical-align:top;width:1px;padding: top 0.21em;rem;">
									<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
									data-empnum="' . htmlspecialchars($empNum) . '" style="">
								</td> 
								<td style="padding-left:.3rem;">
									<label for="' . $id . '" style=" cursor:pointer;text-align:left;font-size:1.1em;">' . htmlspecialchars($fullName) . '</label>
								</td>
								</tr>
							</table>';
					}
				} else {
					$programmerListHtml = "<p>No programmers found.</p>";
				}

			echo '<div id="editprogrammerModal" class="uploader-overlay">
					<div class="uploader-content">
						<span class="close-btn" onclick="closeEditRole(\'editprogrammerModal\')">&times;</span>
						<h2>Assign Programmer</h2>

						<div id="programmerList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 2px 5px;font-size:1em;">
							' . $programmerListHtml . '
						</div>

						<button class="btn btn-primary" onclick="assignProgrammers(\'' . $trackingNumber . '\', \'' . $trackingyear . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($empNum) . '\',\'' . htmlspecialchars($fullName) . '\',\'' . htmlspecialchars($container) . '\')">Assign</button>
					</div>
			</div>';
		}


		if($role == 'Project Plumber'){
			$sql = "
					SELECT i.LastName, i.FirstName, i.Suffix, i.EmployeeNumber, i.MiddleName, i.Title,
						pm.EmployeeNumber AS Assigned, pm.Function
					FROM citydoc$trackingyear.inframanpower i
					LEFT JOIN citydoc$trackingyear.projectmanpower pm
					ON i.EmployeeNumber = pm.EmployeeNumber 
					AND pm.TrackingNumber = '$trackingNumber'
					AND pm.Function = 'Pos5' where i.Type = 'Infra'
					group by i.EmployeeNumber 
					ORDER BY i.LastName, i.FirstName ASC
				";

				$result = $database->query($sql);

				$programmerListHtml = "";

				// Generate checkboxes dynamically
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_array()) {

						$empnum = $row['EmployeeNumber'];
						$lname = $row['LastName'];
						$fname = $row['FirstName'];
						$mname = $row['MiddleName'];
						$suff = $row['Suffix'];
						$title = $row['Title'];
						$manFunct = $row['Function'];

						if(strlen(trim($mname)) > 0) {
							$mname = $row['MiddleName'][0].".";
						}

						if(strlen(trim($suff)) > 0) {
							$suff = ", ".$row['Suffix'];
						}
						
						$fullName = $lname.' '.$fname.' '.$mname.$suff;
		
						$id = "programmer_" . md5($fullName); // Unique ID for each checkbox
						$empNum = $row["EmployeeNumber"];

						// If Assigned is not NULL, the checkbox should be checked
						$checked = $row["Assigned"] !== null ? "checked" : "";

						// $programmerListHtml .= '<div style="display:flex;align-items:center;">
						// 	<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
						// 		data-empnum="' . htmlspecialchars($empNum) . '" style="">
						// 	<label for="' . $id . '" style="margin-left:.3rem; cursor:pointer;">' . htmlspecialchars($fullName) . '</label>
						// </div>';

						$programmerListHtml .= '
							<table border="0">
								<tr>
								<td style="vertical-align:top;width:1px;padding-top:0.21rem;">
									<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
									data-empnum="' . htmlspecialchars($empNum) . '" style="">
								</td> 
								<td style="padding-left:.3rem;">
									<label for="' . $id . '" style=" cursor:pointer;text-align:left;font-size:1.1em;">' . htmlspecialchars($fullName) . '</label>
								</td>
								</tr>
							</table>';
					}
				} else {
					$programmerListHtml = "<p>No programmers found.</p>";
				}

			echo '<div id="editprogrammerModal" class="uploader-overlay">
					<div class="uploader-content">
						<span class="close-btn" onclick="closeEditRole(\'editprogrammerModal\')">&times;</span>
						<h2>Assign Programmer</h2>

						<div id="programmerList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 2px 5px;font-size:1em;">
							' . $programmerListHtml . '
						</div>

						<button class="btn btn-primary" onclick="assignProgrammers(\'' . $trackingNumber . '\', \'' . $trackingyear . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($empNum) . '\',\'' . htmlspecialchars($fullName) . '\',\'' . htmlspecialchars($container) . '\')">Assign</button>
					</div>
			</div>';
		}

		if($role == 'Project Structural Engineer'){
			$sql = "
					SELECT i.LastName, i.FirstName, i.Suffix, i.EmployeeNumber, i.MiddleName, i.Title,
						pm.EmployeeNumber AS Assigned, pm.Function
					FROM citydoc$trackingyear.inframanpower i
					LEFT JOIN citydoc$trackingyear.projectmanpower pm
					ON i.EmployeeNumber = pm.EmployeeNumber 
					AND pm.TrackingNumber = '$trackingNumber'
					AND pm.Function = 'Pos6' where i.Type = 'Infra'
					group by i.EmployeeNumber 
					ORDER BY i.LastName, i.FirstName ASC;
				";

				$result = $database->query($sql);

				$programmerListHtml = "";

				// Generate checkboxes dynamically
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_array()) {

						$empnum = $row['EmployeeNumber'];
						$lname = $row['LastName'];
						$fname = $row['FirstName'];
						$mname = $row['MiddleName'];
						$suff = $row['Suffix'];
						$title = $row['Title'];
						$manFunct = $row['Function'];

						if(strlen(trim($mname)) > 0) {
							$mname = $row['MiddleName'][0].".";
						}

						if(strlen(trim($suff)) > 0) {
							$suff = ", ".$row['Suffix'];
						}
						
						$fullName = $lname.' '.$fname.' '.$mname.$suff;
		
						$id = "programmer_" . md5($fullName); // Unique ID for each checkbox
						$empNum = $row["EmployeeNumber"];

						// If Assigned is not NULL, the checkbox should be checked
						$checked = $row["Assigned"] !== null ? "checked" : "";

						// $programmerListHtml .= '<div style="display:flex;align-items:center;">
						// 	<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
						// 		data-empnum="' . htmlspecialchars($empNum) . '" style="">
						// 	<label for="' . $id . '" style="margin-left:.3rem; cursor:pointer;">' . htmlspecialchars($fullName) . '</label>
						// </div>';

						$programmerListHtml .= '
							<table border="0">
								<tr>
								<td style="vertical-align:top;width:1px;padding-top:0.21rem;">
									<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
									data-empnum="' . htmlspecialchars($empNum) . '" style="">
								</td> 
								<td style="padding-left:.3rem;">
									<label for="' . $id . '" style=" cursor:pointer;text-align:left;font-size:1.1em;">' . htmlspecialchars($fullName) . '</label>
								</td>
								</tr>
							</table>';
					}
				} else {
					$programmerListHtml = "<p>No programmers found.</p>";
				}

			echo '<div id="editprogrammerModal" class="uploader-overlay">
					<div class="uploader-content">
						<span class="close-btn" onclick="closeEditRole(\'editprogrammerModal\')">&times;</span>
						<h2>Assign Programmer</h2>

						<div id="programmerList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 2px 5px;font-size:1em;">
							' . $programmerListHtml . '
						</div>

						<button class="btn btn-primary" onclick="assignProgrammers(\'' . $trackingNumber . '\', \'' . $trackingyear . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($empNum) . '\',\'' . htmlspecialchars($fullName) . '\',\'' . htmlspecialchars($container) . '\')">Assign</button>
					</div>
			</div>';
		}


		if($role == 'Construction Inspector'){
			$sql = "
					SELECT i.LastName, i.FirstName, i.Suffix, i.EmployeeNumber, i.MiddleName, i.Title,
						pm.EmployeeNumber AS Assigned, pm.Function
					FROM citydoc$trackingyear.inframanpower i
					LEFT JOIN citydoc$trackingyear.projectmanpower pm
					ON i.EmployeeNumber = pm.EmployeeNumber 
					AND pm.TrackingNumber = '$trackingNumber'
					AND pm.Function = 'Inspector' where i.Type = 'Infra'
					group by i.EmployeeNumber 
					ORDER BY i.LastName, i.FirstName ASC
				";

				$result = $database->query($sql);

				$programmerListHtml = "";

				// Generate checkboxes dynamically
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_array()) {

						$empnum = $row['EmployeeNumber'];
						$lname = $row['LastName'];
						$fname = $row['FirstName'];
						$mname = $row['MiddleName'];
						$suff = $row['Suffix'];
						$title = $row['Title'];
						$manFunct = $row['Function'];

						if(strlen(trim($mname)) > 0) {
							$mname = $row['MiddleName'][0].".";
						}

						if(strlen(trim($suff)) > 0) {
							$suff = ", ".$row['Suffix'];
						}
						
						$fullName = $lname.' '.$fname.' '.$mname.$suff;
		
						$id = "programmer_" . md5($fullName); // Unique ID for each checkbox
						$empNum = $row["EmployeeNumber"];

						// If Assigned is not NULL, the checkbox should be checked
						$checked = $row["Assigned"] !== null ? "checked" : "";

						// $programmerListHtml .= '<div style="display:flex;align-items:center;">
						// 	<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
						// 		data-empnum="' . htmlspecialchars($empNum) . '" style="">
						// 	<label for="' . $id . '" style="margin-left:.3rem; cursor:pointer;">' . htmlspecialchars($fullName) . '</label>
						// </div>';

						$programmerListHtml .= '
							<table border="0">
								<tr>
								<td style="vertical-align:top;width:1px;padding-top:0.21rem;">
									<input id="' . $id . '" type="checkbox" name="programmer" value="' . htmlspecialchars($fullName) . '" ' . $checked . ' 
									data-empnum="' . htmlspecialchars($empNum) . '" style="">
								</td> 
								<td style="padding-left:.3rem;">
									<label for="' . $id . '" style=" cursor:pointer;text-align:left;font-size:1.1em;">' . htmlspecialchars($fullName) . '</label>
								</td>
								</tr>
							</table>';
					}
				} else {
					$programmerListHtml = "<p>No programmers found.</p>";
				}

			echo '<div id="editprogrammerModal" class="uploader-overlay">
					<div class="uploader-content">
						<span class="close-btn" onclick="closeEditRole(\'editprogrammerModal\')">&times;</span>
						<h2>Assign Programmer</h2>

						<div id="programmerList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 2px 5px;font-size:1em;">
							' . $programmerListHtml . '
						</div>

						<button class="btn btn-primary" onclick="assignProgrammers(\'' . $trackingNumber . '\', \'' . $trackingyear . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($empNum) . '\',\'' . htmlspecialchars($fullName) . '\',\'' . htmlspecialchars($container) . '\')">Assign</button>
					</div>
			</div>';
		}
		

	}


	if (isset($_GET['assignprogrammer'])) {
		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		$role = $database->charEncoder($_GET['role']);
		$container = $database->charEncoder($_GET['container']);
	
		if($role == 'PoW Lead Engineer'){
	
			// Get selected (checked) programmers
			$selectedRaw = isset($_GET['selectedProgrammers']) && !empty($_GET['selectedProgrammers']) 
						? explode("|", $_GET['selectedProgrammers']) 
						: [];
		
			// Step 1: Delete all previously assigned programmers for the same TrackingNumber & Function
			$deleteSql = "DELETE FROM citydoc" . $trackingyear . ".projectmanpower 
						WHERE TrackingNumber = '$trackingNumber' AND `Function` = 'Pos2'";
			$database->query($deleteSql);
		
			// Step 2: Insert newly selected programmers
			if (!empty($selectedRaw)) {
				$inserts = "";
				foreach ($selectedRaw as $programmer) {
					list($fullName, $empNum) = explode("~", $programmer);
					$fullName = $database->charEncoder($fullName);
					$empNum = $database->charEncoder($empNum);
		
					$inserts .= ", ('$trackingNumber', '$empNum', '$fullName', 'Pos2')";
				}
		
				if (!empty($inserts)) {
					$sql = "INSERT INTO citydoc" . $trackingyear . ".projectmanpower 
							(TrackingNumber, EmployeeNumber, Name, `Function`) 
							VALUES " . substr($inserts, 1);
					$database->query($sql);
				}
			}

			// echo '123123';
		}

		if($role == 'PoW Civil Engineer'){
	
			// Get selected (checked) programmers
			$selectedRaw = isset($_GET['selectedProgrammers']) && !empty($_GET['selectedProgrammers']) 
						? explode("|", $_GET['selectedProgrammers']) 
						: [];
		
			// Step 1: Delete all previously assigned programmers for the same TrackingNumber & Function
			$deleteSql = "DELETE FROM citydoc" . $trackingyear . ".projectmanpower 
						WHERE TrackingNumber = '$trackingNumber' AND `Function` = 'Pos1'";
			$database->query($deleteSql);
		
			// Step 2: Insert newly selected programmers
			if (!empty($selectedRaw)) {
				$inserts = "";
				foreach ($selectedRaw as $programmer) {
					list($fullName, $empNum) = explode("~", $programmer);
					$fullName = $database->charEncoder($fullName);
					$empNum = $database->charEncoder($empNum);
		
					$inserts .= ", ('$trackingNumber', '$empNum', '$fullName', 'Pos1')";
				}
		
				if (!empty($inserts)) {
					$sql = "INSERT INTO citydoc" . $trackingyear . ".projectmanpower 
							(TrackingNumber, EmployeeNumber, Name, `Function`) 
							VALUES " . substr($inserts, 1);
					$database->query($sql);
				}
			}
		}

		if($role == 'Project Surveyor'){
	
			// Get selected (checked) programmers
			$selectedRaw = isset($_GET['selectedProgrammers']) && !empty($_GET['selectedProgrammers']) 
						? explode("|", $_GET['selectedProgrammers']) 
						: [];
		
			// Step 1: Delete all previously assigned programmers for the same TrackingNumber & Function
			$deleteSql = "DELETE FROM citydoc" . $trackingyear . ".projectmanpower 
						WHERE TrackingNumber = '$trackingNumber' AND `Function` = 'Surveyor'";
			$database->query($deleteSql);
		
			// Step 2: Insert newly selected programmers
			if (!empty($selectedRaw)) {
				$inserts = "";
				foreach ($selectedRaw as $programmer) {
					list($fullName, $empNum) = explode("~", $programmer);
					$fullName = $database->charEncoder($fullName);
					$empNum = $database->charEncoder($empNum);
		
					$inserts .= ", ('$trackingNumber', '$empNum', '$fullName', 'Surveyor')";
				}
		
				if (!empty($inserts)) {
					$sql = "INSERT INTO citydoc" . $trackingyear . ".projectmanpower 
							(TrackingNumber, EmployeeNumber, Name, `Function`) 
							VALUES " . substr($inserts, 1);
					$database->query($sql);
				}
			}
		}

		if($role == 'Project Architect'){
	
			// Get selected (checked) programmers
			$selectedRaw = isset($_GET['selectedProgrammers']) && !empty($_GET['selectedProgrammers']) 
						? explode("|", $_GET['selectedProgrammers']) 
						: [];
		
			// Step 1: Delete all previously assigned programmers for the same TrackingNumber & Function
			$deleteSql = "DELETE FROM citydoc" . $trackingyear . ".projectmanpower 
						WHERE TrackingNumber = '$trackingNumber' AND `Function` = 'Pos3'";
			$database->query($deleteSql);
		
			// Step 2: Insert newly selected programmers
			if (!empty($selectedRaw)) {
				$inserts = "";
				foreach ($selectedRaw as $programmer) {
					list($fullName, $empNum) = explode("~", $programmer);
					$fullName = $database->charEncoder($fullName);
					$empNum = $database->charEncoder($empNum);
		
					$inserts .= ", ('$trackingNumber', '$empNum', '$fullName', 'Pos3')";
				}
		
				if (!empty($inserts)) {
					$sql = "INSERT INTO citydoc" . $trackingyear . ".projectmanpower 
							(TrackingNumber, EmployeeNumber, Name, `Function`) 
							VALUES " . substr($inserts, 1);
					$database->query($sql);
				}
			}
		}

		if($role == 'Project Electrical Engineer'){
	
			// Get selected (checked) programmers
			$selectedRaw = isset($_GET['selectedProgrammers']) && !empty($_GET['selectedProgrammers']) 
						? explode("|", $_GET['selectedProgrammers']) 
						: [];
		
			// Step 1: Delete all previously assigned programmers for the same TrackingNumber & Function
			$deleteSql = "DELETE FROM citydoc" . $trackingyear . ".projectmanpower 
						WHERE TrackingNumber = '$trackingNumber' AND `Function` = 'Pos4'";
			$database->query($deleteSql);
		
			// Step 2: Insert newly selected programmers
			if (!empty($selectedRaw)) {
				$inserts = "";
				foreach ($selectedRaw as $programmer) {
					list($fullName, $empNum) = explode("~", $programmer);
					$fullName = $database->charEncoder($fullName);
					$empNum = $database->charEncoder($empNum);
		
					$inserts .= ", ('$trackingNumber', '$empNum', '$fullName', 'Pos4')";
				}
		
				if (!empty($inserts)) {
					$sql = "INSERT INTO citydoc" . $trackingyear . ".projectmanpower 
							(TrackingNumber, EmployeeNumber, Name, `Function`) 
							VALUES " . substr($inserts, 1);
					$database->query($sql);
				}
			}
		}

		if($role == 'Project Plumber'){
	
			// Get selected (checked) programmers
			$selectedRaw = isset($_GET['selectedProgrammers']) && !empty($_GET['selectedProgrammers']) 
						? explode("|", $_GET['selectedProgrammers']) 
						: [];
		
			// Step 1: Delete all previously assigned programmers for the same TrackingNumber & Function
			$deleteSql = "DELETE FROM citydoc" . $trackingyear . ".projectmanpower 
						WHERE TrackingNumber = '$trackingNumber' AND `Function` = 'Pos5'";
			$database->query($deleteSql);
		
			// Step 2: Insert newly selected programmers
			if (!empty($selectedRaw)) {
				$inserts = "";
				foreach ($selectedRaw as $programmer) {
					list($fullName, $empNum) = explode("~", $programmer);
					$fullName = $database->charEncoder($fullName);
					$empNum = $database->charEncoder($empNum);
		
					$inserts .= ", ('$trackingNumber', '$empNum', '$fullName', 'Pos5')";
				}
		
				if (!empty($inserts)) {
					$sql = "INSERT INTO citydoc" . $trackingyear . ".projectmanpower 
							(TrackingNumber, EmployeeNumber, Name, `Function`) 
							VALUES " . substr($inserts, 1);
					$database->query($sql);
				}
			}
		}

		if($role == 'Project Structural Engineer'){
	
			// Get selected (checked) programmers
			$selectedRaw = isset($_GET['selectedProgrammers']) && !empty($_GET['selectedProgrammers']) 
						? explode("|", $_GET['selectedProgrammers']) 
						: [];
		
			// Step 1: Delete all previously assigned programmers for the same TrackingNumber & Function
			$deleteSql = "DELETE FROM citydoc" . $trackingyear . ".projectmanpower 
						WHERE TrackingNumber = '$trackingNumber' AND `Function` = 'Pos6'";
			$database->query($deleteSql);
		
			// Step 2: Insert newly selected programmers
			if (!empty($selectedRaw)) {
				$inserts = "";
				foreach ($selectedRaw as $programmer) {
					list($fullName, $empNum) = explode("~", $programmer);
					$fullName = $database->charEncoder($fullName);
					$empNum = $database->charEncoder($empNum);
		
					$inserts .= ", ('$trackingNumber', '$empNum', '$fullName', 'Pos6')";
				}
		
				if (!empty($inserts)) {
					$sql = "INSERT INTO citydoc" . $trackingyear . ".projectmanpower 
							(TrackingNumber, EmployeeNumber, Name, `Function`) 
							VALUES " . substr($inserts, 1);
					$database->query($sql);
				}
			}
		}


		if($role == 'Construction Inspector'){
	
			// Get selected (checked) programmers
			$selectedRaw = isset($_GET['selectedProgrammers']) && !empty($_GET['selectedProgrammers']) 
						? explode("|", $_GET['selectedProgrammers']) 
						: [];
		
			// Step 1: Delete all previously assigned programmers for the same TrackingNumber & Function
			$deleteSql = "DELETE FROM citydoc" . $trackingyear . ".projectmanpower 
						WHERE TrackingNumber = '$trackingNumber' AND `Function` = 'Inspector'";
			$database->query($deleteSql);
		
			// Step 2: Insert newly selected programmers
			if (!empty($selectedRaw)) {
				$inserts = "";
				foreach ($selectedRaw as $programmer) {
					list($fullName, $empNum) = explode("~", $programmer);
					$fullName = $database->charEncoder($fullName);
					$empNum = $database->charEncoder($empNum);
		
					$inserts .= ", ('$trackingNumber', '$empNum', '$fullName', 'Inspector')";
				}
		
				if (!empty($inserts)) {
					$sql = "INSERT INTO citydoc" . $trackingyear . ".projectmanpower 
							(TrackingNumber, EmployeeNumber, Name, `Function`) 
							VALUES " . substr($inserts, 1);
					$database->query($sql);
				}
			}
		}


		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear );
		if($container == 'searchcontainer'){
			echo $sheet->CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear );
		}else if ($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}
		else if ($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear );
		}
		// echo '12312';
	}
	
	
	
	
	if (isset($_GET['myprojectresults'])) {
		$employeeNumber = $database->charEncoder($_GET['employeeNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);

		$sql = "SELECT 
					pm.TrackingNumber, 
					vc.PR_AccountCode, 
					ft.title, 
					pc.name,
					pc.amount, 
					vc.NetAmount,
					vc.status,
					ir.Barangay
				FROM citydoc$trackingyear.projectmanpower pm
				LEFT JOIN citydoc$trackingyear.vouchercurrent vc 
					ON pm.TrackingNumber = vc.TrackingNumber
				LEFT JOIN citydoc$trackingyear.fundtitles ft 
					ON vc.PR_AccountCode = ft.Code
				LEFT JOIN citydoc$trackingyear.programcode pc
					ON pm.TrackingNumber = pc.TrackingNumberInfra
				LEFT JOIN citydoc$trackingyear.infra ir
					ON pm.TrackingNumber = ir.TrackingNumber
				WHERE pm.EmployeeNumber = '$employeeNumber'";
	
		$result = $database->query($sql);
	
		$sheet = '';
		if ($result->num_rows > 0) {
			
			$counter = 1; 

			while ($row = $result->fetch_assoc()) {
				$projectName = htmlspecialchars($row['name']); 
				$trackingNumber = htmlspecialchars($row['TrackingNumber']); 
				$projectType = htmlspecialchars($row['title']); 
				$brgy = htmlspecialchars($row['Barangay']); 
				$status = htmlspecialchars($row['status']); 
				$amount = number_format($row['amount'], 2);

				$sheet .= '<div class="myproject-card" style="margin-top:1.5rem;padding-top:20px;">
							<table  border="0">
								<tr >
									<td >
									</td>
									<td style="vertical-align:top;text-align:left;padding-top:.05rem;padding-right:.3rem;width:0px; border-bottom:1px solid var(--shadow-color); ">
										<span style="
											font-size: var(--tiny-normal-size); 
											color: var(--title-color); 
											font-weight: bold; 
											background-color: rgba(51,117,147,0.13); 
											padding: 5px 10px; 
											border-radius: 5px;" >
											'.$counter++.'
										</span>
									</td>
																	
									<td colspan="1" style="text-align:right;vertical-align:top;color: var(--title-color);padding-bottom:.8rem;border-bottom:1px solid var(--shadow-color);">
										<span style="color: orange;font-weight:bold;font-size:1.2em;"> </span> <span id="myprojecttrackid" style="font-weight:bold;font-size:1.2em;">' . $trackingNumber . '</span>
									</td>
									
								</tr>
								<tr>
								<td colspan="3" style="padding:5px;">

								</td>
								</tr>

								<tr>
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Status </span>
										</div>
									</td>
									<td colspan="0">
										<div style="color: rgba(51,117,147,1); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.20em;">
											 ' . $status . '
										</div>
									</td>
									
								</tr>

								<tr>
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Type</span>
										</div>
									</td>
									<td colspan="0">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em;">
											' . $projectType . '
										</div>
									</td>
									
								</tr>

								<tr >
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Project</span>
										</div>
									</td>
									
									<td colspan="0">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em;">
											' . $projectName . '
										</div>
									</td>
									
								</tr>
								

								<tr>
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Brgy</span>
										</div>
									</td>
									<td colspan="0">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em;">
											' . $brgy . '
										</div>
									</td>
									
								</tr>

								<tr>
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Budget</span>
										</div>
									</td>
									<td colspan="0" style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em;">
											' . $amount . '
										</div>
									</td>
								</tr>
								
							</table>
							<div style="text-align: right; margin-top: .5rem;margin-bottom:.7rem;">
								<span type="button" class="action" onclick="myProjectDetails(\'' . $trackingNumber . '\')" style="color:orange;">See Details</span>
							</div>
						</div>';
			}
			
		} else {
			$sheet .= '<p>No projects found.</p>';
		}

		echo $sheet;
	}


	if (isset($_GET['listofprojectresults'])) {
		$employeeNumber = $database->charEncoder($_GET['employeeNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);

		$sql = "SELECT 
					vc.PR_AccountCode, 
					ft.title, 
					pc.name,
					pc.amount, 
					vc.NetAmount,
					vc.status,
					ir.*
				FROM citydoc$trackingyear.infra ir
				LEFT JOIN citydoc$trackingyear.vouchercurrent vc 
					ON ir.TrackingNumber = vc.TrackingNumber
				LEFT JOIN citydoc$trackingyear.fundtitles ft 
					ON vc.PR_AccountCode = ft.Code
				LEFT JOIN citydoc$trackingyear.programcode pc
					ON ir.TrackingNumber = pc.TrackingNumberInfra
				where status != 'Cancelled';";
	
		$result = $database->query($sql);
	
		$sheet = '';
		$sheet .= '<span style="color:var(--title-color);font-weight:bold;">List of Projects</span>';
		if ($result->num_rows > 0) {
			
			$counter = 1; 

			while ($row = $result->fetch_assoc()) {
				$projectName = htmlspecialchars($row['name']); 
				$trackingNumber = htmlspecialchars($row['TrackingNumber']); 
				$projectType = htmlspecialchars($row['title']); 
				$brgy = htmlspecialchars($row['Barangay']); 
				$status = htmlspecialchars($row['status']); 
				$amount = number_format($row['amount'], 2);

				$sheet .= '<div id="filtercard" class="myproject-card" style="margin-top:1.5rem;padding-top:20px;">
							<table  border="0">
								<tr >
									<td >
									</td>
									<td style="vertical-align:top;text-align:left;padding-top:.05rem;padding-right:.3rem;width:0px; border-bottom:1px solid var(--shadow-color); ">
										<span style="
											font-size: var(--tiny-normal-size); 
											color: var(--title-color); 
											font-weight: bold; 
											background-color: rgba(51,117,147,0.13); 
											padding: 5px 10px; 
											border-radius: 5px;" class="numbercounter" >
											'.$counter++.'
										</span>
									</td>
																	
									<td colspan="1" style="text-align:right;vertical-align:top;color: var(--title-color);padding-bottom:.8rem;border-bottom:1px solid var(--shadow-color);">
										<span style="color: orange;font-weight:bold;font-size:1.2em;"> </span> <span id="myprojecttrackid" style="font-weight:bold;font-size:1.2em;">' . $trackingNumber . '</span>
									</td>
									
								</tr>
								<tr>
								<td colspan="3" style="padding:5px;">

								</td>
								</tr>

								<tr>
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Status </span>
										</div>
									</td>
									<td colspan="0">
										<div style="color: rgba(51,117,147,1); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.20em;">
											 ' . $status . '
										</div>
									</td>
									
								</tr>

								<tr>
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Type</span>
										</div>
									</td>
									<td colspan="0">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em;">
											' . $projectType . '
										</div>
									</td>
									
								</tr>

								<tr >
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Project</span>
										</div>
									</td>
									
									<td colspan="0">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em;">
											' . $projectName . '
										</div>
									</td>
									
								</tr>
								

								<tr>
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Brgy</span>
										</div>
									</td>
									<td colspan="0">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em;">
											' . $brgy . '
										</div>
									</td>
									
								</tr>

								<tr>
									<td></td>
									<td style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em; padding-right:.5rem;">
											<span style="color: rgba(51,117,147,.8);font-weight:normal" >Budget</span>
										</div>
									</td>
									<td colspan="0" style="vertical-align:top;">
										<div style="color: var(--title-color); font-weight:bold;margin-bottom: 0.3rem;font-size: 1.05em;">
											' . $amount . '
										</div>
									</td>
								</tr>
								
							</table>
							<div style="text-align: right; margin-top: .5rem;margin-bottom:.7rem;">
								<span type="button" class="action" onclick="ListofmyProjectDetails(\'' . $trackingNumber . '\')" style="color:orange;">See Details</span>
							</div>
						</div>';
			}
			
		} else {
			$sheet .= '<p>No projects found.</p>';
		}

		echo $sheet;
	}

	if (isset($_GET['myprojectdetails'])) {

		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		
		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear);

		echo $sheet->MyProjectDetails($trackingNumber, $newRecord,$trackingyear);
		
	}

	if (isset($_GET['ListofmyProjectDetails'])) {

		$trackingNumber = $database->charEncoder($_GET['trackingNumber']);
		$trackingyear = $database->charEncoder($_GET['trackingyear']);
		
		$newRecord = $database->searchTrackingNumber2022($trackingNumber,$trackingyear);

		echo $sheet->ListofmyProjectDetails($trackingNumber, $newRecord,$trackingyear);
		
	}
	
?>




















