<?php
if (!isset($_SESSION)) {
    session_start();
}

defined('ROOTER') ? NULL : define("ROOTER", "../");
require_once(ROOTER . 'javascript/ajaxFunction.php');
require_once(ROOTER . 'includes/database.php');

	if(!isset($_SESSION['employeeNumber'])){
		$link = "<script>window.open('../../autoreceiver/interface/login.php','_self')</script>";
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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Template</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>

        @font-face {
            font-family: "Oswald";
            src: url("../fonts/Oswald-ExtraLight.ttf");
        }
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            /* background-color: #fff; */
            color: #333;
            
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

      
        /* Content Container */
        .content-container {
            margin-top:30px;
            padding: 20px;
            text-align: center;
        }

        .search-container {
            position: relative;
            margin: 20px auto;
            max-width: 500px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .search-input {
            padding: 12px 15px;
            width: 100%;
            border: 2px solid #001f3f;
            border-radius: 30px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .search-input:focus {
            border-color: #1c7ed6;
            box-shadow: 0 0 5px rgba(28, 126, 214, 0.4);
        }

        .scananime {
            margin-top: 20px;
            text-align: center;
        }

        .scanning-bar {
            width: 100px;
            height: 5px;
            background-color: #00bfff;
            margin: 10px auto;
            position: relative;
            overflow: hidden;
        }

        .scanner-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 20%;
            height: 100%;
            background-color: #00ff00;
            animation: scan 2s infinite;
        }

        #qr-reader{
            display:none;
            position:relative;
            width:30%;
            left:35%;
        }

        #resultsearchcontainer{
            position:relative;
            left:25%;
            margin-top:20px;
            width:50%;
        }
        
        a{
            cursor:pointer;
        }

        #startScanner{
        display:none
        }

        #scannerStatus{
            display:none
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            /* .navbar {
                flex-wrap: wrap;
                padding: 10px;
            }

            .navbar .menu-icon {
                display: block;
            }

            .navbar-links {
                display: none;
                flex-direction: column;
                gap: 0;
            }

            .navbar-links.show {
                display: flex;
            }

            .sidebar {
                width: 100%;
                left: -100%;
            }

            .sidebar.open {
                left: 0;
            } */

            .content-container {
                padding: 10px;
            }

            #qr-reader{
                width: auto;
                left:0;
            }
            #resultsearchcontainer{
                width:100%;
                left:0;
            }

            #startScanner{
                display:block;
            }

            #scannerStatus{
                display:block;
            }
            .scanning-bar{
                display:none;
            }

            .hideinmobile{
                display:none;
            }

        }



    /* Scanning line animation */
    @keyframes scan {
        0% {
            left: -20%;
        }
        50% {
            left: 100%;
        }
        100% {
            left: -20%;
        }
    }



    </style>
</head>
<body>
    <input type="text" id="ok" class="search-input" placeholder="Scan a QR" disabled style="display:none;font-family: 'Arial', sans-serif; padding: 15px; width: 100%; border-radius: 10px; border: 1px solid #ddd; font-size: 16px; box-sizing: border-box; outline: none; color: #333; background-color: #fff; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

    <div class="content-container">
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
</body>



    <script src="../javascript/html5-qrcode.min1xxx.js"></script>
    <script>
    

        function logout(){
            var queryString = "?logout=1";
            var container = '';
            ajaxGetAndConcatenate(queryString,processorLink,container,"Logout");
        }


        showScanningAnimation();
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


        function receiveTracking(me, scannerBuffer) {
            var trackingNumber = me.value.toUpperCase();
            var officeCode = "<?php echo htmlspecialchars($officeCode); ?>";
            var trackingyear = scannerBuffer.substring(1, 2);
            var year = 2024 + (trackingyear.charCodeAt(0) - 'A'.charCodeAt(0));
            
            if (trackingNumber.length > 1) {
                loader();
                var queryString = "?receiveTrackingNumberQR=1&trackingNumber=" + trackingNumber + "&officeCode=" + officeCode + "&trackingyear=" + year;
                var container = document.getElementById('resultsearchcontainer');
                ajaxGetAndConcatenate(queryString, processorLink, container, "receiveTrackingNumberQR");
                scanningAnimation.style.display = 'none';
            }
        }


        //----- SCANNER DEVICE --- START//

        let scannerBuffer = ""; 
        let scannerTimeout; 
        const inputField2 = document.getElementById("ok");
        document.addEventListener("keydown", (event) => {
            if (event.key === "Enter") {
                if (document.activeElement === inputField2) {
                    receiveTracking(inputField2,scannerBuffer);
                } else if (scannerBuffer) {
                    trackingNumberqr = formatTrackingNumber(scannerBuffer);
                    inputField2.value = trackingNumberqr; 

                    receiveTracking(inputField2,scannerBuffer);

                    scannerBuffer = "";
                }
            } else if (event.key.length === 1 && !event.ctrlKey && !event.altKey) {

                scannerBuffer += event.key;
            }
            scannerTimeout = setTimeout(() => {
                scannerBuffer = "";
            }, 50); 
        });
        
        //---SCANNER DEVICE ---- END//
        
        

    </script>
</html>
