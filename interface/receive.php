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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Auto Receive</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&display=swap');
        
        body {
            font-family: 'Source Sans Pro', sans-serif;
            /* background: #f4f4f9; */
            background-image: linear-gradient(to left,white,#f4f4f9);
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            overflow: hidden;
        }

        @font-face {
            font-family: oswald;
            src: url(../fonts/Oswald-ExtraLight.ttf);
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 15px;
            box-sizing: border-box;
        }

        .search-container {
            margin-top: 50px;
            text-align: center;
            padding-left: 30px;
            padding-right: 30px;
            padding-top: 10px;
        }

        .search-container h1 {
            font-size: 26px;
            color: #333333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .search-input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #d6d6d6;
            border-radius: 5px;
            outline: none;
            background-color: #f9f9f9;
            color: #333;
            transition: border-color 0.3s ease;
            margin-bottom: 20px;
        }

        .search-input:focus {
            border-color: #333333;
        }

        #ResultContainer {
            display: none;
            text-align: center;
            width: auto;
            margin-top: 10px;
        }

        .loader-container {
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
        }

        .headerL1 {
            height: 200px;
            position: fixed;
            z-index: 9999;
            top: 10;
            left: 20;
        }

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

        .scanning-bar {
            position: relative;
            width: 100%;
            height: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            overflow: hidden;
        }

        .scanner-line {
            position: absolute;
            top: 0;
            left: -20%;
            width: 20%;
            height: 100%;
            background-color: #00ff00;
            animation: scan 2s infinite linear;
        }
        #startScanner{
            display:none;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 600px) {
            .search-container {
                padding-left: 15px;
                padding-right: 15px;
            }

            .search-container h1 {
                font-size: 22px;
            }

            .search-input {
                font-size: 14px;
                padding: 10px;
            }

            .loader-container {
                max-width: 250px;
            }
            #startScanner{
                display:inline-block;
            }

        }

        @media (max-width: 400px) {
            .headerL1 {
                height: 150px;
            }

            .search-container h1 {
                font-size: 18px;
            }

            .search-input {
                font-size: 12px;
                padding: 8px;
            }

            .loader-container {
                max-width: 200px;
            }
            #startScanner{
                display:inline-block;
            }
        }

        @media (max-width: 768px) {
            table {
                font-size: 16px;
            }
            td {
                padding-left: 20px;
                padding-top: 5px;
            }
            .container {
                padding: 10px;
                width: 100%;
            }
            .header {
                font-size: 28px;
            }
            .header p {
                font-size: 1em;
            }
            #startScanner{
                display:inline-block;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 5px;
            }
            .header {
                font-size: 22px;
            }
            .header p {
                font-size: 0.9em;
            }
            td {
                font-size: 14px;
            }
            #startScanner{
                display:inline-block;
            }
        }

        	    /* Modal styles */
        .modalqr {
            position: fixed;
            top: 0%;
            left: 5%;
            width: 300px;
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* border-radius: 8px 8px 0px 0px; */
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .modal-headerqr {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background-color: #0073e6;
            color: white;
            cursor: move;
            /* border-radius: 8px 8px 0px 0px; */
        }
        .modal-controls button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            margin-left: 8px;
        }
        .modal-contentqr {
            padding: 20px;
        }
        .modal-hidden {
            display: none !important;
        }
    </style>
</head>
<body>
<div class="headerL1" style="display:none;">
    <table align="center" border="0" style="border-collapse:collapse;border-spacing:0;width:100px;margin-left:1.5px;">
        <tr>
            <td style="width:50%;" align="right">
                <img src="../images/dcplinado.png" class="logoHeader" style="height: 55px;opacity:0.20;">
            </td>
            <td style="width:40%;" align="left">
                <img src="../images/davao.png" class="logoHeader" style="height: 70px;opacity:0.20">
            </td>
            <td style="width:10%;vertical-align:top" align="left">
                <img src="../images/system4.png" class="logoHeader" style="height: 62px;opacity:0.30">
            </td>
        </tr>
    </table>
</div>
<div class="container">
    <div class="search-container" style=" padding: 10px 10px; max-width: 500px; margin: auto;">
        <h1 style="font-family: 'Arial', sans-serif; color: #333; font-size: 28px; font-weight: bold; margin-bottom: 20px; text-align: center;">Receive Tracking</h1>
     

        <input type="text" id="ok" class="search-input" placeholder="Scan a QR" disabled style="display:none;font-family: 'Arial', sans-serif; padding: 15px; width: 100%; border-radius: 10px; border: 1px solid #ddd; font-size: 16px; box-sizing: border-box; outline: none; color: #333; background-color: #fff; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">

        <p style="font-family: 'Arial', sans-serif; font-size: 14px; color: #666; margin-top: 10px;" id='status'>Please scan the QR code to proceed.</p>
        <button id="startScanner"  style="padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;">Open Camera</button>
        </div>
        <div id="scanningAnimation" style="display: none; text-align: center; padding-top: 20px;">
        <div class="scanning-bar" style="width: 100px; height: 5px; background-color: #00bfff; margin: 0 auto; position: relative;">
            <div class="scanner-line" style="position: absolute; top: 0; left: 0; width: 20%; height: 100%; background-color: #00ff00; animation: scan 2s infinite;"></div>
        </div>
        <p style="font-family: 'Arial', sans-serif; font-size: 16px; color: #00bfff; margin-top: 10px;">Scanning...</p>
        <!-- <button id="startScanner" style="padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;">Open Camera</button> -->
    </div>

    
    <!-- <button id="startScanner" 
					style="margin-left:px; padding: 8px 0px; border: none; background: url(../images/qr-scan.png) no-repeat center center; background-size: contain; width: 35px; height: 33px; cursor: pointer;">Open Camera
		</button> -->
    <!-- Scanning Animation Div -->

    <div id="ResultContainer">
        <div class="loader-container">
            <div class="loader-bar" id="loaderBar"></div>
            <div class="loader-percentage" id="loaderPercentage">0%</div>
        </div>
    </div>

</div>

<div id="qrModal" class="modalqr" style="display: none;">
    <div id="qrModalHeader" class="modal-headerqr">
        <span id="qrModalTitle">QR Code Scanner</span>
        <div class="modal-controls">
            <button id="minimizeBtn">−</button>
            <button id="maximizeBtn" style="display:none;">⬜</button>
            <button id="closeBtn">×</button>
        </div>
    </div>
    <div class="modal-contentqr">
        <div id="qr-reader" style="width: auto; height: auto;"></div>
    </div>
</div>
</body>
</html>
<script src="../javascript/html5-qrcode.min1xxx.js"></script>


<script>
	showScanningAnimation();
	// Show the scanning animation
function showScanningAnimation() {
    document.getElementById('scanningAnimation').style.display = 'block';
}

// Hide the scanning animation
function hideScanningAnimation() {
    document.getElementById('scanningAnimation').style.display = 'none';
}


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

	function formatTrackingNumber(decodedText) {
		const code = decodedText.substring(3, 7);
		const series = decodedText.substring(7);
		const trackType = decodedText.substring(2, 3);
		
		// alert(trackingyear);
		// const year = 2024 + (trackingyear.charCodeAt(0) - 'A'.charCodeAt(0));

		return trackType === 'Y' ? `PR-${code}-${series}` : `${code}-${series}`;
	}

	function receiveTracking(me, scannerBuffer) {
		var trackingNumber = me.value.toUpperCase();
		var officeCode = "<?php echo htmlspecialchars($officeCode); ?>";
		var trackingyear = scannerBuffer.substring(1, 2);
		var year = 2024 + (trackingyear.charCodeAt(0) - 'A'.charCodeAt(0));
		
		if (trackingNumber.length > 1) {
			var queryString = "?searchTrackingNumberQR=1&trackingNumber=" + trackingNumber + "&officeCode=" + officeCode + "&trackingyear=" + year;
			var container = document.getElementById('ResultContainer');
			loader();
			ajaxGetAndConcatenate(queryString, processorLink, container, "searchTrackingNumberQR");
			container.style.display = 'block';
			document.getElementById('status').style.display = 'none';
			hideScanningAnimation();
		}
	}

	function loader() {
		const container = document.getElementById('ResultContainer');
		container.innerHTML = `
			<div class="loader-container">
				<div class="loader-bar"></div>
				<div class="loader-percentage">0%</div>
			</div>`;

		const bar = container.querySelector('.loader-bar');
		const percentage = container.querySelector('.loader-percentage');

		let progress = 0;
		const interval = setInterval(() => {
			progress += 1;
			bar.style.width = progress + '%';
			percentage.textContent = progress + '%';

			if (progress >= 100) {
				clearInterval(interval);
			}
		}, 30);
	}

    var mainTab;

    function showTrackingNumber(trackingNumber) {
        if (mainTab && !mainTab.closed && mainTab.location.href.includes("citydoc2024/interface/main.php")) {

            document.body.classList.add("fade-out");

            setTimeout(function() {
                mainTab.focus();

                mainTab.postMessage({ trackingNumber: trackingNumber }, "*");

                document.body.classList.remove("fade-out");
            }, 500); 
        } else {
            mainTab = window.open("../../citydoc2024/interface/main.php", "_blank");

            mainTab.addEventListener('load', function() {

                mainTab.postMessage({ trackingNumber: trackingNumber }, "*");
            });
        }
    }


/* ---------------------------------- Jay qr scanner function ----*/
    const modal = document.getElementById('qrModal');
    const header = document.getElementById('qrModalHeader');
    const qrReader = document.getElementById('qr-reader');
    const inputField = document.getElementById('ok');
    const startScanner = document.getElementById('startScanner');
    const closeBtn = document.getElementById('closeBtn');
    const minimizeBtn = document.getElementById('minimizeBtn');

    let offsetX = 0, offsetY = 0, isDragging = false, isMaximized = false;
    let html5QrCode; 

    // Function to stop the QR code scanner
    async function stopScanner() {
        try {
            if (html5QrCode) {
                await html5QrCode.stop();
            }
        } catch (err) {
            console.warn("Error stopping QR Code scanner:", err);
        }
    }

    startScanner.addEventListener('click', () => {
        modal.style.display = 'inline-block';
        qrReader.innerHTML = ''; 

        html5QrCode = new Html5Qrcode("qr-reader");

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 30, qrbox: { width: 1000, height: 1000 }, disableFlip: false },
            (decodedText) => {
				var trackingNumber = formatTrackingNumber(decodedText);
                inputField.value = trackingNumber;
				// alert(decodeYear(decodedText));
                // alert(decodedText);
                receiveTracking(inputField,decodedText);
                // alert(trackingNumber);

                stopScanner(); 
                modal.style.display = 'none';
            },
            (errorMessage) => console.warn(`QR Code scan error: ${errorMessage}`)
        ).catch(err => console.error(`Error starting QR Code scanner: ${err}`));
    });



    // Close button logic
    closeBtn.addEventListener('click', async () => {
        await stopScanner(); // Stop the QR scanner before closing the modal
        modal.style.display = 'none';
    });

    // Minimize button logic
    minimizeBtn.addEventListener('click', () => {
        const content = modal.querySelector('.modal-content');
        content.style.display = content.style.display === 'none' ? 'inline-block' : 'none';
    });

    // Dragging logic
    header.addEventListener('mousedown', (e) => {
        isDragging = true;
        offsetX = e.clientX - modal.getBoundingClientRect().left;
        offsetY = e.clientY - modal.getBoundingClientRect().top;
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            modal.style.left = `${e.clientX - offsetX}px`;
            modal.style.top = `${e.clientY - offsetY}px`;
        }
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
    });




	/*--------------*/
</script>

