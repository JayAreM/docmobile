<?php
	//require_once("../includes/database.php");

    //$ip = getIp(); 
    
    $dt = time();
	$dateEncoded = date('Y-m-d h:i A', $dt);
    
	$browser =  $_SERVER['HTTP_USER_AGENT']; 
	$ip = getIp();
	$url = $_SERVER['REQUEST_URI'];
	$serverSoftware = $_SERVER['SERVER_SOFTWARE'];
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	$port = $_SERVER['SERVER_PORT'];
	$name = '0';
	if(isset($_SESSION['fullName'])){
		$name = $_SESSION['fullName'];
	}
	
	$_SESSION['asdf'] = 'thanos';
	//$date = date("Y-m-d");
	
	//$iPAddress = $database->FetchIpAddress($ip,$date);
	//$IpInThisDay =  $database->num_rows($iPAddress);
	
	//$recordTodayVisitor  = $database->fetch_array($database->FetchTodayVisitor($date));
	
	//$recordAllVisitor  = $database->num_rows($database->FetchAllVisitors());
	
	
	//$today = 123;
	//$database->RegisterVisitorLog($ip,$browser,$url,$serverSoftware,$protocol,$port,$dateEncoded);
	
	
		
	$sql = "INSERT INTO vstr.visitorlog (Ip,Browser,Url,ServerSoftware,Protocol,Port,DateAccess,Session) VALUES
					('". $ip ."','". $browser ."','". $url ."','". $serverSoftware ."','". $protocol ."','". $port ."','" . $dateEncoded . "','" . $name . "')";
	// $database->queryV($sql);
			
		
	
	
	function getIp(){
	    $ip = $_SERVER['REMOTE_ADDR'];
	    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    return $ip;
	}
?>