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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap");
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-size: cover;
            color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' version='1.1' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns:svgjs='http://svgjs.dev/svgjs' width='1440' height='560' preserveAspectRatio='none' viewBox='0 0 1440 560'%3e%3cg mask='url(%26quot%3b%23SvgjsMask1000%26quot%3b)' fill='none'%3e%3crect width='1440' height='560' x='0' y='0' fill='%230e2a47'%3e%3c/rect%3e%3cpath d='M0.84 443.06L65.79 480.56L65.79 555.56L0.84 593.06L-64.11 555.56L-64.11 480.56zM65.79 555.56L130.75 593.06L130.75 668.06L65.79 705.56L0.84 668.06L0.84 593.06zM130.75 218.06L195.7 255.56L195.7 330.56L130.75 368.06L65.79 330.56L65.79 255.56zM260.65 -6.94L325.61 30.56L325.61 105.56L260.65 143.06L195.7 105.56L195.7 30.56zM325.61 555.56L390.56 593.06L390.56 668.06L325.61 705.56L260.65 668.06L260.65 593.06zM390.56 443.06L455.52 480.56L455.52 555.56L390.56 593.06L325.61 555.56L325.61 480.56zM585.42 105.56L650.38 143.06L650.38 218.06L585.42 255.56L520.47 218.06L520.47 143.06zM585.42 330.56L650.38 368.06L650.38 443.06L585.42 480.56L520.47 443.06L520.47 368.06zM650.38 218.06L715.33 255.56L715.33 330.56L650.38 368.06L585.42 330.56L585.42 255.56zM715.33 555.56L780.29 593.06L780.29 668.06L715.33 705.56L650.38 668.06L650.38 593.06zM910.19 -6.94L975.15 30.56L975.15 105.56L910.19 143.06L845.24 105.56L845.24 30.56zM910.19 218.06L975.15 255.56L975.15 330.56L910.19 368.06L845.24 330.56L845.24 255.56zM1105.05 105.56L1170.01 143.06L1170.01 218.06L1105.05 255.56L1040.1 218.06L1040.1 143.06zM1170.01 -6.94L1234.96 30.56L1234.96 105.56L1170.01 143.06L1105.05 105.56L1105.05 30.56zM1234.96 330.56L1299.91 368.06L1299.91 443.06L1234.96 480.56L1170.01 443.06L1170.01 368.06zM1364.87 555.56L1429.82 593.06L1429.82 668.06L1364.87 705.56L1299.91 668.06L1299.91 593.06zM1494.78 105.56L1559.73 143.06L1559.73 218.06L1494.78 255.56L1429.82 218.06L1429.82 143.06zM1494.78 555.56L1559.73 593.06L1559.73 668.06L1494.78 705.56L1429.82 668.06L1429.82 593.06z' stroke='%2303305d' stroke-width='2'%3e%3c/path%3e%3cpath d='M-6.66 443.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM58.29 480.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM58.29 555.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM-6.66 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM-71.61 555.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM-71.61 480.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM123.25 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM123.25 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM58.29 705.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM-6.66 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM123.25 218.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM188.2 255.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM188.2 330.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM123.25 368.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM58.29 330.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM58.29 255.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM253.15 -6.94 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM318.11 30.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM318.11 105.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM253.15 143.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM188.2 105.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM188.2 30.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM318.11 555.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM383.06 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM383.06 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM318.11 705.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM253.15 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM253.15 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM383.06 443.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM448.02 480.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM448.02 555.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM318.11 480.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM577.92 105.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM642.88 143.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM642.88 218.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM577.92 255.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM512.97 218.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM512.97 143.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM577.92 330.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM642.88 368.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM642.88 443.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM577.92 480.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM512.97 443.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM512.97 368.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM707.83 255.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM707.83 330.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM707.83 555.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM772.79 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM772.79 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM707.83 705.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM642.88 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM642.88 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM902.69 -6.94 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM967.65 30.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM967.65 105.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM902.69 143.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM837.74 105.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM837.74 30.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM902.69 218.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM967.65 255.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM967.65 330.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM902.69 368.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM837.74 330.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM837.74 255.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1097.55 105.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1162.51 143.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1162.51 218.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1097.55 255.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1032.6 218.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1032.6 143.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1162.51 -6.94 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1227.46 30.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1227.46 105.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1097.55 30.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1227.46 330.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1292.41 368.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1292.41 443.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1227.46 480.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1162.51 443.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1162.51 368.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1357.37 555.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1422.32 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1422.32 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1357.37 705.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1292.41 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1292.41 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1487.28 105.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1552.23 143.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1552.23 218.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1487.28 255.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1422.32 218.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1422.32 143.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1487.28 555.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1552.23 593.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1552.23 668.06 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0zM1487.28 705.56 a7.5 7.5 0 1 0 15 0 a7.5 7.5 0 1 0 -15 0z' fill='%2303305d'%3e%3c/path%3e%3cpath d='M29.37 277.12L72.67 302.12L72.67 352.12L29.37 377.12L-13.93 352.12L-13.93 302.12zM72.67 352.12L115.98 377.12L115.98 427.12L72.67 452.12L29.37 427.12L29.37 377.12zM29.37 427.12L72.67 452.12L72.67 502.12L29.37 527.12L-13.93 502.12L-13.93 452.12zM72.67 502.12L115.98 527.12L115.98 577.12L72.67 602.12L29.37 577.12L29.37 527.12zM115.98 -22.88L159.28 2.12L159.28 52.12L115.98 77.12L72.67 52.12L72.67 2.12zM159.28 202.12L202.58 227.12L202.58 277.12L159.28 302.12L115.98 277.12L115.98 227.12zM115.98 277.12L159.28 302.12L159.28 352.12L115.98 377.12L72.67 352.12L72.67 302.12zM115.98 427.12L159.28 452.12L159.28 502.12L115.98 527.12L72.67 502.12L72.67 452.12zM159.28 502.12L202.58 527.12L202.58 577.12L159.28 602.12L115.98 577.12L115.98 527.12zM245.88 52.12L289.19 77.12L289.19 127.12L245.88 152.12L202.58 127.12L202.58 77.12zM202.58 127.12L245.88 152.12L245.88 202.12L202.58 227.12L159.28 202.12L159.28 152.12zM245.88 352.12L289.19 377.12L289.19 427.12L245.88 452.12L202.58 427.12L202.58 377.12zM202.58 427.12L245.88 452.12L245.88 502.12L202.58 527.12L159.28 502.12L159.28 452.12zM419.09 52.12L462.4 77.12L462.4 127.12L419.09 152.12L375.79 127.12L375.79 77.12zM419.09 502.12L462.4 527.12L462.4 577.12L419.09 602.12L375.79 577.12L375.79 527.12zM505.7 52.12L549 77.12L549 127.12L505.7 152.12L462.4 127.12L462.4 77.12zM505.7 202.12L549 227.12L549 277.12L505.7 302.12L462.4 277.12L462.4 227.12zM505.7 352.12L549 377.12L549 427.12L505.7 452.12L462.4 427.12L462.4 377.12zM505.7 502.12L549 527.12L549 577.12L505.7 602.12L462.4 577.12L462.4 527.12zM549 127.12L592.3 152.12L592.3 202.12L549 227.12L505.7 202.12L505.7 152.12zM592.3 352.12L635.61 377.12L635.61 427.12L592.3 452.12L549 427.12L549 377.12zM635.61 -22.88L678.91 2.12L678.91 52.12L635.61 77.12L592.3 52.12L592.3 2.12zM678.91 352.12L722.21 377.12L722.21 427.12L678.91 452.12L635.61 427.12L635.61 377.12zM765.51 52.12L808.82 77.12L808.82 127.12L765.51 152.12L722.21 127.12L722.21 77.12zM852.12 502.12L895.42 527.12L895.42 577.12L852.12 602.12L808.82 577.12L808.82 527.12zM895.42 -22.88L938.72 2.12L938.72 52.12L895.42 77.12L852.12 52.12L852.12 2.12zM895.42 427.12L938.72 452.12L938.72 502.12L895.42 527.12L852.12 502.12L852.12 452.12zM1025.33 202.12L1068.63 227.12L1068.63 277.12L1025.33 302.12L982.03 277.12L982.03 227.12zM982.03 277.12L1025.33 302.12L1025.33 352.12L982.03 377.12L938.72 352.12L938.72 302.12zM1025.33 352.12L1068.63 377.12L1068.63 427.12L1025.33 452.12L982.03 427.12L982.03 377.12zM1068.63 127.12L1111.93 152.12L1111.93 202.12L1068.63 227.12L1025.33 202.12L1025.33 152.12zM1111.93 352.12L1155.23 377.12L1155.23 427.12L1111.93 452.12L1068.63 427.12L1068.63 377.12zM1155.23 -22.88L1198.54 2.12L1198.54 52.12L1155.23 77.12L1111.93 52.12L1111.93 2.12zM1155.23 127.12L1198.54 152.12L1198.54 202.12L1155.23 227.12L1111.93 202.12L1111.93 152.12zM1198.54 202.12L1241.84 227.12L1241.84 277.12L1198.54 302.12L1155.23 277.12L1155.23 227.12zM1285.14 202.12L1328.44 227.12L1328.44 277.12L1285.14 302.12L1241.84 277.12L1241.84 227.12zM1241.84 277.12L1285.14 302.12L1285.14 352.12L1241.84 377.12L1198.54 352.12L1198.54 302.12zM1328.44 -22.88L1371.75 2.12L1371.75 52.12L1328.44 77.12L1285.14 52.12L1285.14 2.12zM1328.44 127.12L1371.75 152.12L1371.75 202.12L1328.44 227.12L1285.14 202.12L1285.14 152.12zM1415.05 -22.88L1458.35 2.12L1458.35 52.12L1415.05 77.12L1371.75 52.12L1371.75 2.12zM1458.35 52.12L1501.65 77.12L1501.65 127.12L1458.35 152.12L1415.05 127.12L1415.05 77.12zM1415.05 127.12L1458.35 152.12L1458.35 202.12L1415.05 227.12L1371.75 202.12L1371.75 152.12zM1458.35 502.12L1501.65 527.12L1501.65 577.12L1458.35 602.12L1415.05 577.12L1415.05 527.12z' stroke='rgba(6%2c 80%2c 156%2c 0.93)' stroke-width='2'%3e%3c/path%3e%3c/g%3e%3cdefs%3e%3cmask id='SvgjsMask1000'%3e%3crect width='1440' height='560' fill='white'%3e%3c/rect%3e%3c/mask%3e%3c/defs%3e%3c/svg%3e");
            background-size: cover;
            /* background-repeat: no-repeat; */
            animation: slideBackground 300s linear infinite;
        }

        @keyframes slideBackground {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: -1440px 0; /* Moves the background left */ 
            }
        }


        .login-container {
            /* background: #1E56A0; */
            background: transparent;
            /* opacity: ; */
            color: #fff;
            border-radius: 16px;
            padding: 40px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 6px 30px rgba(0, 0, 0, 9);
            text-align: center;
            backdrop-filter: blur(8px);
            font-family: "Poppins", sans-serif;
        }

        .login-container h1 {
            margin-bottom: 30px;
            font-size: 2em;
            font-weight: bold;
        }

        /* Style the form group */
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        /* Input field styles */
        .form-group input {
            width: 100%;
            padding: 12px;
            font-size: 1em;
            border: 1px solid #00509E;
            border-radius: 8px;
            outline: none;
            background: white;
            color: black;
            transition: border-color 0.3s ease-in-out;
        }

        /* Label positioning */
        .form-group label {
            position: absolute;
            top: 12px;
            left: 12px;
            font-size: 1em;
            color: #aaa;
            transition: 0.3s ease-in-out;
            pointer-events: none; /* So the label doesn't block the input */
        }

        /* Move label up when focused or not empty */
        .form-group input:focus + label,
        .form-group input:not(:placeholder-shown) + label {
            top: -15px;
            left: 12px;
            font-size: 0.8em;
            letter-spacing: 1px;
            color: white;
        }

        /* Focus effect for the input field */
        .form-group input:focus {
            border-color: #1c7ed6;
        }


        .login-btn {
            width: 50%;
            padding: 15px;
            font-size: 1.1em;
            font-weight: bold;
            /* background: #0074D9; */
            background: #0063B2;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }

        .login-btn:hover {
            background: #0056a3;
            transform: translateY(-2px);
        }

        .login-btn:active {
            background: #003d73;
            transform: translateY(0);
        }

        .login-container {
            animation: fadeIn 1.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                padding: 20px;
                border-radius: 12px;
                width: 80%;
            }

            .login-container h1 {
                font-size: 1.8em;
                margin-bottom: 20px;
            }

            .form-group input {
                padding: 17px;
                font-size: 0.9em;
                margin-top:3px;
                width:90%;
            }

            .login-btn {
                padding: 15px;
                font-size: 1em;
                margin-bottom:30px;
            }
            .form-group label{
                top: 16px;
                left: 25px;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 15px;
            }

            .login-container h1 {
                font-size: 1.5em;
            }

            .form-group input {
                font-size: 0.85em;
            }

            .login-btn {
                padding: 10px;
                font-size: 0.9em;
            }
        }

        @keyframes customAni {
0% {
	transform: translateX(0);
  }

  100% {
	transform: translateX(-100px);
  }
}
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login to DT Receiver</h1>
        <!-- <div class="form-group">
            <label for="inputLoginEmployeeNumber">Employee Number</label>
            <input type="text" id="inputLoginEmployeeNumber" placeholder="Enter your employee number">
        </div> -->


        <!-- <div class="form-group">
            <label for="inputLoginPassword">Password</label>
            <input type="password" id="inputLoginPassword" onkeypress="checkEnter(event)" placeholder="Enter your password">
        </div> -->

        <div class="form-group">
            <input type="text" id="inputLoginEmployeeNumber" name="random_employee_number" placeholder=" " autocomplete="new-password">
            <label for="inputLoginEmployeeNumber">Employee Number</label>
        </div>

        <div class="form-group">
            <input type="password" id="inputLoginPassword" name="random_password" onkeypress="checkEnter(event)" placeholder=" " autocomplete="new-password">
            <label for="inputLoginPassword">Password</label>
        </div>




        <div>
            <button class="login-btn" id="buttonLoginRegistration" onclick="submitLogin()">Log In</button>
        </div>
    </div>

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
</body>
</html>


