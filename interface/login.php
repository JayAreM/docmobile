<?php

	if(!isset($_SESSION)) {
		session_start();
	}


	require_once("../javascript/ajaxFunction.php");
	require_once('../includes/database.php');
	
	$mt =  time();

	if(isset($_SESSION['employeeNumber'])) {
		$link = '<script>window.open("main.php", "_self");</script>';
		echo $link;
	}
	
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>DocMobile</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" href="../images/system2.png"
	type="image/x-icon" />
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/iconic/css/material-design-iconic-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/login.css">
<!--===============================================================================================-->
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100 animate__animated animate__zoomIn" >
				<div class="login100-form validate-form">
					
					<span class="login100-form-title p-b-48">
						<!-- <i class="zmdi zmdi-font"></i> -->
						<img src="../images/system2.png" alt="Sample Image" width="50">  DocMobile
					</span>

					<div class="wrap-input100 validate-input" data-validate="Valid Employee Number is: 6 digits">
						<input class="input100" type="text" name="employeenumber" id="inputLoginEmployeeNumber" autocomplete="off">
						<span for="inputLoginEmployeeNumber" class="focus-input100" data-placeholder="Employee Number"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<span class="btn-show-pass">
							<i class="zmdi zmdi-eye"></i>
						</span>
						<input class="input100" id="inputLoginPassword" type="password" name="pass" autocomplete="new-password" onkeypress="checkEnter(event)">
						<span for="inputLoginPassword" class="focus-input100" data-placeholder="Password"></span>
					</div>

					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn" onclick="submitLogin()">
								Login
							</button>
						</div>
					</div>

					<!-- <div class="text-center p-t-115">
						<span class="txt1">
							Don’t have an account?
						</span>

						<a class="txt2" href="#">
							Sign Up
						</a>
					</div> -->
				</div>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/daterangepicker/moment.min.js"></script>
	<script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>


<script>

	function submitLogin(){
		setCookie("valbalangue",1, 1);
		var mt = "<?php echo $mt; ?>";
		
		var employeeNumber  = document.getElementById('inputLoginEmployeeNumber').value + mt;
		
		var password  =  document.getElementById('inputLoginPassword').value;
		// alert(employeeNumber);
		if(employeeNumber.length < 6 ){
			alert("Invalid employee number. Please try again.");
		}else if(password.length < 1) {
			alert("No record found.");
		}else{
			var joiners = employeeNumber + '~!~' + password;	
			joiners =  vScram(joiners);
			// joiners = b6(password);
			//alert(joiners);
			var queryString = "?fujxyza=1&xaXvsfTs=" + encodeURIComponent(joiners);
			
			var container = '';
			ajaxGetAndConcatenate(queryString,processorLink,container,"fujxyza");
		}
	}

	function checkEnter(event) {
		if (event.key === "Enter") {
			submitLogin(); 
		}
	}

</script>
</html>