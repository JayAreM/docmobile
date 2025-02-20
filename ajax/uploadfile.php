

<?php
	session_start();
	require_once("../includes/database.php");
	require_once("../interface/sheets.php");
	$dt = time();
	$dateEncoded = date('Y-m-d h:i A', $dt);
	$dateEncoded1 = date('Y-m-d h:i:s A', $dt);
	
	
	$officeCode = $_SESSION['cbo'];
	$encodedBy = $_SESSION['employeeNumber'] ;
	$accountType = $_SESSION['accountType'] ;
	
	
	if(isset($_FILES['filePDF'])){
		define("UPLOAD_DIR", "../uploadsBAC/");
		
		$year =  $database->charEncoder($_POST['year']);
		$month = $database->charEncoder($_POST['month']);
		$rec = $database->charEncoder($_POST['record']);
		$type = $database->charEncoder($_POST['type']);
		
		$subject = $database->charEncoder( urldecode($_POST['subject']));
		$myFile =  $_FILES['filePDF'];
		 $filename = preg_replace("/[^A-Z0-9._-]/i", "_", $myFile['name']);
		
		  // don't overwrite an existing file
		/*  $i = 0;
		  $parts = pathinfo($name);
		  while (file_exists(UPLOAD_DIR . $name)) {
		       $i++;
		       $name = $parts['filename'] . "-" . $i . "." . $parts["extension"];
		  }*/
		  $sql = "select * from bacuploads where filename = '" . $filename . "' limit 1";
		  $record = $database->query($sql);
		  $count = $database->num_rows($record);
		 if($count == 0){
			$success = move_uploaded_file($_FILES['filePDF']['tmp_name'],UPLOAD_DIR . $filename);
			 if (!$success) { 
			       echo "<p>Unable to save file.</p>";
			       exit;
			}else{
			     $sql = "Insert into bacuploads (Year,Month,Record,Type,Subject,Filename,EncodedBy, DateEncoded)VALUES (" . $year . ",'" . $month . "','" . $rec . "','" . $type . "','" . $subject . "','" . $filename . "'," . $encodedBy . ",'" . $dateEncoded . "')";
			     $database->query($sql);
			    
			}
		}else{
			echo 1;
		}
	}
	if(isset($_POST['postings'])){
		$path =  "../../attachments/";
		$title = $database->charEncoder($_POST['title']);
		$remark = $database->charEncoder($_POST['remark']);
		$myFile = $_FILES['angFile'];
		$receiver = $database->charEncoder($_POST['office']);
		$err = $database->FileChecker($myFile,"pdf,xls,xlsx,doc,docx,jpeg,jpg,xml");
		if($err == 0){
			$filenameInfo = pathinfo($myFile['name']);	
			$ext = $filenameInfo['extension'];
			$dateEncoded1 = preg_replace('/[:]/', '', $dateEncoded1);
			$filename = $officeCode . ' ' . $dateEncoded1 . '.' . $ext;
			$success = move_uploaded_file($myFile['tmp_name'], $path . $filename);
			 if (!$success) { 
			       echo "<p>Unable to save file.</p>";
			       exit;
			}else{
				$sql = "Insert into posting.information (Title,Remarks,Filename,DatePosted,PostedBy,Sender,Receiver) VALUE ('" . $title . "','" . $remark . "','" . $filename . "','" . $dateEncoded . "','" . $encodedBy . "','" . $officeCode . "','" . $receiver  . "')";
				$database->query($sql);
				
				$record = $database->SearchAttachments();
				echo $sheet->CreateAttachment($record);
			}	
		}else{
			echo "File format error.";
		}
		
	}

	//---------------------------------------------------------------------------------------------------INFRA UPLOADER - START

	if(isset($_POST['saveInfraUpload'])){
		$path =  "../../../uploads/infra/";
		$tn = $database->charEncoder($_POST['trackingNumber']);
		$year = $database->charEncoder($_POST['year']);
		$numOfFilesSent = $database->charEncoder($_POST['numOfFiles']);
		$numOfThisFile = $database->charEncoder($_POST['numOfThisFile']);
		$progress = number_format(floatval($database->charEncoder($_POST['progress'])), 2);
		$upload = $_FILES['angFile'];

		$err = $database->FileChecker($upload,"pdf,xls,xlsx,doc,docx,jpeg,jpg,png");

		if($err == 0) {
			$filenameInfo = pathinfo($upload['name']);	
			$ext = $filenameInfo['extension'];
			// $filename = $year . '~' . $tn . '~' . $progress . '~' . $newLastFile . '.' . $ext;
			$filename = $year . '~' . $tn . '~' . $progress . '~' . $numOfThisFile . '.' . $ext;
			$success = move_uploaded_file($upload['tmp_name'], $path . $filename);
			if (!$success) {
				echo "<p>Unable to save ".$upload['name'].".</p>";
				exit;
			}else{
				// ------------- create thumbnails
				$ext = strtoupper($ext);
				if($ext == "JPEG" || $ext == "JPG" || $ext == "PNG") {
					$destPath = "../../../uploads/infra/reduced/";
					// echo $database->createThumbnail($path.$filename, $destPath.$filename, 0.20, $ext);
					echo $database->createThumbnail($path.$filename, $destPath.$filename, $ext);
				}
			}
		}
	}
	if(isset($_POST['saveInfraUploadPre'])){ //peter mod 30-1-2025
		$year = $database->charEncoder($_POST['year']);
		$tn = $database->charEncoder($_POST['tn']);
		$predate = $database->charEncoder($_POST['predate']);
        $video = $database->charEncoder($_POST['video']);
		$container = $database->charEncoder($_POST['container']);
        
		if(isset($_FILES['pics'])){
			$pics = $_FILES['pics'];
			$picArray = $database->reArrayFiles($pics);
			$x = 0;
			$countFiles = sizeof($picArray);
			for($i = 0 ; $i < $countFiles ; $i++){
				$x +=  $database->FileChecker($picArray[$i],"png,jpg,jpeg");
			}
			$insertCase = '';
			if($x == 0){
				if($countFiles > 0){
					$inserCase = '';
					$filename = "../../../uploads/infra/".$year . '_' . $tn . "_pre_pic_*";
					array_map("unlink", glob($filename));
					$filename = "../../../uploads/infra/reduced/".$year . '_' . $tn . "_pre_pic_*";
					array_map("unlink", glob($filename));
					
					for($i = 0 ; $i < $countFiles ; $i++){
						$nameInfo = pathinfo($picArray[$i]['name']);	
						$filename = $year . "_" . $tn ."_pre_pic_" .$i;
						$extension = $nameInfo['extension'];
						$filenameFinal = $filename . "." . $extension;
						$path =  "../../../uploads/infra/";
						$success = move_uploaded_file($picArray[$i]['tmp_name'], $path . $filenameFinal);
						if (!$success) {
							echo "<p>Unable to save ".$picArray[$i]['name'].".</p>";
							exit;
						}else{
							$inserCase .= ",('" . $tn . "','pre_pic','Image','" . $i . "','" . $extension . "','" . $predate . "')";
							$destPath = "../../../uploads/infra/reduced/";
							$database->createThumbnail($path.$filenameFinal, $destPath.$filenameFinal, $extension);
						}
					}
					
					$sql = "delete from citydoc$year.infrauploads where trackingnumber = '" . $tn . "' and Type = 'Image'";
					$database->query($sql);
					$inserCase = substr($inserCase,1);
					$sql = "insert into citydoc$year.infrauploads (TrackingNumber,Filename,Type,Files,Extension,DateVisit)values " . $inserCase;
					$database->query($sql);
                   
				}
			}
		}
		
		if(strlen($video) > 20){
			$insertCase = '';
			$arrVid = explode(",",$video);
			$count = sizeof($arrVid);
			if($count > 1){
				$vid = '';
				for($i = 0 ; $i < $count; $i++){
					$vid = $arrVid[$i];
					$link =  $database->youtubeLink($vid);
					
					if(strlen($link) > 10){
						$insertCase .= ",('" . $tn . "','" . $link . "','Video','1')";
					}
				}
				$insertCase = substr($insertCase,1);
			}else{
				$link = $database->youtubeLink($video);
				$insertCase .= "('" . $tn . "','" . $link . "','Video','1')";
			}
			$sql = "delete from citydoc$year.infrauploads  where trackingnumber = '" . $tn . "'  and Type = 'Video'";
			$database->query($sql);
			$sql = "insert into citydoc$year.infrauploads  (TrackingNumber,Filename,Type,Files)values " . $insertCase;
			$database->query($sql);
         
		}
        $newRecord = $database->searchTrackingNumber2022($tn,$year);
		if($container == 'searchcontainer'){
       		echo $sheet->CreateTrackerInterfaceResult($tn,$newRecord,$year );
		}else if($container == 'mycardprojectresults'){
			echo $sheet->MyProjectDetails($tn,$newRecord,$year );
	 	}else if($container == 'listofprojectresults'){
			echo $sheet->ListofmyProjectDetails($tn,$newRecord,$year );
	 	}
	}

	if(isset($_POST['updateInfraUploadFull'])) { //peter mod 30-1-25
		$trackingNumber = $database->charEncoder($_POST['tn']);
		$progress = $database->charEncoder($_POST['progress']);
		$details = $database->charEncoder($_POST['details']);
		$year = $database->charEncoder($_POST['year']);
		$video = $database->charEncoder($_POST['video']);
		$date = $database->charEncoder($_POST['date']);

		$e = strpos($video,"embed");
		if($e !=  ''){
			$x = strpos($video,"https://www.youtube.com/embed/");
			if(strlen($x) == 0 ){
				$video = '';
			}
		}else{
			$x = strpos($video,"https://youtu.be");
			if(strlen($x) > 0){
				$arr = parse_url($video);
				$video = "https://www.youtube.com/embed" . $arr['path'];
			}else{
				$video = '';
			}
		}

		$fileSeries = '';
		if(isset($_FILES['uploads'])){
			$path =  "../../../uploads/infra/";
			$path1 =  "../../../uploads/infra/reduced/";
			$path2 =  "../../tempUpload/";
			$filePart1 = $year."~".$trackingNumber."~".$progress;

			$fileList = glob($path.$filePart1.'*');
			foreach($fileList as $filesource){
				if(is_file($filesource)){
					$file = realpath( dirname(__FILE__) ). '/' .$filesource;
					$file1Link = realpath( dirname(__FILE__) ). '/' . $path1.str_replace($path, '', $filesource);
					$file2Link = realpath( dirname(__FILE__) ). '/' . $path2.str_replace($path, '', $filesource);
					
					unlink($file);
					unlink($file1Link);
					unlink($file2Link);
				}   
			}
			unset($fileList);

			$uploads = $_FILES['uploads'];
			$uploadArray = $database->reArrayFiles($uploads);
			$x = 0;
			$countFiles = sizeof($uploadArray);

			for($i = 0 ; $i < $countFiles ; $i++){
				$x +=  $database->FileChecker($uploadArray[$i],"png,jpg,jpeg");
			}

			if($x == 0){
				if($countFiles > 0){
					$inserCase = '';
					for($i = 0 ; $i < $countFiles ; $i++){

						$nameInfo = pathinfo($uploadArray[$i]['name']);	
						$filename = $year."~".$trackingNumber."~".$progress."~".($i+1);
						$extension = $nameInfo['extension'];
						$filenameFinal = $filename . "." . $extension;
						$path =  "../../../uploads/infra/";

						$success = move_uploaded_file($uploadArray[$i]['tmp_name'], $path . $filenameFinal);
						if (!$success) {
							echo "<p>Unable to save ".$uploadArray[$i]['name'].".</p>";
							exit;
						}else{
							// $inserCase .= ",('" . $trackingNumber . "','update_pic','Image','" . ($i+1) . "','" . $extension . "')";
							$fileSeries .= "-".($i+1);
							$destPath = "../../../uploads/infra/reduced/";
							$database->createThumbnail($path.$filenameFinal, $destPath.$filenameFinal, $extension);
						}
					}
					$fileSeries = substr($fileSeries, 1);

					// $sql = "DELETE FROM citydoc2022.infrauploads WHERE TrackingNumber = '" . $trackingNumber . "' AND Type = 'Image'";
					// $database->query($sql);

					// $inserCase = substr($inserCase,1);
					// $sql = "INSERT INTO citydoc2022.infrauploads (TrackingNumber,Filename,Type,Files,Extension) VALUES " . $inserCase;
					// $database->query($sql);
				}
			}
		}

		$seriesStr = "";
		if($fileSeries != "") {
			$seriesStr = ", Files = '".$fileSeries."'";
		}

		$sql = "UPDATE infraconstruction SET ProgressDescription = \"".$details."\", Video = '".$video."'".$seriesStr.", DateVisit = '".$date."' WHERE Progress = '".$progress."' AND TrackingNumber = '".$trackingNumber."'";
		$database->query($sql);

		echo $sheet->createInfraUploadDisplay($trackingNumber, 1);
	}

	//---------------------------------------------------------------------------------------------------INFRA UPLOADER - END
?>