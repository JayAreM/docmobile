<?php
error_reporting(E_ALL & ~E_DEPRECATED);
class Sheets extends MySQLDatabase {

    function ModeOfProcurementTitle($mode) {
			
        $modeList = ['Competitive Bidding','Shopping','Shopping 52.1.b','Alternative','Agency to Agency','Negotiated','Negotiated Procurement 53.9(SVP)','Negotiated Procurement 53.1(TFB)',
                     'Negotiated Procurement 53.6(MS)','Negotiated Procurement 53.7','Negotiated Procurement 53.2(E.C.)','Postal Office','Direct Contracting','Repeat Order','Twice Failed Bidding(TFB)',
                     'Extension of Contract Appx. 21 Sec. 3.31','Renewal of Contract Based on Appendix 21 3.3.1.3','Agency to Agency (DBM)','Lease of Real Property Sec 5.10',];

        return $modeList[$mode-1];

    }

    function generateInfraUploadPDDButton($trackingNumber, $trackingyear,$numberRow) {
        $sql = "Select count(*) as count from citydoc$trackingyear.infrauploads where TrackingNumber = '$trackingNumber' and type = 'Image'";
        $result = $this->query($sql);
        $data = $result->fetch_array();
        $pic_Count = $data['count'];


        if( $_SESSION['CEOPDD'] == '1'){
            return "
                <tr class='uploadpicture-container' style=''>
                    <td class='trackertd'> <small>".$numberRow++."</small> </td>
                    <td class='trackerlabel' style='white-space:nowrap;color:rgba(51,117,147,1)'> Pre-Construction Pictures ($pic_Count)</td>
                    <td style='text-align:right;'>
                        <button class='btn btn-primary' onclick='openInfrapddUploader(\"" . $trackingNumber . "\", \"" . $trackingyear . "\", \"searchcontainer\")' style='vertical-align:bottom;padding:0px 5px;border-radius:2px;margin-bottom:0px;margin-right:10px;'>
                            +
                        </button>

                    </td>
                </tr>
            ";
        }
    }

    function MyProjectDetailsgenerateInfraUploadPDDButton($trackingNumber, $trackingyear,$numberRow) {
        $sql = "Select count(*) as count from citydoc$trackingyear.infrauploads where TrackingNumber = '$trackingNumber' and type = 'Image'";
        $result = $this->query($sql);
        $data = $result->fetch_array();
        $pic_Count = $data['count'];


        // if( $_SESSION['CEOPDD'] == '1'){
        //     return "
        //         <tr class='uploadpicture-container' style=''>
        //             <td class='trackertd'> <small>".$numberRow++."</small> </td>
        //             <td class='trackerlabel' style='white-space:nowrap;color:rgba(51,117,147,1);'> Pre-Construction Pictures ($pic_Count)</td>
        //             <td style='text-align:right;'>

        //             </td>
        //         </tr>
        //     ";
        // }


        
        if( $_SESSION['CEOPDD'] == '1'){
            return "
                <tr class='uploadpicture-container' style=''>
                    <td class='trackertd'> <small>".$numberRow++."</small> </td>
                    <td class='trackerlabel' style='white-space:nowrap;color:rgba(51,117,147,1)'> Pre-Construction Pictures ($pic_Count)</td>
                    <td style='text-align:right;'>
                        <button class='btn btn-primary' onclick='openInfrapddUploader(\"" . $trackingNumber . "\", \"" . $trackingyear . "\", \"mycardprojectresults\")' style='vertical-align:bottom;padding:0px 5px;border-radius:2px;margin-bottom:0px;margin-right:10px;'>
                            +
                        </button>

                    </td>
                </tr>
            ";
        }
    }


    function ListofProjectDetailsgenerateInfraUploadPDDButton($trackingNumber, $trackingyear,$numberRow) {
        $sql = "Select count(*) as count from citydoc$trackingyear.infrauploads where TrackingNumber = '$trackingNumber' and type = 'Image'";
        $result = $this->query($sql);
        $data = $result->fetch_array();
        $pic_Count = $data['count'];


        // if( $_SESSION['CEOPDD'] == '1'){
        //     return "
        //         <tr class='uploadpicture-container' style=''>
        //             <td class='trackertd'> <small>".$numberRow++."</small> </td>
        //             <td class='trackerlabel' style='white-space:nowrap;color:rgba(51,117,147,1);'> Pre-Construction Pictures ($pic_Count)</td>
        //             <td style='text-align:right;'>

        //             </td>
        //         </tr>
        //     ";
        // }


        
        if( $_SESSION['CEOPDD'] == '1'){
            return "
                <tr class='uploadpicture-container' style=''>
                    <td class='trackertd'> <small>".$numberRow++."</small> </td>
                    <td class='trackerlabel' style='white-space:nowrap;color:rgba(51,117,147,1)'> Pre-Construction Pictures ($pic_Count)</td>
                    <td style='text-align:right;'>
                        <button class='btn btn-primary' onclick='openInfrapddUploader(\"" . $trackingNumber . "\", \"" . $trackingyear . "\", \"listofprojectresults\")' style='vertical-align:bottom;padding:0px 5px;border-radius:2px;margin-bottom:0px;margin-right:10px;'>
                            +
                        </button>
                    </td>
                </tr>
            ";
        }
    }
    
    function createImagesDisplay1($arrayList){
        $imageHtml = '';
        foreach($arrayList as $f){
            $sourcePath = '../../tempUpload/' . $f;
            if (file_exists($sourcePath)) {
            list($width, $height) = getimagesize($sourcePath);
            
           
            $imageHtml .= '<img class="image-item" src="' . $sourcePath . '" onclick="openModalImage(\'' . $sourcePath . '\')">';
            }
        }
        
        $sheet = '<div class="image-container" style="margin-left:.85rem;">' . $imageHtml . '</div>';
        
        return $sheet;
    }
    

    // function balhinFile1($filename){
		
	// 	$source = '/../../../uploads/infra/reduced/';
	// 	$destination = '../../tempUpload/';

	// 	if($filename != 0){
	// 		$file = realpath( dirname(__FILE__) ). $source . $filename;
	// 		$file_handle = fopen($destination . $filename , 'a+');

	// 		fwrite($file_handle, file_get_contents($file));
	// 		fclose($file_handle);
	// 	}
	// }

    function balhinFile1($filename) {
        $source = realpath(__DIR__ . '/../../../uploads/infra/reduced/' . $filename);
        $destination = '../../tempUpload/' . $filename;
    
        // Check if the source file exists before proceeding
        if ($filename && file_exists($source)) {
            // Ensure the destination directory exists
            if (!is_dir(dirname($destination))) {
                mkdir(dirname($destination), 0777, true); // Create directory if missing
            }
    
            // Open the destination file safely
            $file_handle = fopen($destination, 'a+');
            if ($file_handle) {
                fwrite($file_handle, file_get_contents($source));
                fclose($file_handle);
            } else {
                error_log("Failed to open destination file: " . $destination);
            }
        } else {
            error_log("Source file not found: " . $source);
        }
    }
    

    // function balhinFile1() {
    //     $sourceDir = realpath(__DIR__ . '/../../../uploads/infra/reduced/') . '/';
    //     $destinationDir = realpath(__DIR__ . '/../../tempUpload/') . '/';
    
    //     // Ensure destination directory exists
    //     if (!is_dir($destinationDir)) {
    //         mkdir($destinationDir, 0777, true);
    //     }
    
    //     // Get all valid files from the source directory
    //     $validFiles = glob($sourceDir . '*_pre_pic_*.{jpeg,jpg,png}', GLOB_BRACE);
    //     $maxValidPicX = 0;
    //     $validFilenames = [];
    
    //     // Extract valid filenames and determine max pic_X
    //     foreach ($validFiles as $filePath) {
    //         $filename = basename($filePath);
    //         $validFilenames[$filename] = $filePath; // Store full path for copying
    
    //         if (preg_match('/_pre_pic_(\d+)\./', $filename, $matches)) {
    //             $maxValidPicX = max($maxValidPicX, (int)$matches[1]);
    //         }
    //     }
    
    //     // Get all files in tempUpload that match *_pre_pic_*.* and delete old/extra ones
    //     $existingFiles = glob($destinationDir . '*_pre_pic_*.{jpeg,jpg,png}', GLOB_BRACE);
    
    //     foreach ($existingFiles as $filePath) {
    //         $filename = basename($filePath);
    
    //         // Extract pic_X from filename
    //         if (preg_match('/_pre_pic_(\d+)\./', $filename, $matches)) {
    //             $picNumber = (int)$matches[1];
    
    //             // Delete file if:
    //             // 1. pic_X is greater than the highest valid pic_X
    //             // 2. The filename exists in both folders (to remove the old one before copying new)
    //             if ($picNumber > $maxValidPicX || isset($validFilenames[$filename])) {
    //                 unlink($filePath);
    //             }
    //         }
    //     }
    
    //     // Copy valid files to tempUpload (overwrite existing ones)
    //     foreach ($validFilenames as $filename => $sourceFile) {
    //         $destFile = $destinationDir . $filename;
    //         copy($sourceFile, $destFile);
    //     }
    // }
    


    function ImageContainer($trackingNumber, $trackingYear) {
        $filenamesPre = array();
		$preMedia = '';
		$preVids = '';
        $sql = "Select * from citydoc$trackingYear.infrauploads where TrackingNumber = '$trackingNumber'";
        $record = $this->query($sql);
		$count = $this->num_rows($record);
        $output = "<tr >";
        if ($count > 0) {
            while ($data = $this->fetch_array($record)) {
                $filename = $data['Filename'];
                $files = $data['Files'];
                $type  = $data['Type'];
                $ext  = $data['Extension'];
                
                if ($type == "Image") {
                    $sourcePath = '../../tempUpload/';
                    
                    // Construct the new filename
                    $filename = $trackingYear . '_' . $trackingNumber . '_pre_pic_' . $files . '.' . $ext;
                    $filePath = $sourcePath . $filename;

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
        
                    $this->balhinFile1($filename);
                    array_push($filenamesPre, $filename);
                } else {
                    $preVids .= '<iframe class="displayImage" src="' . $filename . '" style="margin:0;margin-bottom:15px;margin-left:11px;"></iframe>';
                }
            }    
        
            $output .= "<td colspan='3'>" . $this->createImagesDisplay1($filenamesPre) . "</td>";
        }
        
        
        $output .= "</tr>";
    
        // Modal structure (hidden by default)
        $output .= "
        <div id='imageModal' class='modal-image' onclick='closeModalImage()'>
            <span class='close-image'>&times;</span>
            <img class='modal-content-image' id='fullImage'>
        </div>";
    
        return $output;
    }


    

    function generateReceiveButton($newRecord,$trackingyear) {
        $trackingNumber = $newRecord['TrackingNumber'];
        $sessionOffice = $_SESSION['cbo'];
        if(($_SESSION['accountType'] == 2  or $_SESSION['accountType'] == 7 ) and $sessionOffice == '1031' ) { 
            if($newRecord['TrackingType'] == "PR"){
                if($newRecord['Status']  == "CTO Received" || $newRecord['Status'] == "Pending Released - Admin"){
                
                    return "
                        <div style='text-align: center; margin-top: 20px;'  >
                            <button class='btn btn-primary' onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Receive
                            </button>
                        </div>
                    ";
                    
                }
            }

            if($newRecord['TrackingType'] == "PO"){
                if($newRecord['Status']  == "GSO Received" || $newRecord['Status'] == "Pending Released - Admin"){
                    return "
                        <div style='text-align: center; margin-top: 20px;'  >
                            <button class='btn btn-primary' onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Receive
                            </button>
                        </div>
                    ";
                }
            }

            if($newRecord['TrackingType'] == "PX"){
                if($newRecord['Status']  == "Check Preparation - CTO"){
                    if($_SESSION['privy'] == 5 || $_SESSION['privy'] == 8){
                        return "
                            <div style='text-align: center; margin-top: 20px;' >
                                <button class='btn btn-primary'  onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                    Receive
                                </button>
                            </div>
                        ";
                    }
                }
            }

            if($newRecord['TrackingType'] == "PY"){
                if($newRecord['Status']  == "Encoded"){
                    if($_SESSION['privy'] == 5){
                        return "
                            <div style='text-align: center; margin-top: 20px;'  >
                                <button class='btn btn-primary'  onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                    Receive
                                </button>
                            </div>
                        ";
                    }
                }
            }


            if($newRecord['TrackingType'] == "NF"){
                if($newRecord['Status']  == "Preparation of Plans, PoW, and Detailed Estimates"){
                   
                    return "
                        <div style='text-align: center; margin-top: 20px;'  >
                            <button class='btn btn-primary'  onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Receive
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Plans, PoW, and Detailed Estimates for Approval"){
                   
                    return "
                        <div style='text-align: center; margin-top: 20px;'  >
                            <button class='btn btn-primary'  onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Sign
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Notice of Award for Transmit"){
                   
                    return "
                        <div style='text-align: center; margin-top: 20px;'  >
                            <button class='btn btn-primary'  onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                               Receive
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Notice of Award for Admin Signature"){
                   
                    return "
                        <div style='text-align: center; margin-top: 20px;'  >
                            <button class='btn btn-primary'  onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                               Sign
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Contract and NTP Transmit to Admin"){
                   
                    return "
                        <div style='text-align: center; margin-top: 20px;'  >
                            <button class='btn btn-primary'  onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                               Receive
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Contract and NTP for Admin Signature"){
                   
                    return "
                        <div style='text-align: center; margin-top: 20px;'  >
                            <button class='btn btn-primary'  onclick=\"receiveTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                               Sign
                            </button>
                        </div>
                    ";
                    
                }
                

            }
        }
    }


    function generateRevertButton($newRecord,$trackingyear) {
        $trackingNumber = $newRecord['TrackingNumber'];
        $sessionOffice = $_SESSION['cbo'];
        if(($_SESSION['accountType'] == 2 and $sessionOffice == '1031') or $_SESSION['accountType'] == 7) { 
            if($newRecord['Status']  == "Admin Received" ){
            
                return "
                    <div style='text-align: center; margin-top: 20px;' >
                        <button class='btn btn-primary' onclick=\"revertTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                            Revert
                        </button>
                    </div>
                ";
                
            }
            
            if($newRecord['TrackingType'] == 'NF' ) {
                if($newRecord['Status']  == "Plans, PoW, and Detailed Estimates for Approval" ){
                
                    return "
                        <div style='text-align: center; margin-top: 20px;' >
                            <button class='btn btn-primary' onclick=\"revertTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Revert
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Plans, PoW, and Detailed Estimates Signed" ){
                
                    return "
                        <div style='text-align: center; margin-top: 20px;' >
                            <button class='btn btn-primary' onclick=\"revertTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Revert
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Notice of Award for Admin Signature" ){
                
                    return "
                        <div style='text-align: center; margin-top: 20px;' >
                            <button class='btn btn-primary' onclick=\"revertTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Revert
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Notice of Award Admin Signed" ){
                
                    return "
                        <div style='text-align: center; margin-top: 20px;' >
                            <button class='btn btn-primary' onclick=\"revertTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Revert
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Contract and NTP for Admin Signature" ){
                
                    return "
                        <div style='text-align: center; margin-top: 20px;' >
                            <button class='btn btn-primary' onclick=\"revertTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Revert
                            </button>
                        </div>
                    ";
                    
                }else if($newRecord['Status']  == "Document for Pick-up - Admin" ){
                
                    return "
                        <div style='text-align: center; margin-top: 20px;' >
                            <button class='btn btn-primary' onclick=\"revertTrackingButton('$trackingNumber', '$sessionOffice', '$trackingyear')\">
                                Revert
                            </button>
                        </div>
                    ";
                    
                }

            }
        }
    }
    

    function CreateTrackerInterfaceResult($trackingNumber,$newRecord,$trackingyear ){
        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );


        $numberRow = 1;
        $NewnumberRow = $numberRow + 11;
        $trackingtype = $newRecord['TrackingType'] ;

        if($newRecord['TrackingType'] == 'PR'){
            // echo "
            // <div>
                
            //     <!-- Header Section -->
            //     <div class='trackerheader'>
            //         <span style= ''>
            //             " . $newRecord['TrackingType'] . " - " . $newRecord['Status'] . "
            //         </span>
            //         <p style=''>" . $newRecord['OfficeName'] . "</p>
            //     </div>
        
            //     <!-- Details Section -->
            //     <div style='padding: 15px;'>
            //         <table style='width: auto; border-collapse: collapse; ' >
            //             <tbody >
            //                 <tr >
            //                     <td class='trackertd' >
            //                         <small style='padding-right: 2px;'>".$numberRow++."</small> 
            //                     </td>
            //                     <td class='trackerlabel' >
            //                         TN
            //                     </td>

            //                     <td class='trackingspecs' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\">
            //                         " . $newRecord['TrackingNumber'] . "
            //                     </td>
            //                 </tr>
            //                 <tr >
            //                     <td class='trackertd' >
            //                         <small style='padding-right: 2px;'>".$numberRow++."</small> 
            //                     </td>
            //                     <td class='trackerlabel' >
            //                         Year
            //                     </td>

            //                     <td class='trackingspecs' >
            //                         " . $newRecord['Year'] . "
            //                     </td>
            //                 </tr>
            //                 <tr>
            //                     <td class='trackertd' >
            //                         <small style='padding-right: 2px;'>".$numberRow++."</small> 
            //                     </td>
            //                     <td class='trackerlabel' >
            //                         ADV
            //                     </td>
            //                     <td class='trackingspecs'> <span style=''>".$newRecord['ADV']."</td>
            //                 </tr>
            //                 <tr>
            //                     <td class='trackertd' >
            //                         <small style='padding-right: 2px;'>".$numberRow++."</small> 
            //                     </td>
            //                     <td class='trackerlabel' >
            //                         OBR 
            //                     </td>
            //                     <td class='trackingspecs'>".$newRecord['OBR_Number']."</td>
            //                 </tr>
            //                 <tr>
            //                     <td class='trackertd' >
            //                         <small style='padding-right: 2px;'>".$numberRow++."</small> 
            //                     </td>
            //                     <td class='trackerlabel' >
            //                         PR Sched
            //                     </td>
            //                     <td class='trackingspecs'> ".$this->numberToQuarter($newRecord['PR_Month'])." </td>
            //                 </tr>
            //                 <tr>
            //                     <td class='trackertd' >
            //                         <small style='padding-right: 2px;'>".$numberRow++."</small> 
            //                     </td>
            //                     <td class='trackerlabel' >
            //                         Fund
            //                     </td>
            //                     <td class='trackingspecs'>".$newRecord['Fund']." </td>
            //                 </tr>
            //             </tbody>
            //         </table>
            //     </div>
            //    ".$this->generateReceiveButton($newRecord,$trackingyear)."
            //     ".$this->generateRevertButton($newRecord,$trackingyear)."
            // </div>";
            echo "
            <div>
                <div class='trackerheader'>
                    <span class='tracker-title'>" . $newRecord['TrackingType'] . " - " . $newRecord['Status'] . "</span>
                    <p class='tracker-office'>" . $newRecord['OfficeName'] . "</p>
                </div>
                <div class='tracker-details'>
                    <table>
                        <tbody>
                            ";
                            $fields = [
                                'TN' => 'TrackingNumber',
                                'Year' => 'Year',
                                'Claimant' => 'Claimant',
                                'OBR' => 'OBR_Number',
                                'Document' => 'DocumentType',
                                'Claim Type' => 'ClaimType',
                                'Period' => 'PeriodMonth',
                                'Fund' => 'Fund',
                                'Check Number' => 'CheckNumber',
                                'Check Date' => 'CheckDate',
                                'Net Amount' => 'NetAmount'
                            ];
                            foreach ($fields as $label => $field) {
                                echo "
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>" . $label . "</td>
                                    <td class='trackingspecs' " . ($field === 'NetAmount' ? "style='color: red; font-weight: bold;text-align:center;'" : "") . ">
                                        " . htmlspecialchars($newRecord[$field], ENT_QUOTES) . "
                                    </td>
                                </tr>
                                ";
                            }
                            echo "
                        </tbody>
                    </table>
                </div>
                " . $this->generateReceiveButton($newRecord, $trackingyear) . "
                " . $this->generateRevertButton($newRecord, $trackingyear) . "
            </div>";
        }


        if($newRecord['TrackingType'] == 'PO'){
            echo "
            <div class='card' >
                
                <!-- Header Section -->
                <div class='trackerheader'>
                    <span style= ''>
                        " . $newRecord['TrackingType'] . " - " . $newRecord['Status'] . "
                    </span>
                    <p style=''>" . $newRecord['OfficeName'] . "</p>
                </div>
        
                <!-- Details Section -->
                <div style='padding: 15px;'>
                    <table style='width: auto; border-collapse: collapse; ' >
                        <tbody >
                            <tr >
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    TN
                                </td>

                                <td class='trackingspecs' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\">
                                    " . $newRecord['TrackingNumber'] . "
                                </td>
                            </tr>
                            <tr >
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    Year
                                </td>

                                <td class='trackingspecs' >
                                    " . $newRecord['Year'] . "
                                </td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    Supplier
                                </td>
                                <td class='trackingspecs'> <span style=''>".$newRecord['Claimant']."</td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    OBR 
                                </td>
                                <td class='trackingspecs'>".$newRecord['OBR_Number']."</td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    PO Number
                                </td>
                                <td class='trackingspecs'>".$newRecord['PO_Number']."</td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    PR Number
                                </td>
                                <td class='trackingspecs'>".$newRecord['PR_TrackingNumber']."</td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    PR Sched
                                </td>
                                <td class='trackingspecs'> ".$this->numberToQuarter($newRecord['PR_Month'])." </td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    Fund
                                </td>
                                <td class='trackingspecs'>".$newRecord['Fund']." </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
               ".$this->generateReceiveButton($newRecord,$trackingyear)."
                ".$this->generateRevertButton($newRecord,$trackingyear)."
            </div>";
        }


        if($newRecord['TrackingType'] == 'PX'){
            echo "
            <div class='card' >
                
                <!-- Header Section -->
                <div class='trackerheader'>
                    <span style= 'text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);  font-size: 24px; letter-spacing: 1px; font-weight: bold; color: white;'>
                        " . $newRecord['TrackingType'] . " - " . $newRecord['Status'] . "
                    </span>
                    <p style='margin: 5px 0 0; font-size: 1em;color: white;'>" . $newRecord['OfficeName'] . "</p>
                </div>
        
                <!-- Details Section -->
                <div style='padding: 20px;'>
                    <table style='width: auto; border-collapse: collapse; ' >
                        <tbody >
                            <tr >
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    TN
                                </td>
                                <td class='trackingspecs' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\">
                                    " . $newRecord['TrackingNumber'] . "
                                </td>
                            </tr>
                            <tr >
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    Year
                                </td>

                                <td class='trackingspecs' >
                                    " . $newRecord['Year'] . "
                                </td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                   Supplier
                                </td>
                                <td class='trackingspecs'> <span style=''>".$newRecord['Claimant']."</td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    OBR
                                </td>
                                <td class='trackingspecs'>".$newRecord['OBR_Number']."</td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                    PO Number
                                </td>
                                <td class='trackingspecs'>".$newRecord['PO_Number']."</td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                   PR Number
                                </td>
                                <td class='trackingspecs'>".$newRecord['PR_TrackingNumber']."</td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small>
                                </td>
                                <td class='trackerlabel' >
                                   PR Sched
                                </td>
                                <td class='trackingspecs'> ".$this->numberToQuarter($newRecord['PR_Month'])." </td>
                            </tr>
                            <tr>
                                <td class='trackertd' >
                                    <small style='padding-right: 2px;'>".$numberRow++."</small> 
                                </td>
                                <td class='trackerlabel' >
                                   Fund
                                </td>
                                <td class='trackingspecs'>".$newRecord['Fund']." </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
               ".$this->generateReceiveButton($newRecord,$trackingyear)."
                ".$this->generateRevertButton($newRecord,$trackingyear)."
            </div>";
        }


        if($newRecord['TrackingType'] == 'PY'){
            echo "
            <div>
                <div class='trackerheader'>
                    <span class='tracker-title'>" . $newRecord['TrackingType'] . " - " . $newRecord['Status'] . "</span>
                    <p class='tracker-office'>" . $newRecord['OfficeName'] . "</p>
                </div>
                <div class='tracker-details'>
                    <table>
                        <tbody>
                            ";
                            $fields = [
                                'TN' => 'TrackingNumber',
                                'Year' => 'Year',
                                'Claimant' => 'Claimant',
                                'OBR' => 'OBR_Number',
                                'Document' => 'DocumentType',
                                'Claim Type' => 'ClaimType',
                                'Period' => 'PeriodMonth',
                                'Fund' => 'Fund',
                                'Check Number' => 'CheckNumber',
                                'Check Date' => 'CheckDate',
                                'Net Amount' => 'NetAmount'
                            ];
                            foreach ($fields as $label => $field) {
                                echo "
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>" . $label . "</td>
                                    <td class='trackingspecs' " . ($field === 'NetAmount' ? "style='color: red; font-weight: bold;'" : "") . ">
                                        " . htmlspecialchars($newRecord[$field], ENT_QUOTES) . "
                                    </td>
                                </tr>
                                ";
                            }
                            echo "
                        </tbody>
                    </table>
                </div>
                " . $this->generateReceiveButton($newRecord, $trackingyear) . "
                " . $this->generateRevertButton($newRecord, $trackingyear) . "
            </div>";
        }


        if($newRecord['TrackingType'] == 'NF'){
            $sql="select * from citydoc$trackingyear.infrauploads where trackingnumber = '$trackingNumber' ";
            $result = $this->query($sql);
            $videolink = '';
            $datevisited = '';
            while($data = $result->fetch_array()){
                $type = $data['Type'];
             
                if($type == 'Video'){
                    $filename = $data['Filename'];
                    function convertToEmbedUrl($url) {
                        // Extract video ID from YouTube short link or standard URL
                        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches) || 
                            preg_match('/v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                            return 'https://www.youtube.com/embed/' . $matches[1];
                        }
                        return $url; // Return original if no match
                    }
                    
                    $filename = convertToEmbedUrl($filename);
                    
                    $videolink = '<iframe width="100%" src="' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '" 
                                             title="YouTube video player" frameborder="0" 
                                             allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                             allowfullscreen>
                                  </iframe>';
                }else if($type == 'Image'){
                    $datevisited = $data['DateVisit'];
                }

                unset($data);
            }
            
            // $sql="select * from citydoc$trackingyear.vouchercurrent where trackingnumber = '$trackingNumber'";
            // $result = $this->query($sql);
            // $data = $result->fetch_array();
            // $expensecode = !empty($data['PR_AccountCode']) ? $data['PR_AccountCode'] : "";
            // unset($data);

            $sql="select * from citydoc$trackingyear.programcode where trackingnumberinfra = '$trackingNumber' and category = 'Infrastructure Project'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $projectname = !empty($data['Name']) ? $data['Name'] : "";
            unset($data);
            $progress = 0;
            $sql="select * from citydoc$trackingyear.infra where trackingnumber = '$trackingNumber'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $location = $data['Location'];
            $brgy = $data['Barangay'];
            $progress =  $data['Progress'];
            $coordinates = $data['Coordinates'];
            $map = $data['Map'];
            $duration = !empty($data['Duration']) ? $data['Duration'] : "";
            unset($data);

            $sql = "SELECT `Function`, `Name`, EmployeeNumber FROM citydoc$trackingyear.projectmanpower WHERE trackingnumber = '$trackingNumber'";
            $result = $this->query($sql);

            if($_SESSION['CEOPDD'] == '1'){
                $cardHtml = '
                <div class="project-team-card">
                    <h2 class="project-team-title">Project Team</h2>
                    <div class="project-team-section">';

                // Define hardcoded roles
                $hardcodedRoles = [
                    "PoW Lead Engineer" => [],
                    "PoW Civil Engineer" => [],
                    "Project Surveyor" => [],
                    "Project Architect" => [],
                    "Project Electrical Engineer" => [],
                    "Project Plumber" => [],
                    "Project Structural Engineer" => [],
                    "Project Structural Engineer" => [],
                    "Construction Inspector" => []
                ];
                $employeeNum = '';
                while ($row = $result->fetch_array()) {
                    $role = $row['Function'];
                    $displayrole = '';

                    if ($role == 'Pos2') {
                        $displayrole = 'PoW Lead Engineer';
                    }elseif ($role == 'Pos1') {
                        $displayrole = 'PoW Civil Engineer';
                    }elseif ($role == 'Surveyor') {
                        $displayrole = 'Project Surveyor';
                    }elseif ($role == 'Pos3') {
                        $displayrole = 'Project Architect';
                    }elseif ($role == 'Pos4') {
                        $displayrole = 'Project Electrical Engineer';
                    }elseif ($role == 'Pos5') {
                        $displayrole = 'Project Plumber';
                    }elseif ($role == 'Pos6') {
                        $displayrole = 'Project Structural Engineer';
                    }elseif ($role == 'Inspector') {
                        $displayrole = 'Construction Inspector';
                    }

                    $member = $row['Name'];
                    $employeeNum = $row['EmployeeNumber'];

                    if (isset($hardcodedRoles[$displayrole])) {
                        $hardcodedRoles[$displayrole][] = $member;
                    }
                }

                foreach ($hardcodedRoles as $role => $members) {
                    $cardHtml .= '<div class="project-role" style="display: flex; justify-content: space-between; align-items: center;">
                                            <span>' . htmlspecialchars($role) . '</span>
                                            <button class="ri-edit-box-line" style="margin-left:.3rem;font-weight:bold;color:var(--text-color);"  
                                                onclick="EditRole(\'' . htmlspecialchars($trackingNumber) . '\', \'' . htmlspecialchars($trackingyear) . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($employeeNum) . '\',searchcontainer)">
                                            </button>
                                        </div>';
            
                                
                                        if (!empty($members)) {
                                                $cardHtml .= '<div class="project-members">' . implode('<br>', array_map('htmlspecialchars', $members)) . '</div>';
                                            } else {
                                                $cardHtml .= '<div class="project-members">-</div>'; 
                                            }
                                        }

                                    $cardHtml .= '
                                        </div>
                                    </div>';

                $status = $newRecord['Status'];

                $sheet = '';

                $sheet .="
                    <div>
                        <div class='trackerheader'>
                            <table>
                                <tr>
                                    <td>
                                        <span class='tracker-title' style='color:rgba(51,117,147,1);'>" . $status . " </span>
                                    </td>
                                    <td>
                                        <button class='ri-edit-box-line' 
                                            style='margin-left:1rem;color:var(--text-color);font-weight:bold;font-size:var(--normal-font-size)' 
                                            onclick='openEditStatus(\"" . $trackingNumber . "\", \"" . $trackingyear . "\", \"" . $status . "\", searchcontainer)'>
                                        </button>
                                    </td>
                                <tr>
                                <tr>
                                    <td>
                                     <p class='tracker-office'>" . htmlspecialchars($newRecord['OfficeName'], ENT_QUOTES) . "</p>
                                    </td>
                                </tr>
                            </table>
                           
                        </div>
                    </div>

                    <div class='tracker-details'>
                        <table border='0'>
                            <tbody>
                                ";
                                // $fields = [
                                //     'TN' => 'TrackingNumber',
                                //     'Fund Year' => 'Year',
                                //     'Contructor' => 'Claimant',
                                //     'Document' => 'DocumentType',
                                //     'Fund' => 'Fund',
                                //     'Net Amount' => 'NetAmount'
                                // ];
                                // foreach ($fields as $label => $field) {
                                //     echo "
                                //     <tr>
                                //         <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                //         <td class='trackerlabel'>" . $label . "</td>
                                //         <td class='trackingspecs' " . 
                                //             ($field === 'NetAmount' ? "style='color: red; font-weight: bold; text-align:left;'" : "") . 
                                //             ($field === 'TrackingNumber' ? " id='tracknumid'" : "") . 
                                //             ($field === 'Year' ? " id='yearid'" : "") . ">
                                //             " . htmlspecialchars($newRecord[$field], ENT_QUOTES) . "
                                //         </td>
                                //     </tr>
                                //     ";
                                // }
                                $iframemap = "";
                                if (!empty($map)) {
                                
                                    $iframemap = "<iframe src='$map' width='100%' height='300' style='border:0;' allowfullscreen='' loading='lazy' referrerpolicy='no-referrer-when-downgrade'></iframe>";
                                } else {
                                    $iframemap = ""; 
                                }

                                $sheet .="
                                    
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Project Name</td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                        $projectname
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Location <button class='ri-edit-box-line' style='margin-left:1.45rem;font-weight:bold;color:var(--text-color);'   onclick='openEditLocation(\"$trackingNumber\", \"$trackingyear\", \"searchcontainer\")'></button></td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em; padding-bottom:1em'>
                                        $location
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Barangay <button class='ri-edit-box-line' style='margin-left:1rem;font-weight:bold;color:var(--text-color);' onclick='openEditBrgy(\"$trackingNumber\", \"$trackingyear\", \"searchcontainer\")'></button></td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em; padding-bottom:1em'>
                                        $brgy
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1)'>TN</td>
                                        <td class='trackingspecs' > <span id='tracknumid'>$trackingNumber</span></td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1)'>Year</td>
                                        <td class='trackingspecs' > <span id='yearid'>".$newRecord['Year']."</span></td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='white-space:nowrap;color:rgba(51,117,147,1)'>Project Duration</td>
                                        <td class='trackingspecs' >
                                            ".$duration."
                                        </td>
                                    </tr>
                                    <tr style=''>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1)'>Date Visited</td>
                                        <td class='trackingspecs' >
                                            <div>
                                            ".$datevisited." <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);' onclick='openEditDateVisited(\"$trackingNumber\", \"$trackingyear\", \"searchcontainer\")'></button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Coordinates  <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);' onclick='openEditCoordinates(\"$trackingNumber\", \"$trackingyear\", \"searchcontainer\")'></button> </td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                        $coordinates 
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Map  <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);' onclick='openEditMap(\"$trackingNumber\", \"$trackingyear\", \"searchcontainer\")'></button> </td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                             $iframemap
                                        </td>
                                    </tr>
                                    " . $this->generateInfraUploadPDDButton($trackingNumber,$trackingyear,$numberRow++ ) . "
                                    " . $this->ImageContainer($trackingNumber,$trackingyear) . "
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td colspan='2' class='trackerlabel' style='vertical-align:top ; padding:5px;'>
                                            <div style='color:rgba(51,117,147,1)'>Video Link <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);font-weight:bold;' onclick='openEditVideoLink(\"$trackingNumber\", \"$trackingyear\", \"searchcontainer\")'></button></div>
                                            <div style='margin-top:5px;'> $videolink </div>
                                        </td>

                                        

                                    </tr>";
                                    
                                $sheet .= "
                            </tbody>
                        </table>

                        $cardHtml
                        
                    </div>
                
                </div>";

                echo $sheet;
            }

            
        }

    }

    function MyProjectDetails($trackingNumber,$newRecord,$trackingyear){
        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );
        $numberRow = 1;
        
        if($newRecord['TrackingType'] == 'NF'){
            $sql="select * from citydoc$trackingyear.infrauploads where trackingnumber = '$trackingNumber' ";
            $result = $this->query($sql);
            $videolink = '';
            $datevisited = '';
            while($data = $result->fetch_array()){
                $type = $data['Type'];
             
                if($type == 'Video'){
                    $filename = $data['Filename'];
                    function convertToEmbedUrl($url) {
                        // Extract video ID from YouTube short link or standard URL
                        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches) || 
                            preg_match('/v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                            return 'https://www.youtube.com/embed/' . $matches[1];
                        }
                        return $url; // Return original if no match
                    }
                    
                    $filename = convertToEmbedUrl($filename);
                    
                    $videolink = '<iframe width="100%" src="' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '" 
                                             title="YouTube video player" frameborder="0" 
                                             allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                             allowfullscreen>
                                  </iframe>';
                }else if($type == 'Image'){
                    $datevisited = $data['DateVisit'];
                }

                unset($data);
            }
            
            // $sql="select * from citydoc$trackingyear.vouchercurrent where trackingnumber = '$trackingNumber'";
            // $result = $this->query($sql);
            // $data = $result->fetch_array();
            // $expensecode = !empty($data['PR_AccountCode']) ? $data['PR_AccountCode'] : "";
            // unset($data);

            $sql="select * from citydoc$trackingyear.programcode where trackingnumberinfra = '$trackingNumber' and category = 'Infrastructure Project'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $projectname = !empty($data['Name']) ? $data['Name'] : "";
            unset($data);
            $progress = 0;
            $sql="select * from citydoc$trackingyear.infra where trackingnumber = '$trackingNumber'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $location = $data['Location'];
            $brgy = $data['Barangay'];
            $progress =  $data['Progress'];
            $coordinates =  $data['Coordinates'];
            $map =  $data['Map'];
            $duration = !empty($data['Duration']) ? $data['Duration'] : "";
            unset($data);

            $sql = "SELECT `Function`, `Name`, EmployeeNumber FROM citydoc$trackingyear.projectmanpower WHERE trackingnumber = '$trackingNumber'";
            $result = $this->query($sql);

            if($_SESSION['CEOPDD'] == '1'){
                $cardHtml = '
                <div class="project-team-card">
                    <h2 class="project-team-title" style="color:rgba(51,117,147,1);">Project Team</h2>
                    <div class="project-team-section">';

                // Define hardcoded roles
                $hardcodedRoles = [
                    "PoW Lead Engineer" => [],
                    "PoW Civil Engineer" => [],
                    "Project Surveyor" => [],
                    "Project Architect" => [],
                    "Project Electrical Engineer" => [],
                    "Project Plumber" => [],
                    "Project Structural Engineer" => [],
                    "Project Structural Engineer" => [],
                    "Construction Inspector" => []
                ];
                $employeeNum = '';
                while ($row = $result->fetch_array()) {
                    $role = $row['Function'];
                    $displayrole = '';

                    if ($role == 'Pos2') {
                        $displayrole = 'PoW Lead Engineer';
                    }elseif ($role == 'Pos1') {
                        $displayrole = 'PoW Civil Engineer';
                    }elseif ($role == 'Surveyor') {
                        $displayrole = 'Project Surveyor';
                    }elseif ($role == 'Pos3') {
                        $displayrole = 'Project Architect';
                    }elseif ($role == 'Pos4') {
                        $displayrole = 'Project Electrical Engineer';
                    }elseif ($role == 'Pos5') {
                        $displayrole = 'Project Plumber';
                    }elseif ($role == 'Pos6') {
                        $displayrole = 'Project Structural Engineer';
                    }elseif ($role == 'Inspector') {
                        $displayrole = 'Construction Inspector';
                    }

                    $member = $row['Name'];
                    $employeeNum = $row['EmployeeNumber'];

                    if (isset($hardcodedRoles[$displayrole])) {
                        $hardcodedRoles[$displayrole][] = $member;
                    }
                }

                foreach ($hardcodedRoles as $role => $members) {
                    $cardHtml .= '<div class="project-role" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span>' . htmlspecialchars($role) . '</span>
                                    
                                </div>';
    
                    
                    if (!empty($members)) {
                        $cardHtml .= '<div class="project-members">' . implode('<br>', array_map('htmlspecialchars', $members)) . '</div>';
                    } else {
                        $cardHtml .= '<div class="project-members">-</div>'; 
                    }
                }

                $cardHtml .= '
                    </div>
                </div>';

                $status = $newRecord['Status'];

                $iframemap = "";
                if (!empty($map)) {
                
                    $iframemap = "<iframe src='$map' width='100%' height='300' style='border:0;' allowfullscreen='' loading='lazy' referrerpolicy='no-referrer-when-downgrade'></iframe>";
                } else {
                    $iframemap = ""; 
                }

                $sheet = '';
                $sheet .="
                    <div>
                        <div class='trackerheader' >
                            <table border='0'>
                                <tr>
                                    <td>
                                        <span class='tracker-title' style='color:rgba(51,117,147,1);'>" . $status . "  
                                           
                                        </span>
                                    </td>   
                                    <td style='text-align:right;width:0px;'>
                                        <button class='ri-edit-box-line' 
                                                    style='margin-left:1rem;color:var(--text-color);font-weight:bold;font-size:var(--normal-font-size)' 
                                                onclick='openEditStatus(\"" . $trackingNumber . "\", \"" . $trackingyear . "\", \"" . $status . "\", mycardprojectresults)'>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'>
                                        <p class='tracker-office'>" . htmlspecialchars($newRecord['OfficeName'], ENT_QUOTES) . "</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class='tracker-details'>
                        <table border='0'>
                            <tbody>
                                ";
                                // $fields = [
                                //     'TN' => 'TrackingNumber',
                                //     'Fund Year' => 'Year',
                                //     'Contructor' => 'Claimant',
                                //     'Document' => 'DocumentType',
                                //     'Fund' => 'Fund',
                                //     'Net Amount' => 'NetAmount'
                                // ];
                                // foreach ($fields as $label => $field) {
                                //     echo "
                                //     <tr>
                                //         <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                //         <td class='trackerlabel'>" . $label . "</td>
                                //         <td class='trackingspecs' " . 
                                //             ($field === 'NetAmount' ? "style='color: red; font-weight: bold; text-align:left;'" : "") . 
                                //             ($field === 'TrackingNumber' ? " id='tracknumid'" : "") . 
                                //             ($field === 'Year' ? " id='yearid'" : "") . ">
                                //             " . htmlspecialchars($newRecord[$field], ENT_QUOTES) . "
                                //         </td>
                                //     </tr>
                                //     ";
                                // }

                                $sheet .="
                                    
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Project Name</td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                        $projectname
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Location <button class='ri-edit-box-line' style='margin-left:1.45rem;font-weight:bold;color:var(--text-color);'   onclick='openEditLocation(\"$trackingNumber\", \"$trackingyear\", \"mycardprojectresults\")'></button></td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em; padding-bottom:1em'>
                                        $location
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Barangay <button class='ri-edit-box-line' style='margin-left:1rem;font-weight:bold;color:var(--text-color);' onclick='openEditBrgy(\"$trackingNumber\", \"$trackingyear\", \"mycardprojectresults\")'></button></td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em; padding-bottom:1em'>
                                        $brgy
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1)'>TN</td>
                                        <td class='trackingspecs' > <span id='tracknumid'>$trackingNumber</span></td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1)'>Year</td>
                                        <td class='trackingspecs' > <span id='yearid'>".$newRecord['Year']."</span></td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='white-space:nowrap;color:rgba(51,117,147,1)'>Project Duration</td>
                                        <td class='trackingspecs' >
                                            ".$duration."
                                        </td>
                                    </tr>
                                    <tr style=''>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1)'>Date Visited</td>
                                        <td class='trackingspecs' >
                                            <div>
                                            ".$datevisited." <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);' onclick='openEditDateVisited(\"$trackingNumber\", \"$trackingyear\", \"mycardprojectresults\")'></button>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Coordinates  <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);' onclick='openEditCoordinates(\"$trackingNumber\", \"$trackingyear\", \"mycardprojectresults\")'></button> </td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                        $coordinates 
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Map  <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);' onclick='openEditMap(\"$trackingNumber\", \"$trackingyear\", \"mycardprojectresults\")'></button> </td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                             $iframemap
                                        </td>
                                    </tr>
                                    
                                    " . $this->MyProjectDetailsgenerateInfraUploadPDDButton($trackingNumber,$trackingyear,$numberRow++ ) . "
                                    " . $this->ImageContainer($trackingNumber,$trackingyear) . "
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td colspan='2' class='trackerlabel' style='vertical-align:top ; padding:5px;'>
                                        <div style='color:rgba(51,117,147,1)'>Video Link <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);font-weight:bold;' onclick='openEditVideoLink(\"$trackingNumber\", \"$trackingyear\", \"mycardprojectresults\")'></button></div>
                                        <div style='margin-top:5px;'> $videolink </div>
                                    </td>

                                        

                                    </tr>";
                                    
                                $sheet .= "
                            </tbody>
                        </table>

                        $cardHtml
                        
                    </div>
                
                </div>";

                echo $sheet;
            }
        

            
        }
        
    }



    function ListofmyProjectDetails($trackingNumber,$newRecord,$trackingyear){
        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );
        $numberRow = 1;
        
        if($newRecord['TrackingType'] == 'NF'){
            $sql="select * from citydoc$trackingyear.infrauploads where trackingnumber = '$trackingNumber' ";
            $result = $this->query($sql);
            $videolink = '';
            $datevisited = '';
            while($data = $result->fetch_array()){
                $type = $data['Type'];
             
                if($type == 'Video'){
                    $filename = $data['Filename'];
                    function convertToEmbedUrl($url) {
                        // Extract video ID from YouTube short link or standard URL
                        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches) || 
                            preg_match('/v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                            return 'https://www.youtube.com/embed/' . $matches[1];
                        }
                        return $url; // Return original if no match
                    }
                    
                    $filename = convertToEmbedUrl($filename);
                    
                    $videolink = '<iframe width="100%" src="' . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . '" 
                                             title="YouTube video player" frameborder="0" 
                                             allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                             allowfullscreen>
                                  </iframe>';
                }else if($type == 'Image'){
                    $datevisited = $data['DateVisit'];
                }

                unset($data);
            }
            
            // $sql="select * from citydoc$trackingyear.vouchercurrent where trackingnumber = '$trackingNumber'";
            // $result = $this->query($sql);
            // $data = $result->fetch_array();
            // $expensecode = !empty($data['PR_AccountCode']) ? $data['PR_AccountCode'] : "";
            // unset($data);

            $sql="select * from citydoc$trackingyear.programcode where trackingnumberinfra = '$trackingNumber' and category = 'Infrastructure Project'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $projectname = !empty($data['Name']) ? $data['Name'] : "";
            unset($data);
            $progress = 0;
            $sql="select * from citydoc$trackingyear.infra where trackingnumber = '$trackingNumber'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $location = $data['Location'];
            $brgy = $data['Barangay'];
            $progress =  $data['Progress'];
            $coordinates =  $data['Coordinates'];
            $map =  $data['Map'];
            $duration = !empty($data['Duration']) ? $data['Duration'] : "";
            unset($data);

            $sql = "SELECT `Function`, `Name`, EmployeeNumber FROM citydoc$trackingyear.projectmanpower WHERE trackingnumber = '$trackingNumber'";
            $result = $this->query($sql);

            if($_SESSION['CEOPDD'] == '1'){
                $cardHtml = '
                <div class="project-team-card">
                    <h2 class="project-team-title">Project Team</h2>
                    <div class="project-team-section">';

                // Define hardcoded roles
                $hardcodedRoles = [
                    "PoW Lead Engineer" => [],
                    "PoW Civil Engineer" => [],
                    "Project Surveyor" => [],
                    "Project Architect" => [],
                    "Project Electrical Engineer" => [],
                    "Project Plumber" => [],
                    "Project Structural Engineer" => [],
                    "Project Structural Engineer" => [],
                    "Construction Inspector" => []
                ];
                $employeeNum = '';
                while ($row = $result->fetch_array()) {
                    $role = $row['Function'];
                    $displayrole = '';

                    if ($role == 'Pos2') {
                        $displayrole = 'PoW Lead Engineer';
                    }elseif ($role == 'Pos1') {
                        $displayrole = 'PoW Civil Engineer';
                    }elseif ($role == 'Surveyor') {
                        $displayrole = 'Project Surveyor';
                    }elseif ($role == 'Pos3') {
                        $displayrole = 'Project Architect';
                    }elseif ($role == 'Pos4') {
                        $displayrole = 'Project Electrical Engineer';
                    }elseif ($role == 'Pos5') {
                        $displayrole = 'Project Plumber';
                    }elseif ($role == 'Pos6') {
                        $displayrole = 'Project Structural Engineer';
                    }elseif ($role == 'Inspector') {
                        $displayrole = 'Construction Inspector';
                    }

                    $member = $row['Name'];
                    $employeeNum = $row['EmployeeNumber'];

                    if (isset($hardcodedRoles[$displayrole])) {
                        $hardcodedRoles[$displayrole][] = $member;
                    }
                }

                foreach ($hardcodedRoles as $role => $members) {
                    $cardHtml .= '<div class="project-role" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span>' . htmlspecialchars($role) . '</span>
                                     <button class="ri-edit-box-line" style="margin-left:.3rem;font-weight:bold;color:var(--text-color);"  
                                        onclick="EditRole(\'' . htmlspecialchars($trackingNumber) . '\', \'' . htmlspecialchars($trackingyear) . '\',\'' . htmlspecialchars($role) . '\',\'' . htmlspecialchars($employeeNum) . '\',listofprojectresults)">
                                    </button>
                                </div>';
    
                    
                    if (!empty($members)) {
                        $cardHtml .= '<div class="project-members">' . implode('<br>', array_map('htmlspecialchars', $members)) . '</div>';
                    } else {
                        $cardHtml .= '<div class="project-members">-</div>'; 
                    }
                }

                $cardHtml .= '
                    </div>
                </div>';

                $status = $newRecord['Status'];

                $iframemap = "";
                if (!empty($map)) {
                
                    $iframemap = "<iframe src='$map' width='100%' height='300' style='border:0;' allowfullscreen='' loading='lazy' referrerpolicy='no-referrer-when-downgrade'></iframe>";
                } else {
                    $iframemap = ""; 
                }
                echo "
                    <div>
                        <div class='trackerheader' >
                            <table border='0'>
                                <tr>
                                    <td>
                                        <span class='tracker-title' style='color:rgba(51,117,147,1);'>" . $status . "  
                                           
                                        </span>
                                    </td>   
                                    <td style='text-align:right;width:0px;'>
                                     <button class='ri-edit-box-line' 
                                                style='margin-left:1rem;color:var(--text-color);font-weight:bold;font-size:var(--normal-font-size)' 
                                            onclick='openEditStatus(\"" . $trackingNumber . "\", \"" . $trackingyear . "\", \"" . $status . "\", listofprojectresults)'>
                                            </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan='2'>
                                        <p class='tracker-office'>" . htmlspecialchars($newRecord['OfficeName'], ENT_QUOTES) . "</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class='tracker-details'>
                        <table border='0'>
                            <tbody>
                                ";
                                // $fields = [
                                //     'TN' => 'TrackingNumber',
                                //     'Fund Year' => 'Year',
                                //     'Contructor' => 'Claimant',
                                //     'Document' => 'DocumentType',
                                //     'Fund' => 'Fund',
                                //     'Net Amount' => 'NetAmount'
                                // ];
                                // foreach ($fields as $label => $field) {
                                //     echo "
                                //     <tr>
                                //         <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                //         <td class='trackerlabel'>" . $label . "</td>
                                //         <td class='trackingspecs' " . 
                                //             ($field === 'NetAmount' ? "style='color: red; font-weight: bold; text-align:left;'" : "") . 
                                //             ($field === 'TrackingNumber' ? " id='tracknumid'" : "") . 
                                //             ($field === 'Year' ? " id='yearid'" : "") . ">
                                //             " . htmlspecialchars($newRecord[$field], ENT_QUOTES) . "
                                //         </td>
                                //     </tr>
                                //     ";
                                // }

                                echo "
                                    
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1);'>Project Name</td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                        $projectname
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1);'>Location  <button class='ri-edit-box-line' style='margin-left:1rem;font-weight:bold;color:var(--text-color);' onclick='openEditLocation(\"$trackingNumber\", \"$trackingyear\", \"listofprojectresults\")'></button></td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em; padding-bottom:1em'>
                                        $location
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1);'>Barangay <button class='ri-edit-box-line' style='margin-left:1rem;font-weight:bold;color:var(--text-color);' onclick='openEditBrgy(\"$trackingNumber\", \"$trackingyear\", \"listofprojectresults\")'></button></td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em; padding-bottom:1em'>
                                        $brgy
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1);'>TN</td>
                                        <td class='trackingspecs' > <span id='tracknumid'>$trackingNumber</span></td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1);'>Year</td>
                                        <td class='trackingspecs' > <span id='yearid'>".$newRecord['Year']."</span></td>
                                    </tr>
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='white-space:nowrap;color:rgba(51,117,147,1);'>Project Duration</td>
                                        <td class='trackingspecs' >
                                            ".$duration."
                                        </td>
                                    </tr>
                                    <tr style=''>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' style='color:rgba(51,117,147,1);'>Date Visited </td>
                                        <td class='trackingspecs' >
                                            <div>
                                            ".$datevisited."   <button class='ri-edit-box-line' style='margin-left:1rem;font-weight:bold;color:var(--text-color);' onclick='openEditDateVisited(\"$trackingNumber\", \"$trackingyear\", \"listofprojectresults\")'></button>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Coordinates  <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);' onclick='openEditCoordinates(\"$trackingNumber\", \"$trackingyear\", \"listofprojectresults\")'></button> </td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                        $coordinates 
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                        <td class='trackerlabel' colspan='2' style='color:rgba(51,117,147,1)'>Map  <button class='ri-edit-box-line' style='margin-left:1rem;color:var(--text-color);' onclick='openEditMap(\"$trackingNumber\", \"$trackingyear\", \"listofprojectresults\")'></button> </td>
                                    
                                    </tr>
                                    <tr>
                                        <td class='trackertd'></td>
                                        <td class='trackerlabel' colspan='2' style='font-weight:bold;padding-left:5px;text-align: left;padding-top:.1em;padding-bottom:1em'>
                                             $iframemap
                                        </td>
                                    </tr>


                                    " . $this->ListofProjectDetailsgenerateInfraUploadPDDButton($trackingNumber,$trackingyear,$numberRow++ ) . "
                                    " . $this->ImageContainer($trackingNumber,$trackingyear) . "
                                    <tr>
                                        <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td colspan='2' class='trackerlabel' style='vertical-align:top ; padding:5px;'>
                                        <div style='color:rgba(51,117,147,1);'>Video Link </div>
                                        <div style='margin-top:5px;'> $videolink </div>
                                    </td>

                                    </tr>";
                                    
                                echo "
                            </tbody>
                        </table>

                        $cardHtml
                        
                    </div>
                
                </div>";
            }

            
        }
        
    }



    function CreatePRQRInterface($trackingNumber,$newRecord,$trackingyear ){
        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );

        if($newRecord['Complex'] == '1' || empty($newRecord['Complex'])){
            $transactionClassification = 'Simple Transaction';
        }else if($newRecord['Complex'] == '2') {
            $transactionClassification = 'Complex Transaction';
        } 

        // echo $Message;
        // echo $unsuccessMessage;

        $sql="Select TrackingNumber,PR_TrackingNumber from citydoc$trackingyear.vouchercurrent where PR_TrackingNumber = '$trackingNumber'";
        $result = $this->query($sql);
        $data = $result->fetch_array();


        $numberRow = 1;
        $NewnumberRow = $numberRow + 11;

        echo "
            <div style=' border-radius: 10px; font-family: oswald; border-radius: 2px; overflow: hidden;width:auto;'>

                <!-- Header Section -->
                <div style='background-color: #00bfff; color: white; padding: 15px; text-align:left;'>
                    <span style='text-shadow:0px 0px 10px black; color:white;font-size:34px;letter-spacing:1px;font-weight:bold;'> " . $newRecord['TrackingType'] . " -  " . $newRecord['Status'] . "</span>
                    <p style='margin: 5px 0 0; font-size: 1em;'>" . $newRecord['OfficeName'] . " </p>
                </div>

                <!-- Details Section -->
                <table style='width: 100%; border-collapse: collapse;  line-height: 1.6; '>
                    <tbody style='font-size:18px;'>
                       <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:20px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> TN
                            </td>
                            <td style='font-weight:bold;color: #00bfff; text-decoration: underline; cursor: pointer;  padding-left: 10px;padding-top:20px;' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\")'>
                                " . $newRecord['TrackingNumber'] . "
                            </td>
                        </tr>

                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> ADV Number
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['ADV']."</td>
                        </tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> OBR 
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['OBR_Number']."</td>
                        </tr>
                        <tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> PR Sched
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$this->numberToQuarter($newRecord['PR_Month'])."</td>
                        </tr>
                        <tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Fund
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['Fund']."</td>
                        </tr>
                    </tbody>
                </table>


                
            </div>";

        // Pending Notes Section
        if (!empty($newRecord['Remarks'])) {
            echo "
            <div style='max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif;  border-radius: 2px; overflow: hidden; '>
                <div style=' padding: 15px;text-align:left;'>
                    <h2 style='margin: 0; font-size: 1.5em; color: #000; border-bottom: 1px solid rgba(225, 228, 224, 0.8); padding-bottom: 5px;'>Pending Notes</h2>
                    <p style='margin-top: 10px; font-size: 1em; color: #333;'>" . $newRecord['Remarks'] . "</p>
                </div>
            </div>";
        }
    }

    function CreatePOQRInterface($Message,$unsuccessMessage,$trackingNumber,$newRecord,$trackingyear){
        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );

        if($newRecord['Complex'] == '1' || empty($newRecord['Complex'])){
            $transactionClassification = 'Simple Transaction';
        }else if($newRecord['Complex'] == '2') {
            $transactionClassification = 'Complex Transaction';
        } 

        // $sql="Select TrackingNumber,PR_TrackingNumber from citydoc$trackingyear.vouchercurrent where PR_TrackingNumber = '$trackingNumber'";
        // $result = $this->query($sql);
        // $data = $result->fetch_array();

        // echo $Message;
        // echo $unsuccessMessage;

        $numberRow = 1;
        $NewnumberRow = $numberRow + 8;

        echo "
         <div style=' border-radius: 10px; font-family: oswald; border: 1px solid #ddd; border-radius: 2px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);width:100%'>

            <!-- Header Section -->
            <div style='background-color: #00bfff; color: white; padding: 15px; text-align:left;'>
                <span style='text-shadow:0px 0px 10px black; color:white;font-size:34px;letter-spacing:1px;font-weight:bold;'> " . $newRecord['TrackingType'] . " -  " . $newRecord['Status'] . "</span>
                <p style='margin: 5px 0 0; font-size: 1em;'>" . $newRecord['OfficeName'] . " </p>
            </div>
        
            <!-- Details Section -->
           <table style='width: 100%; border-collapse: collapse; background-color: #f9f9f9; line-height: 1.6;'>
                <tbody style='font-size:18px;'>
                   <tr>
                        <td style=' width: 200px;  padding-left: 40px; padding-top:10px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> TN
                        </td>
                        <td style='font-weight:bold;color: #00bfff; text-decoration: underline; cursor: pointer;  padding-left: 10px;padding-top:10px;' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\")'>
                            " . $newRecord['TrackingNumber'] . "
                        </td>
                        

                    </tr>
                    <tr>
                        <td style=' padding-left: 40px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Supplier
                        </td>
                        <td style='font-weight:bold;padding-left: 10px;padding-top:5px;'> " . $newRecord['Claimant'] . "</td>


                    </tr>
                    <tr>
                        <td style=' padding-left: 40px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Transaction Classification
                        </td>
                        <td style='font-weight:bold;padding-left: 10px;padding-top:5px;'>$transactionClassification</td>


                    </tr>
                    <tr>
                        <td style=' padding-left: 40px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> PO Number
                        </td>
                        <td style='font-weight:bold;padding-left: 10px;padding-top:5px;'>".$newRecord['PO_Number']."</td>

   
                    </tr>
                    <tr>
                       <td style=' padding-left: 40px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> PO Date
                        </td>
                        <td style='font-weight:bold;padding-left: 10px;padding-top:5px;'>".$newRecord['ADV']."</td>
                    </tr>
                    <tr>
                       <td style=' padding-left: 40px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> OBR 
                        </td>
                        <td style='font-weight:bold;padding-left: 10px;padding-top:5px;'>".$newRecord['OBR_Number']."</td>
                    </tr>

                    <tr>
                       <td style=' padding-left: 40px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Period
                        </td>
                        <td style='font-weight:bold;padding-left: 10px;padding-top:5px;'>" . $this->numberToQuarter($newRecord['PR_Month']) . "</td>
                    </tr>
                    <tr>
                       <td style=' padding-left: 40px;padding-top:5px;padding-bottom:10px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> PR Tracking Number
                        </td>
                        <td style='font-weight:bold;padding-left: 10px;padding-top:5px;padding-bottom:10px;'>".$newRecord['PR_TrackingNumber']."</td>
                    </tr>

                </tbody>
            </table>
        
        </div>";
        

        // Pending Notes Section
        if (!empty($newRecord['Remarks'])) {
            echo "
            <div style='max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif; border: 1px solid #ddd; border-radius: 2px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
                <div style='background-color: #f9f9f9; padding: 15px;text-align:left;'>
                    <h2 style='margin: 0; font-size: 1.5em; color: #000; border-bottom: 1px solid #000; padding-bottom: 5px;'>Pending Notes</h2>
                    <p style='margin-top: 10px; font-size: 1em; color: #333;'>" . $newRecord['Remarks'] . "</p>
                </div>
            </div>";
        }
    }

    function CreatePXQRInterface($Message,$unsuccessMessage,$trackingNumber,$newRecord,$trackingyear ){
        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );

        if($newRecord['Complex'] == '1' || empty($newRecord['Complex'])){
            $transactionClassification = 'Simple Transaction';
        }else if($newRecord['Complex'] == '2') {
            $transactionClassification = 'Complex Transaction';
        } 

        // echo $Message;




        $numberRow = 1;

        echo "
            <div style=' border-radius: 10px; font-family: oswald; border-radius: 2px; overflow: hidden;width:auto;'>

                <!-- Header Section -->
                <div style='background-color: #00bfff; color: white; padding: 15px; text-align:left;'>
                    <span style='text-shadow:0px 0px 10px black; color:white;font-size:34px;letter-spacing:1px;font-weight:bold;'> " . $newRecord['TrackingType'] . " -  " . $newRecord['Status'] . "</span>
                    <p style='margin: 5px 0 0; font-size: 1em;'>" . $newRecord['OfficeName'] . " </p>
                </div>

                <!-- Details Section -->
                <table style='width: 100%; border-collapse: collapse;  line-height: 1.6; '>
                    <tbody style='font-size:18px;'>
                       <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:20px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> TN
                            </td>
                            <td style='font-weight:bold;color: #00bfff; text-decoration: underline; cursor: pointer;  padding-left: 10px;padding-top:20px;' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\")'>
                                " . $newRecord['TrackingNumber'] . "
                            </td>
                        </tr>
                        <tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Claimant
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['Claimant']."</td>
                        </tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> ADV Number
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['ADV']."</td>
                        </tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> OBR 
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['OBR_Number']."</td>
                        </tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Document
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['DocumentType']."</td>
                        </tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Period
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['PeriodMonth']."</td>
                        </tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Claim Type
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['ClaimType']."</td>
                        </tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Fund
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['Fund']."</td>
                        </tr>
                        <tr>
                        <td style=' width: 150px;  padding-left: 40px; padding-top:10px;padding-bottom:20px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Net Amount
                            </td>
                            <td style='padding-bottom:20px;font-weight:bold;padding-left:10px;padding-top:5px;padding-bottom:10px;'>".number_format($newRecord['NetAmount'], 2)."</td>
                        </tr>
                    </tbody>
                </table>


                
            </div>";

        // Pending Notes Section
        if (!empty($newRecord['Remarks'])) {
            echo "
            <div style='max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif;  border-radius: 2px; overflow: hidden; '>
                <div style=' padding: 15px;text-align:left;'>
                    <h2 style='margin: 0; font-size: 1.5em; color: #000; border-bottom: 1px solid rgba(225, 228, 224, 0.8); padding-bottom: 5px;'>Pending Notes</h2>
                    <p style='margin-top: 10px; font-size: 1em; color: #333;'>" . $newRecord['Remarks'] . "</p>
                </div>
            </div>";
        }
    }

    function CreatePYQRInterface($Message,$unsuccessMessage,$trackingNumber,$newRecord,$trackingyear){
        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );

        if($newRecord['Complex'] == '1' || empty($newRecord['Complex'])){
            $transactionClassification = 'Simple Transaction';
        }else if($newRecord['Complex'] == '2') {
            $transactionClassification = 'Complex Transaction';
        } 

        echo $Message;



        $numberRow = 1;

        echo "
        <div style=' border-radius: 10px; font-family: oswald; border: 1px solid #ddd; border-radius: 2px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);width:auto;'>

            <!-- Header Section -->
            <div style='background-color: #00bfff; color: white; padding: 15px; text-align:left;'>
                <span style='text-shadow:0px 0px 10px black; color:white;font-size:34px;letter-spacing:1px;font-weight:bold;'> " . $newRecord['TrackingType'] . " -  " . $newRecord['Status'] . "</span>
                <p style='margin: 5px 0 0; font-size: 1em;'>" . $newRecord['OfficeName'] . " </p>
            </div>

            <!-- Details Section -->
            <table style='width: 100%; border-collapse: collapse; background-color: #f9f9f9; line-height: 1.6; '>
                <tbody style='font-size:18px;'>
                    <tr>
                        <td style=' width: 200px;  padding-left: 10px; padding-top:10px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> TN
                        </td>
                        <td style='font-weight:bold;color: #00bfff; text-decoration: underline; cursor: pointer;  padding-left: 10px;padding-top:10px;' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\")'>
                            " . $newRecord['TrackingNumber'] . "
                        </td>
                    </tr>
                    <tr>
                        <td style=' padding-left:10px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Transaction Classification
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>$transactionClassification</td>
                    </tr>
                    <tr>
                        <td style=' padding-left:10px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Claimant
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['Claimant']."</td>
                    </tr>
                    <tr>
                       <td style=' padding-left:10px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> ADV Number
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['ADV']."</td>
                    </tr>
                    <tr>
                       <td style=' padding-left:10px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> OBR 
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['OBR_Number']."</td>
                    </tr>
                    <tr>
                       <td style=' padding-left:10px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Document
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['DocumentType']."</td>
                    </tr>
                    <tr>
                       <td style=' padding-left:10px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Period
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['PeriodMonth']."</td>
                    </tr>
                    <tr>
                       <td style=' padding-left:10px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Claim Type
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['ClaimType']."</td>
                    </tr>
                    <tr>
                       <td style=' padding-left:10px;padding-top:5px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Fund
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['Fund']."</td>
                    </tr>
                    <tr>
                       <td style=' padding-left:10px;padding-top:5px;padding-bottom:10px;'>
                            <small style='padding-right:2px;'>".$numberRow++."</small> Net Amount
                        </td>
                        <td style='font-weight:bold;padding-left:10px;padding-top:5px;padding-bottom:10px;'>".number_format($newRecord['NetAmount'], 2)."</td>
                    </tr>
                </tbody>
            </table>


            
        </div>";

        // Pending Notes Section
        if (!empty($newRecord['Remarks'])) {
            echo "
            <div style='max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif; border: 1px solid #ddd; border-radius: 2px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
                <div style='background-color: #f9f9f9; padding: 15px;text-align:left;'>
                    <h2 style='margin: 0; font-size: 1.5em; color: #000; border-bottom: 1px solid #000; padding-bottom: 5px;'>Pending Notes</h2>
                    <p style='margin-top: 10px; font-size: 1em; color: #333;'>" . $newRecord['Remarks'] . "</p>
                </div>
            </div>";
        }
    }

    function CreateNFQRInterface($successMessage,$unsuccessMessage,$trackingNumber,$newRecord){

        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );
        if($newRecord['Complex'] == '1' || empty($newRecord['Complex'])){
            $transactionClassification = 'Simple Transaction';
        }else if($newRecord['Complex'] == '2') {
            $transactionClassification = 'Complex Transaction';
        } 

        echo $successMessage;
        echo $unsuccessMessage;



        $numberRow = 1;

        echo "
            <div style=' border-radius: 10px; font-family: oswald; border: 1px solid #ddd; border-radius: 2px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);width:auto;'>

                <!-- Header Section -->
                <div style='background-color: #00bfff; color: white; padding: 15px; text-align:left;'>
                    <span style='text-shadow:0px 0px 10px black; color:white;font-size:34px;letter-spacing:1px;font-weight:bold;'> " . $newRecord['TrackingType'] . " -  " . $newRecord['Status'] . "</span>
                    <p style='margin: 5px 0 0; font-size: 1em;'>" . $newRecord['OfficeName'] . " </p>
                </div>

                <!-- Details Section -->
                <table style='width: 100%; border-collapse: collapse; background-color: #f9f9f9; line-height: 1.6; '>
                    <tbody style='font-size:18px;'>
                        <tr>
                            <td style=' width: 200px;  padding-left: 10px; padding-top:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> TN
                            </td>
                            <td style='font-weight:bold;color: #00bfff; text-decoration: underline; cursor: pointer;  padding-left: 10px;padding-top:10px;' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\")'>
                                " . $newRecord['TrackingNumber'] . "
                            </td>
                        </tr>
                        <tr>
                            <td style=' padding-left:10px;padding-top:5px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Transaction Classification
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>$transactionClassification</td>
                        </tr>
                        <tr>
                            <td style=' padding-left:10px;padding-top:5px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Claimant
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['Claimant']."</td>
                        </tr>
                        <tr>
                           <td style=' padding-left:10px;padding-top:5px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> ADV Number
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['ADV']."</td>
                        </tr>
                        <tr>
                           <td style=' padding-left:10px;padding-top:5px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> OBR 
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['OBR_Number']."</td>
                        </tr>
                        <tr>
                           <td style=' padding-left:10px;padding-top:5px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Document
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['DocumentType']."</td>
                        </tr>
                        <tr>
                           <td style=' padding-left:10px;padding-top:5px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Period
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['PeriodMonth']."</td>
                        </tr>
                        <tr>
                           <td style=' padding-left:10px;padding-top:5px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Claim Type
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['ClaimType']."</td>
                        </tr>
                        <tr>
                           <td style=' padding-left:10px;padding-top:5px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Fund
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;'>".$newRecord['Fund']."</td>
                        </tr>
                        <tr>
                           <td style=' padding-left:10px;padding-top:5px;padding-bottom:10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Net Amount
                            </td>
                            <td style='font-weight:bold;padding-left:10px;padding-top:5px;padding-bottom:10px;'>".number_format($newRecord['NetAmount'], 2)."</td>
                        </tr>
                    </tbody>
                </table>


                
            </div>";

        // Pending Notes Section
        if (!empty($newRecord['Remarks'])) {
            echo "
            <div style='max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif; border: 1px solid #ddd; border-radius: 2px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
                <div style='background-color: #f9f9f9; padding: 15px;text-align:left;'>
                    <h2 style='margin: 0; font-size: 1.5em; color: #000; border-bottom: 1px solid #000; padding-bottom: 5px;'>Pending Notes</h2>
                    <p style='margin-top: 10px; font-size: 1em; color: #333;'>" . $newRecord['Remarks'] . "</p>
                </div>
            </div>";
        }
    }


    function CreateIPQRInterface($successMessage,$unsuccessMessage,$trackingNumber,$newRecord){
        $newRecord = $this->searchTrackingNumber2022($trackingNumber,$trackingyear );

        if($newRecord['Complex'] == '1' || empty($newRecord['Complex'])){
            $transactionClassification = 'Simple Transaction';
        }else if($newRecord['Complex'] == '2') {
            $transactionClassification = 'Complex Transaction';
        } 

        echo $successMessage;
        echo $unsuccessMessage;



        $numberRow = 1;

        echo "
            <div style=' border-radius: 10px; font-family: oswald; border: 1px solid #ddd; border-radius: 2px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);width:auto;'>

                <!-- Header Section -->
                <div style='background-color: #00bfff; color: white; padding: 15px; text-align:left;'>
                    <span style='text-shadow:0px 0px 10px black; color:white;font-size:34px;letter-spacing:1px;font-weight:bold;'> " . $newRecord['TrackingType'] . " -  " . $newRecord['Status'] . "</span>
                    <p style='margin: 5px 0 0; font-size: 1em;'>" . $newRecord['OfficeName'] . " </p>
                </div>

                <!-- Details Section -->
                <table style='width: 100%; border-collapse: collapse; background-color: #f9f9f9; line-height: 1.6; '>
                    <tbody style='font-size:18px;'>
                        <tr>
                            <td style=' width: 40%;  padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> TN
                            </td>
                            <td style='font-weight:bold;color: #00bfff; text-decoration: underline; cursor: pointer;  padding: 10px;' onclick=\"showTrackingNumber('" . htmlspecialchars($newRecord['TrackingNumber'], ENT_QUOTES) . "')\")'>
                                " . $newRecord['TrackingNumber'] . "
                            </td>
                        </tr>
                        <tr>
                            <td style=' padding-left:10px'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Transaction Classification
                            </td>
                            <td style='font-weight:bold;padding-left:10px'>$transactionClassification</td>
                        </tr>
                        <tr>
                            <td style='  padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Claimant
                            </td>
                            <td style='font-weight:bold; padding: 10px;'>".$newRecord['Claimant']."</td>
                        </tr>
                        <tr>
                            <td style='  padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> ADV Number
                            </td>
                            <td style='font-weight:bold; padding: 10px;'>".$newRecord['ADV']."</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> OBR
                            </td>
                            <td style='font-weight:bold; padding: 10px;'>".$newRecord['OBR_Number']."</td>
                        </tr>
                        <tr>
                            <td style='  padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Document
                            </td>
                            <td style='font-weight:bold; padding: 10px;'>".$newRecord['DocumentType']."</td>
                        </tr>
                        <tr>
                            <td style='  padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Period
                            </td>
                            <td style='font-weight:bold; padding: 10px;'>".$newRecord['PeriodMonth']."</td>
                        </tr>
                        <tr>
                            <td style='  padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Claim Type
                            </td>
                            <td style='font-weight:bold; padding: 10px;'>".$newRecord['ClaimType']."</td>
                        </tr>
                        <tr>
                            <td style='  padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Fund
                            </td>
                            <td style='font-weight:bold; padding: 10px;'>".$newRecord['Fund']."</td>
                        </tr>
                        <tr>
                            <td style='  padding: 10px;'>
                                <small style='padding-right:2px;'>".$numberRow++."</small> Net Amount
                            </td>
                            <td style='font-weight:bold; color: red; padding: 10px;'>".number_format($newRecord['NetAmount'], 2)."</td>
                        </tr>
                    </tbody>
                </table>


                
            </div>";

        // Pending Notes Section
        if (!empty($newRecord['Remarks'])) {
            echo "
            <div style='max-width: 800px; margin: 20px auto; font-family: Arial, sans-serif; border: 1px solid #ddd; border-radius: 2px; overflow: hidden; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);'>
                <div style='background-color: #f9f9f9; padding: 15px;text-align:left;'>
                    <h2 style='margin: 0; font-size: 1.5em; color: #000; border-bottom: 1px solid #000; padding-bottom: 5px;'>Pending Notes</h2>
                    <p style='margin-top: 10px; font-size: 1em; color: #333;'>" . $newRecord['Remarks'] . "</p>
                </div>
            </div>";
        }
    }



}
$sheet = new Sheets();
	

