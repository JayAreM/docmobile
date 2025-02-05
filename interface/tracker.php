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
<?php include('navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<!-- <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* General Styles */

       

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            /* background-color: #fff; */
            color: #333;
        }

    
        /* Content Container */
        .content-container {
            padding: 40px;
            text-align: center;
            margin-left: 250px; /* Adjust to allow space for sidebar */
            transition: margin-left 0.3s;
        }

        .content-container h1 {
            font-size: 2.5em;
        }

        .content-container p {
            font-size: 1.2em;
        }

        .search-container {
            position: relative; /* Position it relative to its nearest positioned ancestor */
            top: 20px; /* Position it vertically in the center */
            left: 40%; /* Position it horizontally in the center */
            transform: translate(-50%, -50%); /* Offset by 50% of its own width and height to center it */
            max-width: 500px;
            width: 100%; /* Allow it to take up the available space */
            gap: 10px; /* Space between inputs */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .search-input {
            padding: 12px 15px;
            width: 100%;
            border: 2px solid #001f3f;
            border-radius: 30px;
            font-size: 1em;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .search-input:focus {
            width: 95%;
            border-color: #1c7ed6;
            box-shadow: 0 0 5px rgba(28, 126, 214, 0.4);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #aaa;
            cursor: pointer;
        }

        /* Modern Year Input Styling */
        .year-container {
            position: relative;
        }

        .year-input {
            appearance: none; /* Removes default styles */
            -webkit-appearance: none; /* For Safari */
            padding: 10px 15px;
            width: 120px; /* Minimal width */
            font-size: 1em;
            font-family: 'Roboto', sans-serif;
            color: #333;
            background-color: #f9f9f9;
            border: 2px solid #001f3f;
            border-radius: 30px;
            outline: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .year-input:focus,
        .year-input:hover {
            border-color: #1c7ed6;
            box-shadow: 0 0 8px rgba(28, 126, 214, 0.4);
        }

        /* Adding a Dropdown Icon for Modern Look */
        .year-container::after {
            content: "▼";
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.9em;
            color: #333;
            pointer-events: none; /* Prevent interaction with the icon */
        }
        #resultsearchcontainer{
            left:15%;
            margin-top:20px;
            width:50%;
        }

        .card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            flex: 1 1 calc(33.333% - 20px); 
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            min-width: 250px; /
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .card-icon {
            font-size: 2em;
            color: #1c7ed6;
            margin-bottom: 10px;
        }

        .card-content h2 {
            font-size: 1.5em;
            margin: 10px 0;
        }

        .card-content p {
            color: #777;
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



        

        /* Mobile Styles */
        @media (max-width: 768px) {
            .year-input {
                width: 100px; /* Slightly smaller for mobile screens */
            }
            .search-container{
                width:90%;
                left:50%;
            }

            .navbar {
                justify-content: space-between;
            }

            .navbar .menu-icon {
                display: block;
                font-size: 30px;
            }

            /* Hide navbar links in mobile */
            .navbar-links {
                display: none; 
            }

            .content-container {
                margin-left: 0; /* No space for sidebar on mobile */
                padding: 20px;
            }


            #resultsearchcontainer{
                width:100%;
                left:0;
            }

            .card {
                flex: 1 1 100%; /* Take full width on small screens */
                padding: 5px;
                width: auto;
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

        /* .loader-container {
            position: relative;
            width: 100%;
            max-width: 300px;
            height: 30px;
            background: #f0f0f0;
            border-radius: 15px;
            overflow: hidden;
            margin: 20px auto;
        }

        .loader-bar {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: #000000;
            width: 0;
            transition: width 0.1s linear;
        }

        .loader-percentage {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            font-weight: bold;
            color: #ffffff;
            line-height: 30px;
        } */

        table {
        font-family: 'Oswald';

        }


    </style>
</head>
<body>

    <!-- Navbar -->
    <!-- <div class="navbar">
        <div>
            <a href="#">DT Receiver</a>
        </div>
        <div class="menu-icon" onclick="toggleSidebar()">
            &#9776;
        </div>
        <div class="navbar-links">
            <a onclick="gotohome()">Home</a>
            <a href="#">Tracker</a>
            <a onclick="autoreceive()">QR Auto Receive</a>
            <a onclick="logout()" style="cursor:pointer;">Log Out</a>
        </div>
    </div> -->

    <!-- Sidebar
    <div class="sidebar" id="sidebar">
        <span class="close-btn" onclick="closeSidebar()">&times;</span>
        <a onclick="gotohome()">Home</a>
        <a href="#">Tracker</a>
        <a onclick="autoreceive()">QR Auto Receive</a>
        <a onclick="logout()">Log Out</a>
    </div> -->

    <!-- Content -->
    <div class="content-container">
        <div class="search-container">
            <!-- Modern Year Input -->
            <div class="year-container">
                <select class="year-input" id="yearInput">
                    <option value="" disabled selected>Year</option>
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                    <option value="2022">2022</option>
                    <!-- Add more years as needed -->
                </select>
            </div>

            <!-- Tracking Number Input -->
            <input type="text" class="search-input" style="text-align:center;" id="trackingNumberInput" placeholder="Tracking Number" onkeypress="checkEnter(event)">

            <!-- Search Button -->
            <span class="search-icon" onclick="searchTracking()">&#128269;</span>
        </div>

        <div id="resultsearchcontainer" style="position:relative;">
        
        </div>


    </div>
</body>
</html>


<script>

    function searchTracking() {
        var year = document.getElementById("yearInput").value;
        var trackingNumber = document.getElementById("trackingNumberInput").value;
        var officeCode = "<?php echo htmlspecialchars($officeCode); ?>";

        if (year && trackingNumber) {
            var queryString = "?searchTrackingNumber=1&trackingNumber=" + trackingNumber + "&officeCode=" + officeCode + "&trackingyear=" + year;
			var container = document.getElementById('resultsearchcontainer');
            loader();
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

        
</script>
