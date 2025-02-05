<?php
error_reporting(E_ALL & ~E_DEPRECATED);
class Sheets extends MySQLDatabase {

    function ModeOfProcurementTitle($mode) {
			
        $modeList = ['Competitive Bidding','Shopping','Shopping 52.1.b','Alternative','Agency to Agency','Negotiated','Negotiated Procurement 53.9(SVP)','Negotiated Procurement 53.1(TFB)',
                     'Negotiated Procurement 53.6(MS)','Negotiated Procurement 53.7','Negotiated Procurement 53.2(E.C.)','Postal Office','Direct Contracting','Repeat Order','Twice Failed Bidding(TFB)',
                     'Extension of Contract Appx. 21 Sec. 3.31','Renewal of Contract Based on Appendix 21 3.3.1.3','Agency to Agency (DBM)','Lease of Real Property Sec 5.10',];

        return $modeList[$mode-1];

    }

    function generateInfraUploadPDDButton($trackingNumber, $trackingyear) {
        $sql = "Select count(*) as count from citydoc$trackingyear.infrauploads where TrackingNumber = '$trackingNumber' and type = 'Image'";
        $result = $this->query($sql);
        $data = $result->fetch_array();
        $imgCount = $data['count'];

        $sql = "Select filename from citydoc$trackingyear.infrauploads where TrackingNumber = '$trackingNumber' and type = 'Video'";
        $result = $this->query($sql);
        $data = $result->fetch_array();
        $videolink = $data ? $data['filename'] : '';

        if( $_SESSION['perm'] == '40'){
            return "
                <div class='uploadpicture-container' style=''>
                    Pre-Construction Upload
                    <div style='display: flex; justify-content: space-between; align-items: start;'>
                        <span>Photos: $imgCount</span>
                        <button class='btn btn-primary' onclick='openInfrapddUploader($trackingNumber,$trackingyear)' style='vertical-align:bottom;padding:0px 5px;border-radius:2px;margin-bottom:0px;margin-right:10px;'>
                            +
                        </button>
                    </div>
                </div>
            ";
        }
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
            $sql="select * from citydoc$trackingyear.infrauploads where trackingnumber = '$trackingNumber' order by id desc;";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $videolink = !empty($data['Filename']) ? $data['Filename'] : "";
            if($videolink == 'pre_pic'){
                $videolink = '';
            }
            $datevisited = !empty($data['DateVisit']) ? $data['DateVisit'] : "";
            unset($data);


            $sql="select * from citydoc$trackingyear.vouchercurrent where trackingnumber = '$trackingNumber'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $expensecode = !empty($data['PR_AccountCode']) ? $data['PR_AccountCode'] : "";
            unset($data);

            $sql="select * from citydoc$trackingyear.programcode where trackingnumberinfra = '$trackingNumber' and category = 'Infrastructure Project'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $projectname = !empty($data['Name']) ? $data['Name'] : "";
            unset($data);

            $sql="select * from citydoc$trackingyear.infra where trackingnumber = '$trackingNumber'";
            $result = $this->query($sql);
            $data = $result->fetch_array();
            $progress = !empty($data['Progress']) ? $data['Progress'] : "";
            $duration = !empty($data['Duration']) ? $data['Duration'] : "";
            unset($data);

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
                                'Fund Year' => 'Year',
                                'Contructor' => 'Claimant',
                                'Document' => 'DocumentType',
                                'Fund' => 'Fund',
                                'Net Amount' => 'NetAmount'
                            ];
                            foreach ($fields as $label => $field) {
                                echo "
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>" . $label . "</td>
                                    <td class='trackingspecs' " . 
                                        ($field === 'NetAmount' ? "style='color: red; font-weight: bold; text-align:left;'" : "") . 
                                        ($field === 'TrackingNumber' ? " id='tracknumid'" : "") . 
                                        ($field === 'Year' ? " id='yearid'" : "") . ">
                                        " . htmlspecialchars($newRecord[$field], ENT_QUOTES) . "
                                    </td>
                                </tr>
                                ";
                            }
                            echo "
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>Project Name</td>
                                    <td class='trackingspecs' >
                                        ".$projectname."
                                    </td>
                                </tr>
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>Progress</td>
                                    <td class='trackingspecs' >
                                        ".$progress."%
                                    </td>
                                </tr>
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>Expense Account</td>
                                    <td class='trackingspecs' >
                                        ".$expensecode."
                                    </td>
                                </tr>
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>Project Duration</td>
                                    <td class='trackingspecs' >
                                        ".$duration."
                                    </td>
                                </tr>
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>Video Link</td>
                                    <td class='trackingspecs' >
                                        ".$videolink."
                                    </td>
                                </tr>
                                <tr>
                                    <td class='trackertd'><small>" . $numberRow++ . "</small></td>
                                    <td class='trackerlabel'>Date Visited</td>
                                    <td class='trackingspecs' >
                                        ".$datevisited."
                                    </td>
                                </tr>";
                            echo "
                        </tbody>
                    </table>
                </div>
                 " . $this->generateInfraUploadPDDButton($trackingNumber,$trackingyear) . "
            </div>";

            
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
	

