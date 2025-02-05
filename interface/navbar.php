<!-- navbar.php -->
 <?php
if (!isset($_SESSION)) {
    session_start();
}

defined('ROOTER') ? NULL : define("ROOTER", "../");
require_once(ROOTER . 'javascript/ajaxFunction.php');
require_once(ROOTER . 'includes/database.php');

	if(!isset($_SESSION['employeeNumber'])){
		$link = "<script>window.open('../../dtr/interface/login.php','_self')</script>";
		echo $link;
	}

	// Check privilege and redirect if privilege is 6 and 7 go to project cleansing
	if (isset($_SESSION['privy'] ) && ($_SESSION['privy'] == '6' )) {

		$link = "<script> window.open('../../inv/interface/main.php', '_self')</script>";
		echo $link;
	}

    // echo($_SESSION['officeCode']);
    $officeCode = $_SESSION['officeCode'];
    // echo $officeCode;
?>
<title>DT Receiver</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Oxygen' rel='stylesheet'>

<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap");
    @font-face {
            font-family: "Oswald";
            src: url("../fonts/Oswald-ExtraLight.ttf");
        }
    /* Navbar Styles */



    .navbar {
        background-color: #0e2a47;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 200px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        /* font-family:'Oxygen'; */
        font-family: "Poppins", sans-serif;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        font-size: 1.1em;
        margin: 0 15px;
        transition: color 0.3s;
    }

    .navbar a:hover {
        color: #1c7ed6;
    }

    .menu-icon {
        display: none;
        font-size: 20px;
    }

        /* Sidebar Styles */
    .sidebar {
        height: 100%;
        width: 250px;
        background-color: #001f3f;
        color: white;
        position: fixed;
        top: 0;
        left: -250px;
        transition: 0.3s;
        padding-top: 60px;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        font-family: 'Oxygen';
    }

    .sidebar a {
        padding: 10px 15px;
        text-decoration: none;
        font-size: 1.2em;
        color: white;
        display: block;
        transition: background 0.3s;
    }

    .sidebar a:hover {
        background-color: #00509E;
    }

    .sidebar .close-btn {
        position: absolute;
        top: 20px;
        right: 25px;
        font-size: 36px;
        color: white;
        cursor: pointer;
    }


    .trackertd{
        width:20px;
        padding-right:3px;
        text-align:right;
        padding:4px 10px 0px 10px;
    }

    .trackertd small{
        display:inline-block;
    }
    .trackerlabel{
        /* font-weight: 100; */
        text-align:left;
        width: 140px;
        font-size:22px;
        /* border:1px solid red; */
        
    }

    .trackingspecs{
        font-weight: bold;
        border-bottom:1px solid silver;
        font-size:22px;
        /* padding-top:8px; */
        width:385px;
        
    }

    .trackerheader{
        background-color: #00bfff;
        padding: 15px 15px 15px 20px; 
        border-radius: 3px 3px 3px 3px;
        text-align:left;
        font-family:Oswald;
    }

    .trackerheader span{
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);  
        font-size: 27px; 
        letter-spacing: 1px; 
        font-weight: bold; 
        color: white;
    }

    .trackerheader p{
        margin: 5px 0 0; 
        font-size: 1.2em;
        color: white;
    }


    .receivebutton {
        background-color: rgb(230, 237, 241);
        color: black;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); 
        transition: all 0.3s ease;
        text-align: center;
    }

    .receivebutton:hover {
        background-color: rgb(200, 210, 220); /* A darker silver/gray tone */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.25); 
        transform: translateY(-2px); 
    }

    .receivebutton:active {
        background-color: rgb(180, 190, 200); /* A more subtle dark shade */
        transform: translateY(1px);
        box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2); 
    }


    .loader-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height:100%;
        text-align: center;
        background-repeat:no-repeat;
        z-index:100;
        background-color:rgba(252, 254, 254,.4);
    }

    .loader-container i {
        position:relative;
        top: 50%;
        /* left: 50%; */
        font-size: 30px;
        color: #00bfff;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }


   

    @media screen and (max-width: 1160px) {
    .navbar {
        padding: 15px 100px;
    }

    }
    @media screen and (max-width: 950px) {
    .navbar {
        padding: 15px 50px;
    }

    }


    /* Mobile Styles */
    @media (max-width: 768px) {
        .navbar .menu-icon {
            display: block;
        }

        .navbar-links {
            display: none;
        }

        .navbar {
            padding: 15px 20px;
        }


        .sidebar {
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0;
            left: -100%;
            transition: left 0.3s;
        }

        .sidebar.open {
            left: 0;
        }

        .card {
                flex: 1 1 100%; /* Take full width on small screens */
                padding: 5px;
                width: auto;
        }
 
        .trackerheader{
            padding: 15px 15px 15px 12px; 
        }

        .trackertd{
           /* padding-right:5px; */
           width: 2px;
           text-align:left;
        }
        .trackingspecs{
            width:100%;
            /* white-space: nowrap; */
            text-align:left;
            /* border:1px solid red; */
        }
        .trackerlabel{
          /* border:1px solid red; */
          width: auto;
          white-space: nowrap;
          padding-right:20px;
        }

        .receivebutton {
            margin-bottom:20px;
        }
        

    }

    @media (max-width: 1024px) {
        .card {
            flex: 1 1 calc(50% - 20px); /* Take 1/2 of the row on medium screens */
        }
    }


    a{
        cursor:pointer;
    }

    table {
        font-family: 'Oswald';
    }

    .navbar-title{
        font-family: "Poppins", sans-serif; 
        font-weight:500;
        text-decoration: none;
        /* font-size:22px; */
    }

    
</style>

<div class="navbar">
    <div>
        <a class="navbar-title" onclick="gotohome()">DT Receiver</a>
    </div>
    <div class="menu-icon" onclick="toggleSidebar()">
        &#9776;
    </div>
    <div class="navbar-links">
        <a onclick="gotohome()">Home</a>
        <a onclick="gototracker()">Tracker</a>
        <a onclick="gotoreceive()">QR Auto Receive</a>
        <a onclick="logout()" style="cursor:pointer;">Log Out</a>
    </div>

    <!-- Sidebar -->
        <div class="sidebar" id="sidebar" >
        <span class="close-btn" onclick="closeSidebar()">&times;</span>
        <a onclick="gotohome()">Home</a>
        <a onclick="gototracker()">Tracker</a>
        <a onclick="gotoreceive()">QR Auto Receive</a>
        <a onclick="logout()">Log Out</a>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("open");
    }

    // Close Sidebar
    function closeSidebar() {
        document.getElementById("sidebar").classList.remove("open");
    }

    function logout(){
        var queryString = "?logout=1";
        var container = '';
        ajaxGetAndConcatenate(queryString,processorLink,container,"Logout");
    }

    function gotoreceive(){
        window.open('../../dtr/interface/receiver.php', '_self');
    }

    function gototracker(){
        window.open('../../dtr/interface/tracker.php', '_self');
    }

    function gotohome(){
        window.open('../../dtr/interface/main.php', '_self');
    }


    // function loader() {
	// 	const container = document.getElementById('resultsearchcontainer');
	// 	container.innerHTML = `
	// 		<div class="loader-container">
	// 			<div class="loader-bar"></div>
	// 			<div class="loader-percentage">0%</div>
	// 		</div>`;

	// 	const bar = container.querySelector('.loader-bar');
	// 	const percentage = container.querySelector('.loader-percentage');

	// 	let progress = 0;
	// 	const interval = setInterval(() => {
	// 		progress += 1;
	// 		bar.style.width = progress + '%';
	// 		percentage.textContent = progress + '%';

	// 		if (progress >= 100) {
	// 			clearInterval(interval);
	// 		}
	// 	}, 30);
	// }

    function loader() {
    const container = document.getElementById('resultsearchcontainer');
    container.innerHTML = `
        <div class="loader-container">
            <i class="fa-solid fa-spinner"></i>
        </div>
    `;
}




    function revertTrackingButton(trackingNumber, sessionOffice,trackingYear) {
        // alert(trackingNumber);
            // var trackingNumber = me.value.toUpperCase();
            // var officeCode = "";
            // var trackingyear = scannerBuffer.substring(1, 2);
            // var year = 2024 + (trackingyear.charCodeAt(0) - 'A'.charCodeAt(0));
            
            if (trackingNumber.length > 1) {
                var queryString = "?revertTrackingNumberQR=1&trackingNumber=" + trackingNumber + "&officeCode=" + sessionOffice + "&trackingyear=" + trackingYear;
                var container = document.getElementById('resultsearchcontainer');
                loader();
                ajaxGetAndConcatenate(queryString, processorLink, container, "revertTrackingNumberQR");
            }
    }

    function receiveTrackingButton(trackingNumber, sessionOffice,trackingYear) {
        // alert(trackingNumber);
            // var trackingNumber = me.value.toUpperCase();
            // var officeCode = "";
            // var trackingyear = scannerBuffer.substring(1, 2);
            // var year = 2024 + (trackingyear.charCodeAt(0) - 'A'.charCodeAt(0));
            
            if (trackingNumber.length > 1) {
                var queryString = "?receiveTrackingNumberQR=1&trackingNumber=" + trackingNumber + "&officeCode=" + sessionOffice + "&trackingyear=" + trackingYear;
                var container = document.getElementById('resultsearchcontainer');
                loader();
                ajaxGetAndConcatenate(queryString, processorLink, container, "receiveTrackingNumberQR");
            }
    }
    

    window.addEventListener('load', () => {
  setTimeout(() => {
    window.scrollTo(0, 1);
  }, 0);
});



</script>
