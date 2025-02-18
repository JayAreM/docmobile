<?php
if (!isset($_SESSION)) {
    session_start();
}

defined('ROOTER') ? NULL : define("ROOTER", "../");
require_once(ROOTER . 'javascript/ajaxFunction.php');
require_once(ROOTER . 'includes/database.php');

	if(!isset($_SESSION['employeeNumber'])){
		$link = "<script>window.open('login.php','_self')</script>";
		echo $link;
	}

	// Check privilege and redirect if privilege is 6 and 7 go to project cleansing
	if (isset($_SESSION['privy'] ) && ($_SESSION['privy'] == '6' )) {

		$link = "<script> window.open('../../inv/interface/main.php', '_self')</script>";
		echo $link;
	}


    $officeCode = $_SESSION['officeCode'];

    $employeeNumber = $_SESSION['employeeNumber'];

    // $sql = "SELECT COUNT(*) AS TotalCount
    //     FROM (
    //         SELECT * 
    //         FROM citydoc2023.voucherhistory 
    //         WHERE ModifiedBy = '$employeeNumber' AND status = 'admin received'
    //         UNION ALL
    //         SELECT * 
    //         FROM citydoc2023.voucherhistory 
    //         WHERE ModifiedBy = '$employeeNumber' AND status = 'admin received'
    //         UNION ALL
    //         SELECT * 
    //         FROM citydoc2024.voucherhistory 
    //         WHERE ModifiedBy = '$employeeNumber' AND status = 'admin received'
    //         UNION ALL
    //         SELECT * 
    //         FROM citydoc2025.voucherhistory 
    //         WHERE ModifiedBy = '$employeeNumber' AND status = 'admin received'
    //     ) AS TotalReceived";
    

    // $result = $database->query($sql);
   
    // $year = date('Y');

    // $data = $database->fetch_array($result);

    // $sql1 = "SELECT  *, DATEDIFF(CURRENT_DATE, STR_TO_DATE(DateModified, '%Y-%m-%d %H:%i')) AS DaysAgo , Status
    //         FROM 
    //         citydoc2025.voucherhistory where ModifiedBy = '$employeeNumber' and DateModified like '%$year%'";
    // $result1 = $database->query($sql1);
    // $data1 = $database->fetch_array($result1);

    // if (isNotMobile()) {
    //     $link = "<script>window.open('../../../citydoc2025/interface/main.php','_self')</script>";
    // } 
    // if (isset($link)) {
    //     echo $link;
    //     exit();
    // }
    
    // function isNotMobile() {
    //     $userAgent = $_SERVER['HTTP_USER_AGENT'];
    //     $mobileAgents = ['Mobile', 'Android', 'Silk/', 'Kindle', 'BlackBerry', 'Opera Mini', 'Opera Mobi'];
    
    //     foreach ($mobileAgents as $agent) {
    //         if (strpos($userAgent, $agent) !== false) {
    //             return false; // If it's a mobile device, return false
    //         }
    //     }
    //     return true; // If no mobile keywords found, it's NOT a mobile
    // }
    // var_dump( $_SESSION['CEOUser']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--=============== REMIXICONS ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/system2.png"
        type="image/x-icon" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
    <title>DocMobile</title>
</head>
<body id="body" style="overflow-x: hidden;" class="animate__animated animate__fadeIn">
    <!--=============== HEADER ===============-->
    <header class="header" id="header">
        <div class="header__container">
            <a href="#" class="header__logo">
                <i class=""><img src="../images/system2.png" alt="Sample Image" width="20"></i>
                <span>DocMobile</span>
            </a>

            <button class="header__toggle" id="header-toggle">
                <i class="ri-menu-line"></i>
            </button>
        </div>

    </header>

    <!--=============== SIDEBAR ===============-->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar__container">
            <div class="sidebar__user">
                <div class="sidebar__img">
                    <!-- <img src="assets/img/perfil.png" alt="image"> -->
                    <img src="../images/system2.png">
                </div>

                <div class="sidebar__info">
                    <h3><?php echo $_SESSION['firstName'];?></h3>
                    <span style=""><?php echo $_SESSION['officeName'];?></span>
                    <br>
                    <span style=""><?php echo $_SESSION['PositionTitle'];?></span>
                </div>
            </div>

            <div class="sidebar__content">
                <div>
                    <h3 class="sidebar__title">MANAGE</h3>

                    <div class="sidebar__list">
                        <a href="#" class="sidebar__link" onclick="showSection('dashboard-section')">
                            <i class="ri-pie-chart-2-fill"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="#" class="sidebar__link" onclick="showSection('Tracker-section')" id="trackerclicksection">
                            <i class="ri-search-line"></i>
                            <span>Tracker</span>
                        </a>

                        <a href="#" class="sidebar__link" onclick="showSection('myprojects-section')">
                            <i class="ri-task-line"></i>
                            <span>My Projects</span>
                        </a>

                        <?php if (isset($_SESSION['CEOUser']) && $_SESSION['CEOUser'] === 'Checker'): ?>
                            <a href="#" class="sidebar__link" onclick="showSection('listofprojects-section')">
                                <i class="ri-list-check-3"></i>
                                <span>List of Projects</span>
                            </a>
                        <?php endif; ?>

                        <!-- <a href="#" class="sidebar__link" onclick="showSection('AutoReceive-section')" 
                        <?php //echo ($_SESSION['perm'] == '40') ? 'style="display: none;"' : ''; ?>
                        >
                            <i class="ri-qr-scan-2-line"></i>
                            <span>QR Auto Receive</span>
                        </a> -->


                    </div>
                </div>

            </div>

            <div class="sidebar__actions">
                <button>
                    <i class="ri-moon-clear-fill sidebar__link sidebar__theme" id="theme-button">
                        <span>Theme</span>
                    </i>
                </button>

                <button onclick="logout()" class="sidebar__link">
                    <i class="ri-logout-box-r-fill"></i>
                    <span >Log Out</span>
                </button>
            </div>
        </div>
    </nav>

    <!--=============== MAIN CONTENT ===============-->
    <main class="main container" id="main" style="overflow-x: hidden;">
        <!-- Dashboard Section -->
        <section id="dashboard-section">
            <h1>Dashboard</h1>
            <p>Welcome to the dashboard overview.</p>

        </section>

        <!-- Tracker Section -->
        <section id="Tracker-section" style="display: none;">
            <div style="margin-inline:1rem;">
                <!-- <h1>Tracker</h1> -->
                <div class="search-container">
                    <select class="search-dropdown" id="yearInput">
                        <option value="">Year</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                    </select>
                    <input type="text" id="trackingNumberInput" class="search-input" placeholder="Search..." onkeypress="checkEnter(event)">
                    <button class="search-button"><i class="fa fa-search" onclick="searchTracking()"></i></button>
                </div>

                <div style="max-height:73vh;overflow-x:hidden;overflow-y:auto;" id='gusion'>
                    <div id="searchcontainer" class="tracker-results" ></div>

                </div>
               

            </div>
        </section>





        <!-- AutoReceive Section -->
        <section id="AutoReceive-section" style="display: none;height:auto;overflow-x:hidden">
            <h1>QR Auto Receive</h1>
            <input type="text" id="ok" class="search-input" placeholder="Scan a QR" disabled style="display:none;font-family: 'Arial', sans-serif; padding: 15px; width: 100%; border-radius: 10px; border: 1px solid #ddd; font-size: 16px; box-sizing: border-box; outline: none; color: #333; background-color: #fff; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

            <div class="container autoreceiver-container">
                <!-- QR Scanner will appear here, above the button -->
                <div id="qr-reader" >


                </div>

                <div id="scanningAnimation" class="scananime" style="display:none;">
                    <p class="hideinmobile"style="font-family: 'Arial', sans-serif; font-size: 14px; color: #666; margin-top: 10px;" id='status'>Please scan the QR code to proceed.</p>
                    <div id="scanning-bar" class="scanning-bar" style="width: 100px; height: 5px; background-color: #00bfff; margin: 0 auto; position: relative; ">
                        <div class="scanner-line" ></div>
                    </div>
                    <p class="hideinmobile"style="font-family: 'Arial', sans-serif; font-size: 16px; color: #00bfff; margin-top: 10px;">Scanning...</p>
                </div>

                <!-- Open Camera button -->
                <div style="position:relative;margin-top:5px;">
                    <!-- <button id="startScanner" style="padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; margin-top:50px;">Scan</button> -->
                    <i id="startScanner" class="fa-solid fa-camera fa-bounce" style='font-size:45px;color: #00509E; cursor:pointer;'></i>
                    <div id="scannerStatus" style="font-family: 'Arial', sans-serif;font-size:10px;color: #666;">Please scan the QR code to proceed</div>

                </div>

                <div id="resultsearchcontainer" style="position:relative;">
                
                </div>
            </div>
        </section>


        <section id="myprojects-section" style="display:none;">
            <div>
                <div class="select-container" style="">
                    <center><h1>My Projects</h1></center>
             
                    <div class="custom-select" >
                        <select id="yearSelect" onchange="showSelectedYear()">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                        <span class="select-arrow">&#9662;</span>
                    </div>
                </div>

           
                <div id="mycardprojectresults" class="myproject-results">

                </div>
        
                
                
            </div>
        </section>


        <section id="listofprojects-section" style="display:none;">
            <div>
                <div class="select-container">
             

                    <!-- <div class="custom-select" id="customSelect">
                        <select id="listprojectyearSelect" onchange="showSelectedYearListofProject()">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                        <span class="select-arrow">&#9662;</span>
                    </div> -->
                        
                    <!-- <div class="input-wrapper" onclick="focusInput()">
                        <button class="icon ri-search-line"></button>
                        <input placeholder="Search ..." class="input" name="text" type="text" id="searchProject" 
                            onfocus="hideElements()" onblur="showElements()">
                    </div> -->

                    <div class="InputContainer">
                        <input
                            placeholder="Search"
                            id="searchProject"
                            class="input"
                            name="text"
                            type="text"
                            onkeyup="filterProjects()"
                        />

                        <label class="labelforsearch ri-search-line" for="input">
                            
                        </label>
                    </div>


                    <i id="filterButton" class="ri-equalizer-line" onclick="toggleFilterProjects()"></i>

                    <div class="filter-dropdown" id="filterDropdown">
                    <div class="filter-header">
                        <span>Filter</span>
                        <button class="close-btn" onclick="toggleFilterProjects()">×</button>
                    </div>



                    <!-- Type Selection -->
                    <div class="filter-group">
                        <label>Year</label>
                        <select id="listprojectyearSelect" onchange="showSelectedYearListofProject()">
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2023">2023</option>
                        </select>
                    </div>

                    <!-- Status Selection -->
                    <!-- <div class="filter-group">
                        <label>Status</label>
                        <select id="status">
                            <option value="">Select Status</option>
                            <option value="Done">✅ Done</option>
                            <option value="In Progress">🔵 In Progress</option>
                            <option value="Pending">🟠 Pending</option>
                        </select>
                    </div> -->

                    <div class="filter-group">
                        <label>Project Type</label>
                        <select id="projecttype" onchange="filterProjects()">
                            <option value=""></option>
                            <option >Building</option>
                            <option >Disaster Response and Rescue Equipment</option>
                            <option >Flood Control Systems</option>
                        </select>
                    </div>


                    <div class="filter-group">
                        <label>Barangay</label>
                        <select id="brgy">
                            <option value=""></option>
                            <option value="Done">✅ Done</option>
                            <option value="In Progress">🔵 In Progress</option>
                            <option value="Pending">🟠 Pending</option>
                        </select>
                    </div>
                    
                </div>

        
                </div>


                


                
               
                <span style="color:var(--title-color);font-weight:bold;">List of Projects</span>

           
                <div id="listofprojectresults" class="listofprojectresults">

                </div>
        
                
                
            </div>

              <!-- Filter Dropdown -->
             
        </section>


        <!----------------------------- Modals Section ---------->

        <div id="EditStatusModal" class="uploader-overlay" style="display:none;">
            <div class="uploader-content">
                <span class="close-btn" onclick="closeEditStatus()">&times;</span>
                <h2>Edit Status</h2>

                <!-- <label for="updatedstatus">Status</label> -->
                <select id="updatedstatus" class="input-field">
                    <option value="Project Encoded">Project Encoded</option>
                    <option value="Requesting for Fund Certification">Requesting for Fund Certification</option>
                    <option value="Acknowledge Certification Request">Acknowledge Certification Request</option>
                    <option value="Fund Certification Signed">Fund Certification Signed</option>
                    <option value="Schedule for Inspection">Schedule for Inspection</option>
                    <option value="For Inspection">For Inspection</option>
                    <option value="Inspected - Not Feasible">Inspected - Not Feasible</option>
                    <option value="Preparation of Plans, PoW, and Detailed Estimates">Preparation of Plans, PoW, and Detailed Estimates</option>
                    <option value="Plans, PoW, and Detailed Estimates for Approval">Plans, PoW, and Detailed Estimates for Approval</option>
                    <option value="Plans, PoW, and Detailed Estimates Signed">Plans, PoW, and Detailed Estimates Signed</option>
                    <option value="For BAC Procedures">For BAC Procedures</option>
                    <option value="Drafting Invitation to Bid">Drafting Invitation to Bid</option>
                    <option value="Checking Invitation to Bid">Checking Invitation to Bid</option>
                    <option value="Invitation to Bid for Adjustment">Invitation to Bid for Adjustment</option>
                    <option value="Invitation to Bid for Pick-up">Invitation to Bid for Pick-up</option>
                    <option value="Invitation to Bid for Transmit">Invitation to Bid for Transmit</option>
                    <option value="Invitation to Bid for Signature">Invitation to Bid for Signature</option>
                    <option value="Invitation to Bid Signed">Invitation to Bid Signed</option>
                    <option value="Posting">Posting</option>
                    <option value="Bidding Schedule">Bidding Schedule</option>
                    <option value="Post Qualification">Post Qualification</option>
                    <option value="Letters for Signature">Letters for Signature</option>
                    <option value="Letters Signed">Letters Signed</option>
                    <option value="Notice of Award for Transmit">Notice of Award for Transmit</option>
                    <option value="Notice of Award for Admin Signature">Notice of Award for Admin Signature</option>
                    <option value="Notice of Award Admin Signed">Notice of Award Admin Signed</option>
                    <option value="NoA and Contract for Contractor's Signature">NoA and Contract for Contractor's Signature</option>
                    <option value="NoA and Contract Released to Contractor">NoA and Contract Released to Contractor</option>
                    <option value="NoA and Contract Contractor Signed">NoA and Contract Contractor Signed</option>
                    <option value="Obligation Request Preparation">Obligation Request Preparation</option>
                    <option value="OBR for Implementing Office Signature">OBR for Implementing Office Signature</option>
                    <option value="OBR Implementing Office Signed">OBR Implementing Office Signed</option>
                    <option value="CBO Received">CBO Received</option>
                    <option value="Pending at CBO">Pending at CBO</option>
                    <option value="Pending Released - CBO">Pending Released - CBO</option>
                    <option value="Obligation Request for Pick-up">Obligation Request for Pick-up</option>
                    <option value="Obligation Request Signed">Obligation Request Signed</option>
                    <option value="Contract and NTP for City Engineer's Signature">Contract and NTP for City Engineer's Signature</option>
                    <option value="Contract and NTP Signed">Contract and NTP Signed</option>
                    <option value="Document Filing">Document Filing</option>
                    <option value="CAO Received">CAO Received</option>
                    <option value="Pending at CAO">Pending at CAO</option>
                    <option value="Pending Released - CAO">Pending Released - CAO</option>
                    <option value="Document for Pick-up - CAO">Document for Pick-up - CAO</option>
                    <option value="Contract and NTP Transmit to Admin">Contract and NTP Transmit to Admin</option>
                    <option value="Contract and NTP for Admin Signature">Contract and NTP for Admin Signature</option>
                    <option value="Pending at Admin">Pending at Admin</option>
                    <option value="Pending Released - Admin">Pending Released - Admin</option>
                    <option value="Document for Pick-up - Admin">Document for Pick-up - Admin</option>
                    <option value="Contract for Notarization">Contract for Notarization</option>
                    <option value="Released to Contractor for Notarization">Released to Contractor for Notarization</option>
                    <option value="Contract Notarized">Contract Notarized</option>
                    <option value="Prepare Letter for Pick-up NTP">Prepare Letter for Pick-up NTP</option>
                    <option value="Forwarded Letter for Pick-up NTP">Forwarded Letter for Pick-up NTP</option>
                    <option value="NTP Released to Contractor">NTP Released to Contractor</option>
                    <option value="NTP Received from Contractor">NTP Received from Contractor</option>
                    <option value="NTP Forwarded to Construction Division">NTP Forwarded to Construction Division</option>
                </select>

                <button class="btn btn-primary" onclick="UpdateStatus()">Update</button>
            </div>
        </div>



        <!-- Modal InfraPDD UPLOADER -->
        <div id="infrapddUploader" class="uploader-overlay" style="display:none;">
            <div class="uploader-content">
                <span class="close-btn" onclick="closeInfrapddUploader()">&times;</span>
                <h2>Pre-Construction Upload</h2>
                
                <?php if ($_SESSION['perm'] == '41') { ?>
                    <label for="progress">Progress</label>
                    <input type="number" id="progress" class="input-field" placeholder="Enter progress %">
                <?php } ?>

                <label for="infraUpFilePre">Upload Pictures</label>
                <input type="file" id="infraUpFilePre" class="input-field" multiple onchange="displayFileNames()">
                <!-- File Names Display Area -->
                <div id="fileNames" class="file-names"></div>


                <label for="infraVisitDatePre">Date Visited</label>
                <input id="infraVisitDatePre" type="date" class="input-field">


                <label for="infraVideoLinkPre">Video Link</label>
                <input type="url" id="infraVideoLinkPre" class="input-field" placeholder="Enter video link">

                <button class="btn btn-primary" onclick="saveInfraUploadPre()">Upload</button>
            </div>
        </div>
        
        <div id="infrapddUploadervideo" class="uploader-overlay" style="display:none;">
            <div class="uploader-content">
                <span class="close-btn" onclick="closeInfrapddUploaderVideo()">&times;</span>
                <h2>Pre-Constructiom Video Link</h2>

                <label for="videoLink">Video Link</label>
                <input type="url" id="videoLink" class="input-field" placeholder="Enter video link">

                <button class="btn btn-primary">Save</button>
            </div>
        </div>

        <div id="EditDateVisited" class="uploader-overlay" style="display:none;">
            <div class="uploader-content">
                <span class="close-btn" onclick="closeEditDateVisited()">&times;</span>
                <h2>Edit Date Visited</h2>

                <!-- <label for="editdatevisit">Date Visited</label> -->
                <input type="date" id="editdatevisit" class="input-field" placeholder="Enter Date">

                <button class="btn btn-primary" onclick="UpdatePreConDateVisit()">Update</button>
            </div>
        </div>

        <div id="EditVideoLink" class="uploader-overlay" style="display:none;">
            <div class="uploader-content">
                <span class="close-btn" onclick="closeEditRole('EditVideoLink')">&times;</span>
                <h2>Edit Video Link</h2>
<!-- 
                <label for="updatedvideolink">Video Link</label> -->
                <input type="url" id="updatedvideolink" class="input-field" placeholder="Enter Video Link">

                <button class="btn btn-primary" onclick="UpdateVideoLink()">Update</button>
            </div>
        </div>
        

        <div id="EditLocation" class="uploader-overlay" style="display:none;">
            <div class="uploader-content">
                <span class="close-btn" onclick="closeEditLocation()">&times;</span>
                <h2>Edit Location</h2>

                <!-- <label for="updatedlocation">Location</label> -->
                <input type="text" id="updatedlocation" class="input-field" placeholder="Enter Location">

                <button class="btn btn-primary" onclick="UpdateLocation()">Update</button>
            </div>
        </div>



        <div id="Editbrgymodal" class="uploader-overlay" style="display:none;">
            <div class="uploader-content">
                <span class="close-btn" onclick="closeEditBrgy()">&times;</span>
                <h2>Edit Barangay</h2>

                <!-- <label for="updatedbrgy">Barangay</label> -->
                <input type="text" id="updatedbrgy" class="input-field" placeholder="Enter Barangay">

                <button class="btn btn-primary" onclick="UpdateBrgy()">Update</button>
            </div>
        </div>

        <!----------------------------- Modals Section END---------->


    </main>

    <div class="inspector-card" style="display:none;">
                    <div class="inspection-card">
                        <div class="inspection-header">
                            <h2>Status</h2>
                        </div>
                        <div class="inspection-body">
                            <p><strong>User:</strong> John Doe</p>
                            <p><strong>Status:</strong> Pending Inspection</p>
                            <button class="inspection-btn">Inspect Now</button>
                        </div>
                    </div>
                    <div class="inspection-card">
                        <div class="inspection-header">
                            <h2>Inspection Required</h2>
                        </div>
                        <div class="inspection-body">
                            <p><strong>User:</strong> John Doe</p>
                            <p><strong>Status:</strong> Pending Inspection</p>
                            <button class="inspection-btn">Inspect Now</button>
                        </div>
                    </div>
                    <div class="inspection-card">
                        <div class="inspection-header">
                            <h2>Inspection Required</h2>
                        </div>
                        <div class="inspection-body">
                            <p><strong>User:</strong> John Doe</p>
                            <p><strong>Status:</strong> Pending Inspection</p>
                            <button class="inspection-btn">Inspect Now</button>
                        </div>
                    </div>
                    <div class="inspection-card">
                        <div class="inspection-header">
                            <h2>Inspection Required</h2>
                        </div>
                        <div class="inspection-body">
                            <p><strong>User:</strong> John Doe</p>
                            <p><strong>Status:</strong> Pending Inspection</p>
                            <button class="inspection-btn">Inspect Now</button>
                        </div>
                    </div>
                    <div class="inspection-card">
                        <div class="inspection-header">
                            <h2>Inspection Required</h2>
                        </div>
                        <div class="inspection-body">
                            <p><strong>User:</strong> John Doe</p>
                            <p><strong>Status:</strong> Pending Inspection</p>
                            <button class="inspection-btn">Inspect Now</button>
                        </div>
                    </div>
                    <div class="inspection-card">
                        <div class="inspection-header">
                            <h2>Inspection Required</h2>
                        </div>
                        <div class="inspection-body">
                            <p><strong>User:</strong> John Doe</p>
                            <p><strong>Status:</strong> Pending Inspection</p>
                            <button class="inspection-btn">Inspect Now</button>
                        </div>
                    </div>
                    <div class="inspection-card">
                        <div class="inspection-header">
                            <h2>Inspection Required</h2>
                        </div>
                        <div class="inspection-body">
                            <p><strong>User:</strong> John Doe</p>
                            <p><strong>Status:</strong> Pending Inspection</p>
                            <button class="inspection-btn">Inspect Now</button>
                        </div>
                    </div>
                </div>

    <!--=============== MAIN JS ===============-->
    <script src="assets/js/main.js"></script>

</body>
<script src="../javascript/html5-qrcode.min1xxx.js"></script>
<script>
    // document.getElementById("trackerclicksection").click();
    // alert(document.getElementById("Tracker-section").textContent);
    // function toggleFilterProjects() {
    //     const dropdown = document.getElementById("filterDropdown");
    //     dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    // }

    function toggleFilterProjects() {
        const dropdown = document.getElementById("filterDropdown");
        const filterButton = document.getElementById("filterButton");

        // Toggle the dropdown visibility
        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
        } else {
            dropdown.style.display = "block";
        }

        // Close the dropdown if clicked outside of it
        const closeDropdown = function(event) {
            if (!dropdown.contains(event.target) && event.target !== filterButton) {
                dropdown.style.display = "none";
                // Remove the event listener after closing the dropdown
                document.removeEventListener('click', closeDropdown);
            }
        };

        // Add the event listener to close the dropdown if clicked outside
        document.addEventListener('click', closeDropdown);
    }


    function hideElements() {
        document.getElementById("title").style.display = "none";
        document.getElementById("customSelect").style.display = "none";
    }

    function showElements() {
        document.getElementById("title").style.display = "flex";
        document.getElementById("customSelect").style.display = "flex";
    }

    function focusInput() {
        document.getElementById("searchProject").focus();
    }

    function myProjectDetails(trackingNumber) {
        var trackyear = document.getElementById("yearSelect").value;

        var queryString = "?myprojectdetails=1&trackingyear=" + trackyear + "&trackingNumber=" + trackingNumber;
        var container = document.getElementById('mycardprojectresults');
        loader(container);
        ajaxGetAndConcatenate(queryString, processorLink, container, "myprojectdetails");

    }

    function ListofmyProjectDetails(trackingNumber) {
        var trackyear = document.getElementById("listprojectyearSelect").value;

        var queryString = "?ListofmyProjectDetails=1&trackingyear=" + trackyear + "&trackingNumber=" + trackingNumber;
        var container = document.getElementById('listofprojectresults');
        loader(container);
        ajaxGetAndConcatenate(queryString, processorLink, container, "ListofmyProjectDetails");

    }


    function showSelectedYear() {
        let employeeNumber = "<?php echo $_SESSION['employeeNumber']; ?>";
        let selectedYear = document.getElementById("yearSelect").value;

        var queryString = "?myprojectresults=1&trackingyear=" + selectedYear + "&employeeNumber=" + employeeNumber;
        var container = document.getElementById('mycardprojectresults');
        loader(container);
        ajaxGetAndConcatenate(queryString, processorLink, container, "myprojectresults");

    }

    function showSelectedYearListofProject() {
        let employeeNumber = "<?php echo $_SESSION['employeeNumber']; ?>";
        let selectedYear = document.getElementById("listprojectyearSelect").value;
        var container = document.getElementById('listofprojectresults');
        loader(container);
        var queryString = "?listofprojectresults=1&trackingyear=" + selectedYear + "&employeeNumber=" + employeeNumber;

        ajaxGetAndConcatenate(queryString, processorLink, container, "listofprojectresults", function() {
            // Ensure the filter applies after loading new results
            filterProjects();
        });
    }


    // function filterProjects() {
    //     let input = document.getElementById("searchProject").value.toLowerCase();
    //     let projects = document.querySelectorAll("#listofprojectresults .myproject-card");

    //     projects.forEach(project => {
    //         let text = project.textContent.toLowerCase();
    //         project.style.display = text.includes(input) ? "" : "none";
    //     });
    // }

    function filterProjects() {
        const dropdown = document.getElementById("filterDropdown");
        let input = document.getElementById("searchProject").value.toLowerCase();
        let projectType = document.getElementById("projecttype").value.toLowerCase();
        let projects = document.querySelectorAll("#filtercard ");

        projects.forEach(project => {
            let text = project.textContent.toLowerCase();

            // Check if the project text includes the search input and if the project type matches the selected option
            let matchesText = text.includes(input);
            let matchesType = projectType ? text.includes(projectType) : true;

            project.style.display = (matchesText && matchesType) ? "" : "none";
        });

        dropdown.style.display = "none";
    }

    function loader(container) {
        // const container = document.getElementById('searchcontainer');
        container.innerHTML = `
            <div class="loader-container">
                <i class="fa-solid fa-spinner"></i>
            </div>
        `;
    }
    function showSection(sectionId) {
        // Hide all sections
        const sections = document.querySelectorAll("main section");
        sections.forEach(section => {
            section.style.display = "none";
        });

        // Show the selected section
        document.getElementById(sectionId).style.display = "block";

        // If AutoReceive section is selected
        if (sectionId === "AutoReceive-section") {
            showScanningAnimation();

            let scannerBuffer = ""; 
            let scannerTimeout; 
            const inputField2 = document.getElementById("ok");

            document.addEventListener("keydown", (event) => {
                if (event.key === "Enter") {
                    if (document.activeElement === inputField2) {
                        receiveTracking(inputField2, scannerBuffer);
                    } else if (scannerBuffer) {
                        trackingNumberqr = formatTrackingNumber(scannerBuffer);
                        inputField2.value = trackingNumberqr; 

                        receiveTracking(inputField2, scannerBuffer);
                        scannerBuffer = "";
                    }
                } else if (event.key.length === 1 && !event.ctrlKey && !event.altKey) {
                    scannerBuffer += event.key;
                }
                scannerTimeout = setTimeout(() => {
                    scannerBuffer = "";
                }, 50); 
            });

            function receiveTracking(me, scannerBuffer) {
                var trackingNumber = me.value.toUpperCase();
                var officeCode = "<?php echo htmlspecialchars($officeCode); ?>";
                var trackingyear = scannerBuffer.substring(1, 2);
                var year = 2024 + (trackingyear.charCodeAt(0) - 'A'.charCodeAt(0));
                
                if (trackingNumber.length > 1) {
                    var queryString = "?receiveTrackingNumberQR=1&trackingNumber=" + trackingNumber + "&officeCode=" + officeCode + "&trackingyear=" + year;
                    var container = document.getElementById('resultsearchcontainer');
                    loader(container);
                    ajaxGetAndConcatenate(queryString, processorLink, container, "receiveTrackingNumberQR");
                    scanningAnimation.style.display = 'none';
                }
            }
        } else {
            hideScanningAnimation();
        }

        // If Tracker section is selected
        if (sectionId === "Tracker-section") {
            // Define revertTrackingButton only for Tracker-section
            window.revertTrackingButton = function(trackingNumber, sessionOffice, trackingYear) {
                if (trackingNumber.length > 1) {
                    var queryString = "?revertTrackingNumberQR=1&trackingNumber=" + trackingNumber + "&officeCode=" + sessionOffice + "&trackingyear=" + trackingYear;
                    var container = document.getElementById('searchcontainer');
                    loader(container);
                    ajaxGetAndConcatenate(queryString, processorLink, container, "revertTrackingNumberQR");
                }
            };

             window.receiveTrackingButton = function(trackingNumber, sessionOffice,trackingYear) {
                if (trackingNumber.length > 1) {
                var queryString = "?receiveTrackingNumberQR=1&trackingNumber=" + trackingNumber + "&officeCode=" + sessionOffice + "&trackingyear=" + trackingYear;
                var container = document.getElementById('searchcontainer');
                loader(container);
                ajaxGetAndConcatenate(queryString, processorLink, container, "receiveTrackingNumberQR");
            }
            }
        }

        // Check if the screen width is below 768px (mobile)
        if (window.innerWidth <= 768) {
            const toggleButton = document.getElementById("header-toggle");
            if (toggleButton) {
                toggleButton.click();
            }
        }


        if (sectionId === "myprojects-section") {
            showSelectedYear();
        }

        if (sectionId === "listofprojects-section") {
            showSelectedYearListofProject();
        }
    }



    function searchTracking() {
        var year = document.getElementById("yearInput").value;
        var trackingNumber = document.getElementById("trackingNumberInput").value;
        var officeCode = "<?php echo htmlspecialchars($officeCode); ?>";

        if (year && trackingNumber) {
            var queryString = "?searchTrackingNumber=1&trackingNumber=" + trackingNumber + "&officeCode=" + officeCode + "&trackingyear=" + year;
            var container = document.getElementById('searchcontainer');
            loader(container);
            ajaxGetAndConcatenate(queryString, processorLink, container, "searchTrackingNumberQR");
        } else if (!year && !trackingNumber) {
            alert("Please select a year and enter a tracking number.");
        } else if (!year) {
            alert("Please select a year.");
        } else if (!trackingNumber) {
            alert("Please enter a tracking number.");
        }
    }


    function checkEnter(event) {
        if (event.key === "Enter") {
            searchTracking(); 
        }
    }


   function logout(){
      var queryString = "?logout=1";
      var container = '';
      ajaxGetAndConcatenate(queryString,processorLink,container,"Logout");
   }

    // Show the scanning animation
    function showScanningAnimation() {
        document.getElementById('scanningAnimation').style.display = 'inline-block';
    }

    // Hide the scanning animation
    function hideScanningAnimation() {
        document.getElementById('scanningAnimation').style.display = 'none';
    }


    function formatTrackingNumber(decodedText) {
            const code = decodedText.substring(3, 7);
            const series = decodedText.substring(7);
            const trackType = decodedText.substring(2, 3);
            
            // alert(trackingyear);
            // const year = 2024 + (trackingyear.charCodeAt(0) - 'A'.charCodeAt(0));

            return trackType === 'Y' ? `PR-${code}-${series}` : `${code}-${series}`;
        }




        
        //------- CAMERA SCANNER ------- START///

        const qrReader = document.getElementById('qr-reader');
        const startScanner = document.getElementById('startScanner');
        const inputField = document.getElementById('ok');
        const scanningAnimation = document.getElementById('scanning-bar');


        let html5QrCode;
        let isCameraActive = false; // Track if the camera is active

        // Function to stop the QR code scanner
        async function stopScanner() {
            try {
                if (html5QrCode) {
                    await html5QrCode.stop();
                    html5QrCode = null; // Reset the QR code reader instance
                    isCameraActive = false; // Set camera status to inactive
                    startScanner.classList.remove('fa-circle-xmark');
                    startScanner.classList.add('fa-camera');
                    scannerStatus.textContent = 'Click to Open Camera';
                    qrReader.style.display = 'none'; // Hide the QR code reader
                    scanningAnimation.style.display = 'none'; // Hide scanning animation
                }
            } catch (err) {
                console.warn("Error stopping QR Code scanner:", err);
            }
        }

        startScanner.addEventListener('click', () => {
            if (isCameraActive) {
                // Stop the camera if it's already active
                stopScanner();
            } else {
                // Show QR code reader and scanning animation
                qrReader.style.display = 'block';
                scanningAnimation.style.display = 'block';

                // Initialize QR code scanner
                html5QrCode = new Html5Qrcode("qr-reader");

                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 30, qrbox: { width: 1000, height: 1000 }, disableFlip: false },
                    (decodedText) => {
                        var trackingNumber = formatTrackingNumber(decodedText);
                        inputField.value = trackingNumber;
                        receiveTracking(inputField, decodedText);
                        stopScanner();
                    },
                    (errorMessage) => console.warn(`QR Code scan error: ${errorMessage}`)
                ).catch(err => console.error(`Error starting QR Code scanner: ${err}`));

                // Update the button text to "Stop Camera"
                startScanner.classList.remove('fa-camera'); // Remove camera icon
                startScanner.classList.remove('fa-bounce');
                startScanner.classList.add('fa-circle-xmark'); // Add stop icon
                scannerStatus.textContent = 'Click to Stop Scanning'; // Update status text
                isCameraActive = true; // Set camera status to active
            }
        });
        //------- CAMERA SCANNER ------- END///





    // Function to open the uploader
    function openInfrapddUploader(trackingNumber,trackingyear) {
        document.getElementById("infrapddUploader").style.display = "block";
    }

    // Function to close the uploader
    function closeInfrapddUploader(trackingNumber) {
        document.getElementById("infrapddUploader").style.display = "none";
    }

    // Function to open the uploader
    function openInfrapddUploaderVideo(trackingNumber,trackingyear) {
        document.getElementById("infrapddUploadervideo").style.display = "block";
    }

    // Function to close the uploader
    function closeInfrapddUploaderVideo(trackingNumber) {
        document.getElementById("infrapddUploadervideo").style.display = "none";
    }


    function openEditDateVisited(trackingNumber,trackingyear) {
        document.getElementById("EditDateVisited").style.display = "block";
    }


    function closeEditDateVisited(trackingNumber) {
        document.getElementById("EditDateVisited").style.display = "none";
    }

    function openEditVideoLink(trackingNumber,trackingyear) {
        document.getElementById("EditVideoLink").style.display = "block";
    }


    function openEditLocation(trackingNumber,trackingyear) {
        document.getElementById("EditLocation").style.display = "block";
    }


    function closeEditLocation(trackingNumber) {
        document.getElementById("EditLocation").style.display = "none";
    }

    function openEditBrgy(trackingNumber,trackingyear) {
        document.getElementById("Editbrgymodal").style.display = "block";
    }


    function closeEditBrgy(trackingNumber) {
        document.getElementById("Editbrgymodal").style.display = "none";
    }

    function closeEditRole(modalID) {
        document.getElementById(modalID).style.display = "none";
    }

    function openEditStatus(trackingNumber, trackingYear, status,container) {
        window.selectedContainer = container;
        // alert(window.selectedContainer);
        document.getElementById("EditStatusModal").style.display = "block";

        let dropdown = document.getElementById("updatedstatus");
        dropdown.value = status;
    }

    function closeEditStatus(trackingNumber,trackingyear,status) {
        document.getElementById("EditStatusModal").style.display = "none";
    }

    function UpdateStatus() {
        var tnNum = document.getElementById('tracknumid').textContent.replace(/\s/g, '');
        var yearNumElement = document.getElementById('yearid'); 
        var year = yearNumElement ? yearNumElement.innerText.trim() : ""; 
        let selectedStatus = document.getElementById("updatedstatus").value;

        // container = document.getElementById('searchcontainer');
        var container = window.selectedContainer ;
        var containername = container.id;
        loader(container);
        var queryString = "?UpdateStatus=1&trackingNumber=" + tnNum + "&trackingyear=" + year + "&status=" + selectedStatus + "&container=" + containername;
        ajaxGetAndConcatenate(queryString,processorLink,container,"UpdateStatus");

    }


    function displayFileNames() {
        const input = document.getElementById('infraUpFilePre');
        const fileNamesContainer = document.getElementById('fileNames');

        // Clear previous file names
        fileNamesContainer.innerHTML = '';

        if (input.files.length > 0) {
            const fileList = document.createElement('ul');
            fileList.style.listStyleType = 'none'; // Removes default bullet points

            for (let i = 0; i < input.files.length; i++) {
                const fileItem = document.createElement('li');
                fileItem.textContent = input.files[i].name;
                fileList.appendChild(fileItem);
            }

            fileNamesContainer.appendChild(fileList);
        }
    }

    function saveInfraUploadPre() { 
        var tnNum = document.getElementById('tracknumid').textContent.replace(/\s/g, '');
        // var tnNum = tnNumElement ? tnNumElement.innerText.trim() : ""; 
        var pictures = document.getElementById('infraUpFilePre').files;
        var yearNumElement = document.getElementById('yearid'); 
        var year = yearNumElement ? yearNumElement.innerText.trim() : ""; 
        var predate = document.getElementById('infraVisitDatePre').value.trim();
        var err = 0;
        var video = document.getElementById('infraVideoLinkPre').value.trim();

        if (tnNum === "") {
            alert("No TrackingNum found"); // Alert if no tracking number
            return; // Stop execution if no tracking number
        }

        if(pictures.length > 0) {
			for(var i = 0; i < pictures.length; i++) {
				err = fileCheckJS(pictures[i], "jpeg,jpg,png");
			}
			if(err > 0){
				err = 1;
			}
		}
		
		if(video.length > 0){
			var vidLink = '';
			var arr = video.split(",");
			if(arr.length == 1){
				let result = video.indexOf("https://youtu.be/");
				if(result == -1){
					err = 2;
					vidLink = video;
				}
			}else if(arr.length > 1){
				var dup = checkDuplicate(arr);
				if(dup == 1){
					err = 3;
				}
				for(var i = 0 ; i < arr.length; i++){
					let result = arr[i].indexOf("https://youtu.be/");
					if(result == -1){
						vidLink = arr[i];
						err = 2;
						break;
					}
				}
			}
		}
				
		if(predate == ''){
			err = 6;
		}
		
		if(video.length  == 0 & pictures.length == 0  ){
			err = 5;
		}

		// if(tnNum == null){
		// 	err = 4;
		// }else{
		// 	var tn = tnNum.textContent.trim();
		// }

        if (err === 0) {
            var container = document.getElementById("searchcontainer");
            var formData = new FormData();

            formData.append("saveInfraUploadPre", 1);
            formData.append("tn", tnNum);  // ✅ Fixed: tnNum instead of undefined tn
            formData.append("year", year);
            formData.append("predate", predate);
            formData.append("video", video);
            formData.append("pics", pictures);

            // ✅ Removed incorrect `formData.append("pics", pictures);`
            for (var i = 0; i < pictures.length; i++) {
                formData.append("pics[]", pictures[i]);
            }

            // ✅ Debug: Log all FormData key-value pairs
            console.log("FormData Entries:");
            for (var pair of formData.entries()) {
                console.log(pair[0] + ": ", pair[1]);
            }

            // ✅ Debug: Show values in alert box
            let formDataContent = "";
            formData.forEach((value, key) => {
                formDataContent += `${key}: ${value.name || value}\n`;
            });
            document.getElementById("infrapddUploader").style.display = "none";
            loader(container);
            ajaxFormUpload1(formData, uploadLink, "saveInfraUploadPre", container);
        } else {
            document.getElementById("infrapddUploader").style.display = "none";
            if(err == 1){
				alert("Invalid photo file format.");	
			}else if(err == 2){
				alert("Invalid video link : " + vidLink);	
			}else if(err == 3){
				alert("Duplicate video link.");	
			}else if(err == 4){
				alert("Please search transaction first.");	
			}else if(err == 5){
				alert("Please input something.");	
			}else if(err == 6){
				alert("Please enter a valid Date.");
			}
        }
    }

    function UpdatePreConDateVisit(){
        var tnNum = document.getElementById('tracknumid').textContent.replace(/\s/g, '');
        var yearNumElement = document.getElementById('yearid'); 
        var year = yearNumElement ? yearNumElement.innerText.trim() : ""; 
        var datevisit = document.getElementById('editdatevisit').value;

        container = document.getElementById('searchcontainer');
        loader(container);
        var queryString = "?UpdatePreConDateVisit=1&trackingNumber=" + tnNum + "&trackingyear=" + year + "&datevisit=" + datevisit;
        ajaxGetAndConcatenate(queryString,processorLink,container,"UpdatePreConDateVisit");
    }

    function UpdateLocation(){
        var tnNum = document.getElementById('tracknumid').textContent.replace(/\s/g, '');
        var yearNumElement = document.getElementById('yearid'); 
        var year = yearNumElement ? yearNumElement.innerText.trim() : ""; 
        var location = document.getElementById('updatedlocation').value;

        container = document.getElementById('searchcontainer');
        loader(container);
        var queryString = "?UpdateLocation=1&trackingNumber=" + tnNum + "&trackingyear=" + year + "&location=" + location;
        ajaxGetAndConcatenate(queryString,processorLink,container,"UpdateLocation");
    }

    function UpdateVideoLink(){
        var tnNum = document.getElementById('tracknumid').textContent.replace(/\s/g, '');
        var yearNumElement = document.getElementById('yearid'); 
        var year = yearNumElement ? yearNumElement.innerText.trim() : ""; 
        var videolink = document.getElementById('updatedvideolink').value;

        container = document.getElementById('searchcontainer');
        loader(container);
        var queryString = "?Updatevideolink=1&trackingNumber=" + tnNum + "&trackingyear=" + year + "&videolink=" + videolink;
        ajaxGetAndConcatenate(queryString,processorLink,container,"Updatevideolink");
    }

    function UpdateBrgy(){
        var tnNum = document.getElementById('tracknumid').textContent.replace(/\s/g, '');
        var yearNumElement = document.getElementById('yearid'); 
        var year = yearNumElement ? yearNumElement.innerText.trim() : ""; 
        var brgy = document.getElementById('updatedbrgy').value;

        container = document.getElementById('searchcontainer');
        loader(container);
        var queryString = "?UpdateBrgy=1&trackingNumber=" + tnNum + "&trackingyear=" + year + "&brgy=" + brgy;
        ajaxGetAndConcatenate(queryString,processorLink,container,"UpdateBrgy");
    }

    function EditRole(trackingNumber,trackingyear,role,employeeNum,container){

        // alert(container)
        // container = document.getElementById('searchcontainer');
        var queryString = "?editprogrammer=1&trackingNumber=" + trackingNumber + "&trackingyear=" + trackingyear + "&role=" + role + "&employeeNumber=" + employeeNum + "&container=" + container.id;
        ajaxGetAndConcatenate(queryString,processorLink,container,"editprogrammer");

     
    }


    function assignProgrammers(trackingNumber, trackingyear, role,employeenum,fullname,namecontainer) {
        var selectedProgrammers = [];
        var allProgrammers = [];
        var checkboxes = document.querySelectorAll("#programmerList input[type='checkbox']");
        
        var containername = namecontainer;
        // alert(namecontainer);
        checkboxes.forEach((checkbox) => {
            var employeeNum = checkbox.getAttribute("data-empnum");
            var fullName = checkbox.value;
            var programmerData = encodeURIComponent(fullName) + "~" + encodeURIComponent(employeeNum);

            allProgrammers.push(programmerData); // All programmers (checked or unchecked)

            if (checkbox.checked) {
                selectedProgrammers.push(programmerData); // Only checked programmers
            }
        });

        var queryString = "?assignprogrammer=1" +
                        "&trackingNumber=" + encodeURIComponent(trackingNumber) +
                        "&trackingyear=" + encodeURIComponent(trackingyear) +
                        "&role=" + encodeURIComponent(role) +
                        "&selectedProgrammers=" + selectedProgrammers.join("|") +
                        "&allProgrammers=" + allProgrammers.join("|");

        var container = document.getElementById(containername);
        loader(container);
        ajaxGetAndConcatenate(queryString, processorLink, container, "assignprogrammer");
    }





 // container = document.getElementById('searchcontainer');
            // var queryString = "?assignprogrammer=1&trackingNumber=" + trackingNumber + "&trackingyear=" + trackingyear + "&role=" + role + "&employeeNumber=" + employeeNum;
            // ajaxGetAndConcatenate(queryString,processorLink,container,"assignprogrammer");

    document.addEventListener("DOMContentLoaded", function () {
        var currentYear = new Date().getFullYear(); // Get current year
        var yearDropdown = document.getElementById("yearInput");

        if (yearDropdown) {
            yearDropdown.value = currentYear; // Set default selected value
        }
    });


    
    function openModalImage(src) {
        const modal = document.getElementById("imageModal");
        const fullImage = document.getElementById("fullImage");
        fullImage.src = src;
        modal.style.display = "flex"; 
    }

    function closeModalImage() {
        document.getElementById("imageModal").style.display = "none";
    }

    


</script>
</html>
