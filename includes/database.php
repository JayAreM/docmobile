<?php
	require_once("constants.php");
	date_default_timezone_set('Asia/Manila');	
	class MySQLDatabase {
		
		private $connection;
		public $last_query;
		
		// private $magic_quotes_active;
		private $real_escape_string_exists;
		public $rows_affected;

		function __construct(){
			$this->open_connection();	
			// $this->magic_quotes_active = get_magic_quotes_gpc();
			$this->real_escape_string_exists = function_exists("mysqli_real_escape_string");
		}
		public function open_connection(){
			$this->connection = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
			if(!$this->connection){
				die("Database connection failed: " . mysqli_error());
			}else{
				$db_select = mysqli_select_db($this->connection,DB_NAME);
				if(!$db_select){
					die("Database selection failed: " . mysqli_error());
				}
				
			}
		}
		public function close_connection(){
			if(isset($this->connection)){
				mysqli_close($this->connection);
				unset($this->connection);
			}
		}

		public function updateRealtimeLookup($year, $trackingNumber, $status, $dateEncoded) {

			$db = $this->getDB($year);

			// $sql = "SELECT 
			// 		Office,
			// 		CASE 
			// 			WHEN TrackingType = 'PR ' THEN 'Purchase Request'
			// 			WHEN TrackingType = 'PO' THEN 'Purchase Order'
			// 			WHEN TrackingType = 'PX' THEN 'Payment'
			// 			WHEN TrackingType = 'PY' THEN DocumentType
			// 			ELSE 'Unknown'
			// 		END AS DocType,
			// 		CASE 
			// 			WHEN TotalAmountMultiple > 0 THEN TotalAmountMultiple
			// 			WHEN PO_Amount > 0 THEN PO_Amount
			// 			WHEN Amount > 0 THEN Amount
			// 			ELSE 0
			// 		END AS ThisAmount
			// 		FROM ".$db.".vouchercurrent 
			// 		WHERE TrackingNumber = '".$trackingNumber."' LIMIT 1;";

			$sql = "SELECT 
					Office, TrackingType, PeriodMonth, PR_Month,
					CASE 
						WHEN TrackingType = 'PR ' THEN 'Purchase Request'
						WHEN TrackingType = 'PO' THEN 'Purchase Order'
						WHEN TrackingType = 'PX' THEN 'Payment'
						WHEN TrackingType = 'PY' THEN DocumentType
						ELSE 'Unknown'
					END AS DocType,
					CASE 
						WHEN TotalAmountMultiple > 0 THEN TotalAmountMultiple
						WHEN PO_Amount > 0 THEN PO_Amount
						WHEN Amount > 0 THEN Amount
						ELSE 0
					END AS ThisAmount
					FROM ".$db.".vouchercurrent 
					WHERE TrackingNumber = '".$trackingNumber."' LIMIT 1;";

			$record = $this->query($sql);
			$data = $this->fetch_array($record);
			$ofis = $data['Office'];
			$docType = $data['DocType'];
			$amount = $data['ThisAmount'];
			$periodMonth = $data['PeriodMonth'];
			$prMonth = $data['PR_Month'];
			$trackingType = $data['TrackingType'];

			if(strlen(trim($prMonth ?? '')) > 0) {
				$periodMonth = date('F', mktime(0, 0, 0, $prMonth, 10));
			}

			$sql = "SELECT Id FROM lookup.realtime WHERE Year = '".$year."' AND TrackingNumber = '".$trackingNumber."'";
			$record = $this->query($sql);
			
			if($this->num_rows($record) > 0) {
				$data = $this->fetch_array($record);
				$id = $data['Id'];
			
				$sql = "UPDATE lookup.realtime SET Status = '".$status."', DateModified = '".$dateEncoded."', Notified = '0', Amount = '".$amount."', DocumentType = '".$docType."' WHERE Id = '".$id."'";
				$this->query($sql);
			}else {
				$sql = "INSERT INTO lookup.realtime 
						(Year, Office, TrackingNumber, Status, DateModified, Notified, DocumentType, Amount, PMonth, TrackingType) 
						VALUES 
						('".$year."','".$ofis."','".$trackingNumber."','".$status."','".$dateEncoded."','0','".$docType."','".$amount."','".$periodMonth."','".$trackingType."')";
				$this->query($sql);
			}

			// $this->callMobileNotifier($ofis, $trackingNumber, $docType);
			
		}
		
		public function last_id(){
			return mysqli_insert_id($this->connection);
		}

		public function query($sql){
			$match = preg_match('/SELECT/i', $sql);
			$this->last_query = $sql;
			
			if($match == 1){
				$result = mysqli_query($this->connection,$sql);
				$this->confirm_query($result);
				return $result;
			}else{
				try{
					$result = $this->connection->query($sql);
					$this->confirm_query($result);
					$this->connection->begin_transaction();
					// $affected = $this->connection->query('SELECT ROW_COUNT() as rowsAffected');
					$affected = $this->connection->query('SELECT ROW_COUNT() as rowsAffected, LAST_INSERT_ID() as lastInsertId');
					$temp = $this->fetch_array($affected);
					$this->connection->commit();
					$this->rows_affected = $temp['rowsAffected'];	
					return $temp;
				}catch(\Throwable $e){
					$this->connection->rollback();
					throw $e;
				}
			}
		}

		public function insertReturnId($sql){	
			try{
				$result = $this->connection->query($sql);
				$this->confirm_query($result);
				$id = $this->last_id();
				$this->connection->begin_transaction();
				$affected = $this->connection->query('SELECT ROW_COUNT() as rowsAffected');
				$temp = $this->fetch_array($affected);
				$this->connection->commit();
				$this->rows_affected = $temp['rowsAffected'];	
				return $id;
			}catch(\Throwable $e){
				$this->connection->rollback();
				throw $e;
			}	
		}

		public function queryV($sql){
			$this->last_query = $sql;
			$result = mysqli_query($this->connection,$sql);
			$this->confirm_query($result);
			return $result;
		}
		
		private function confirm_query($result){
			if(!$result){
				$output = "Database query failed : " . mysqli_error($this->connection) . "</br>";
				$output .= "Last query : " . $this->last_query;
				$output .= "<div style = 'color:red;'>No changes has been made. Please contact programmer.</div>";
				die($output);
			}
		}

		public function charEncoder($value){
			 return mysqli_real_escape_string($this->connection,$value);
		}	
	
		//database - neutral methods
		public function fetch_array($result){
			return mysqli_fetch_array($result);
		}
		
		public function num_rows($value){
			return mysqli_num_rows($value);
		}
		public function affected_rows($value){
			return mysqli_affected_rows($this->connection);
		}
		public function redirect_to($location){
			header('Location:' . $location);
		}

		// special functions
		
		function numberToMonth($month){
			if($month == 1){
				$month = "January";
			}else if($month == 2){
				$month = "February";
			}if($month ==3){
				$month = "March";
			}if($month == 4){
				$month = "April";
			}if($month == 5){
				$month = "May";
			}if($month == 6){
				$month = "June";
			}if($month == 7){
				$month = "July";
			}if($month == 8){
				$month = "August";
			}if($month == 9){
				$month = "September";
			}if($month == 10){
				$month = "October";
			}if($month == 11){
				$month = "November";
			}if($month == 12){
				$month = "December";
			}
			return $month;
		}
		
		public function numToMonthField($num){
			if($num == "1"){
				$field = "Jan";
			}else if($num == "2"){
				$field = "Feb";
			}else if($num == "3"){
				$field = "Mar";
			}else if($num == "4"){
				$field = "Apr";
			}else if($num == "5"){
				$field = "May";
			}else if($num == "6"){
				$field = "Jun";
			}else if($num == "7"){
				$field = "Jul";
			}else if($num == "8"){
				$field = "Aug";
			}else if($num == "9"){
				$field = "Sep";
			}else if($num == "10"){
				$field = "Oct";
			}else if($num == "11"){
				$field = "Nov";
			}else if($num == "12"){
				$field = "Dex";
			}
			return $field;	
		}
		
		function numberToQuarter($month){
			if($month <= 3){
				$qtr = "1st Quarter";
			}else if($month <= 6){
				$qtr = "2nd Quarter";
			}else if($month <=9){
				$qtr = "3rd Quarter";
			}else if($month <= 12){
				$qtr = "4th Quarter";
			}
			return $qtr;
		}

		function numberToQuarter2016($month){
			if (preg_match_all("/\b(Jan|Feb|March)\b/i",$month,$matches)) { 
				$matches = array_unique($matches[0]);				
				$quarter = "1st Quarter";
			}else if (preg_match_all("/\b(Apr|May|June)\b/i",$month,$matches)) { 
				$quarter = "2nd Quarter";
			}else if (preg_match_all("/\b(July|Aug|Sept)\b/i",$month,$matches)) { 
				$quarter = "3rd Quarter";
			}else if (preg_match_all("/\b(Oct|Nov|December)\b/i",$month,$matches)) { 		
				$quarter = "4th Quarter";
			}else{
				$quarter = "";
			}

			return $quarter;
		}
		
		
		function dayCounter($date){
			$d1 = date_create($date);
			$d2 = date('Y-m-d');
			$d2 = date_create($d2);
			$count = date_diff($d1,$d2);
			return $count->format("%a");
		}
		function mondayToFriday($beginningDate){
			//ang pag ihap sa adlaw kay mag matter ang oras from 1:am to 1:am sa sunod na adlaw
			$dt = time();
			$endingDate = date('Y-m-d h:i A', $dt);
			$startDate = date_create($beginningDate);
			$endingDate = date_create($endingDate);
			$count = date_diff($startDate,$endingDate);
			
			$days = $count->format("%a");
			$counter = 0;
			for($i = 0; $i <= $days; $i++){
				$day = $beginningDate . " + " . $i . " days";
				$x = date('D', strtotime($day));
				if($x == "Sat" || $x == "Sun"){
					$counter++;
				}
			}
			return $days - $counter;
		}	
		function ordinal($number) {
			//https://stackoverflow.com/questions/3109978/display-numbers-with-ordinal-suffix-in-php
		    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
		    if ((($number % 100) >= 11) && (($number%100) <= 13))
		        return $number. 'th';
		    else
		        return $number. '<span style ="margin-top:-2px;font-size:10px;vertical-align:top;">' .  $ends[$number % 10] . '</span>';
		}
		public function completion($field){
			$completion = "Completion = case 
                 WHEN  id > 0 THEN  
                        IF(substring("  . $field . ",18,2) = 'PM'
                               ,if(TIMESTAMPDIFF(minute,DATE_ADD(substring("  . $field . ",1,16), INTERVAL 12 HOUR),NOW()) < 2
                                   ,'1 minute'
                                   ,if(TIMESTAMPDIFF(minute,DATE_ADD(substring("  . $field . ",1,16), INTERVAL 12 HOUR),NOW()) < 60
                                       ,concat(TIMESTAMPDIFF(minute,DATE_ADD(substring("  . $field . ",1,16), INTERVAL 12 HOUR),NOW()),' minutes')
                                       ,if(TIMESTAMPDIFF(minute,DATE_ADD(substring("  . $field . ",1,16), INTERVAL 12 HOUR),NOW()) < 120
                                           ,'1 hour'
                                           ,If(TIMESTAMPDIFF(minute,DATE_ADD(substring("  . $field . ",1,16), INTERVAL 12 HOUR),NOW()) < 1440 
                                               ,concat(TIMESTAMPDIFF(hour,DATE_ADD(substring("  . $field . ",1,16), INTERVAL 12 HOUR),NOW()),' hours')
                                               ,if(TIMESTAMPDIFF(minute,DATE_ADD(substring("  . $field . ",1,16), INTERVAL 12 HOUR),NOW()) <= 2880 
                                                   ,'1 day'
                                                   ,concat(floor(TIMESTAMPDIFF(hour,DATE_ADD(substring("  . $field . ",1,16), INTERVAL 12 HOUR),NOW()) / 24),' days')
                                               )
                                           )
                                       )
                                   )
                                )        
                                ,if(TIMESTAMPDIFF(minute,substring("  . $field . ",1,16),NOW()) < 2
                                    ,'1 minute'
                                    ,if(TIMESTAMPDIFF(minute,substring("  . $field . ",1,16),NOW()) < 60
                                        ,concat(TIMESTAMPDIFF(minute,substring("  . $field . ",1,16),NOW()),' minutes')
                                        ,if(TIMESTAMPDIFF(minute,substring("  . $field . ",1,16),NOW()) < 120
                                            ,'1 hour'
                                            ,If(TIMESTAMPDIFF(minute,substring("  . $field . ",1,16),NOW()) < 1440 
                                                ,concat(TIMESTAMPDIFF(hour,substring("  . $field . ",1,16),NOW()),' hours')
                                                ,if(TIMESTAMPDIFF(minute,substring("  . $field . ",1,16),NOW()) <= 2880 
                                                    ,'1 day'
                                                    ,concat(floor(TIMESTAMPDIFF(hour,substring("  . $field . ",1,16),NOW()) / 24),' days')
                                                )
                                            )
                                        )
                                    )
                                )
                         )
                END";
				return $completion;
		}

		public function completionPerHour($field){
			return "MinutesCounter = case 
                	WHEN  id > 0 THEN  
                       	IF(
							substring(".$field.",18,2) = 'PM',
							IF(
								TIMESTAMPDIFF(minute,DATE_ADD(substring(datemodified,1,16), INTERVAL 12 HOUR),NOW()) < 1, 
								1, 
								TIMESTAMPDIFF(minute,DATE_ADD(substring(datemodified,1,16), INTERVAL 12 HOUR),NOW())
								), 
							IF(
								TIMESTAMPDIFF(minute,substring(datemodified,1,16),NOW()) < 1, 
								1, 
								TIMESTAMPDIFF(minute,substring(datemodified,1,16),NOW())
								)
						) END";
		}

		public function ezDate($d) {
		 $ts = time() - strtotime(str_replace("-","/",$d)) ;	
	        // $ts = time()+21600 - strtotime(str_replace("-","/",$d)) ;
	        if($ts>31536000) $val = intval($ts/31536000).' year';
	        else if($ts>2419200) $val = intval($ts/2419200).' month';
	        else if($ts>604800) $val = intval($ts/604800).' week';
	        else if($ts>86400) $val = intval($ts/86400).' day';
	        else if($ts>3600) $val = intval($ts/3600).' hour';
	        else if($ts>60) $val = intval($ts/60).' minute';
	        else $val = $ts.' second';
	        if($val>1) $val .= 's';
	        return $val . ' ago';
	    }
		public function ezDate1($d) {
		 $ts = time() - strtotime(str_replace("-","/",$d)) ;	
	       // $ts = time()+21600 - strtotime(str_replace("-","/",$d)) ;
	        if($ts>604800) $val = round($ts/604800,0). ' week';
	        else if($ts>86400) $val = round($ts/86400,0).' day';
	        else if($ts>3600) $val =  round($ts/3600,0).' hour';
	        else if($ts>60) $val = round($ts/60,0).' minute';
	        else $val = $ts.' second';
	        if($val>1) $val .= 's';
	      
	        return $val . ' ago';
	    }
		public function ezDateDay($d) {
		 $ts = time() - strtotime(str_replace("-","/",$d)) ;	
	        if($ts>86400) $val = intval($ts/86400).'&nbsp;day';
	        else if($ts>3600) $val = intval($ts/3600).'&nbsp;hour';
	        else if($ts>60) $val = intval($ts/60).'&nbsp;minute';
	        else $val = $ts.'&nbsp;second';
	        if($val>1) $val .= 's';
	        return $val . '&nbsp;ago';
	    }
	    public function ezDateTotalDay($d) {
		 	$ts = time() - strtotime(str_replace("-","/",$d)) ;	
	        if($ts>86400){
	        	$t = intval($ts/86400); 
	        	$val = $t;
	        	/*if($t > 1 ){
					$val = '<b style = "color:red;font-size:14px;">'. $t . '</b>' . '&nbsp;days';
				}else{
					$val = '<b style = "color:red;font-size:14px;">'. $t . '</b>' . '&nbsp;day';
				}*/
	        	/*if($t > 1 ){
					$val = '<b style = "color:red;font-size:14px;">'. $t . '</b>' . '&nbsp;days';
				}else{
					$val = '<b style = "color:red;font-size:14px;">'. $t . '</b>' . '&nbsp;day';
				}*/
	        	 
			}else{
				$val = '';
			}
			
	        return $val;
	    }
		
		
		public function zeroToNothing($value){
			if($value == 0){
				return '&nbsp;';
			}else{
				return $value;
			}
		}
		public function zeroToEmpty($value){
			if($value == 0){
				return '';
			}else{
				return $value;
			}
		}
		public function nullToZero($value){
			if($value == ''){
				return 0;
			}else{
				return $value;
			}
		}
		public function toNumberFormat($value){
			if($value == '' || $value == 0 ){
				return '';
			}else{
				return number_format($value,2);
			}
		}
		
		public function dateFormat($dt){
			$year =  substr($dt,0,4);
			$month =  substr($dt,5,2);
			$day =  substr($dt,8,2);
			if($month == "01"){
				$month = "Jan";
			}else if($month == "02"){
				$month = "Feb";
			}else if($month == "03"){
				$month = "Mar";
			}else if($month == "04"){
				$month = "Apr";
			}else if($month == "05"){
				$month = "May";
			}else if($month == "06"){
				$month = "Jun";
			}else if($month == "07"){
				$month = "Jul";
			}else if($month == "08"){
				$month = "Aug";
			}else if($month == "09"){
				$month = "Sep";
			}else if($month == "10"){
				$month = "Oct";
			}else if($month == "11"){
				$month = "Nov";
			}else if($month == "12"){
				$month = "Dec";
			}
			return $month . ' ' . $day . ', ' . $year;	
		}
		public function numWords($number) {
			//salamat http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
		    $hyphen      = ' ';
		   // $conjunction = ' and ';
		    $conjunction = ' ';
		    $separator   = ' ';
		    $negative    = 'negative ';
		   // $decimal     = ' point ';
		   $decimal     = ' PESOS AND ';
		   
		    $dictionary  = array(
		        0                   => 'zero',
		        1                   => 'one',
		        2                   => 'two',
		        3                   => 'three',
		        4                   => 'four',
		        5                   => 'five',
		        6                   => 'six',
		        7                   => 'seven',
		        8                   => 'eight',
		        9                   => 'nine',
		        10                  => 'ten',
		        11                  => 'eleven',
		        12                  => 'twelve',
		        13                  => 'thirteen',
		        14                  => 'fourteen',
		        15                  => 'fifteen',
		        16                  => 'sixteen',
		        17                  => 'seventeen',
		        18                  => 'eighteen',
		        19                  => 'nineteen',
		        20                  => 'twenty',
		        30                  => 'thirty',
		        40                  => 'forty',
		        50                  => 'fifty',
		        60                  => 'sixty',
		        70                  => 'seventy',
		        80                  => 'eighty',
		        90                  => 'ninety',
		        100                 => 'hundred',
		        1000                => 'thousand',
		        1000000             => 'million',
		        1000000000          => 'billion',
		        1000000000000       => 'trillion',
		        1000000000000000    => 'quadrillion',
		        1000000000000000000 => 'quintillion'
		    );
		   
		    if (!is_numeric($number)) {
		        return false;
		    }
		   
		    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		        // overflow
		        trigger_error(
		            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
		            E_USER_WARNING
		        );
		        return false;
		    }	
		
		    if ($number < 0) {
		        return $negative . $this->numWords(abs($number));
		    }
		   
		    $string = $fraction = null;
		   
		    if (strpos($number, '.') !== false) {
		        list($number, $fraction) = explode('.', $number);
		    }
		   
		    switch (true) {
		        case $number < 21:
		            $string = $dictionary[$number];
		            break;
		        case $number < 100:
		            $tens   = ((int) ($number / 10)) * 10;
		            $units  = $number % 10;
		            $string = $dictionary[$tens];
		            if ($units) {
		                $string .= $hyphen . $dictionary[$units];
		            }
		            break;
		        case $number < 1000:
		            $hundreds  = $number / 100;
		            $remainder = $number % 100;
		            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
		            if ($remainder) {
		                $string .= $conjunction .  $this->numWords($remainder) ;
		            }
		            break;
		        default:
		            $baseUnit = pow(1000, floor(log($number, 1000)));
		            $numBaseUnits = (int) ($number / $baseUnit);
		            $remainder = $number % $baseUnit;
		            $string = $this->numWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
		            if ($remainder) {
		                $string .= $remainder < 100 ? $conjunction : $separator;
		                $string .= $this->numWords($remainder) ;
		            }
		            break;
		    }
		   
		    if (null !== $fraction && is_numeric($fraction)) {
		        $string .= $decimal;
		        $words = array();
		        foreach (str_split((string) $fraction) as $number) {
		            $words[] = $dictionary[$number];
		        }
		        $string .= implode(' ', $words);
		    }
		   
		    return $string ;
		}
		public function numWordsFinal($amount) {
			$n = explode(".",$amount);
			if(sizeof($n) == 2){
				$n1 =  $n[0];
				$n2 = abs($n[1]);
				if($n2 > 1){
					
					$inWords = $this->numWords($n1) . ' Pesos and ' . $n2 . '/100';
				}else if($n2 == 1){
					
					$inWords = $this->numWords($n1) . ' Pesos and ' . $n2 . '/100';
				}else{
					$inWords = $this->numWords($n1) . ' Pesos';
				}
			}else{
				$n1 =  $n[0];	
				$inWords = $this->numWords($n1) . ' Pesos';
			}
			return $inWords;
		}
		public function numWords1($number) {
			//salamat http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
		    $hyphen      = ' ';
		   // $conjunction = ' and ';
		    $conjunction = ' ';
		    $separator   = ' ';
		    $negative    = 'negative ';
		   // $decimal     = ' point ';
		   $decimal     = ' and ';
		   
		    $dictionary  = array(
		        0                   => 'zero',
		        1                   => 'one',
		        2                   => 'two',
		        3                   => 'three',
		        4                   => 'four',
		        5                   => 'five',
		        6                   => 'six',
		        7                   => 'seven',
		        8                   => 'eight',
		        9                   => 'nine',
		        10                  => 'ten',
		        11                  => 'eleven',
		        12                  => 'twelve',
		        13                  => 'thirteen',
		        14                  => 'fourteen',
		        15                  => 'fifteen',
		        16                  => 'sixteen',
		        17                  => 'seventeen',
		        18                  => 'eighteen',
		        19                  => 'nineteen',
		        20                  => 'twenty',
		        30                  => 'thirty',
		        40                  => 'forty',
		        50                  => 'fifty',
		        60                  => 'sixty',
		        70                  => 'seventy',
		        80                  => 'eighty',
		        90                  => 'ninety',
		        100                 => 'hundred',
		        1000                => 'thousand',
		        1000000             => 'million',
		        1000000000          => 'billion',
		        1000000000000       => 'trillion',
		        1000000000000000    => 'quadrillion',
		        1000000000000000000 => 'quintillion'
		    );
		   
		    if (!is_numeric($number)) {
		        return false;
		    }
		   
		    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		        // overflow
		        trigger_error(
		            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
		            E_USER_WARNING
		        );
		        return false;
		    }	
		
		    if ($number < 0) {
		        return $negative . $this->numWords(abs($number));
		    }
		   
		    $string = $fraction = null;
		   
		    if (strpos($number, '.') !== false) {
		        list($number, $fraction) = explode('.', $number);
		    }
		   
		    switch (true) {
		        case $number < 21:
		            $string = $dictionary[$number];
		            break;
		        case $number < 100:
		            $tens   = ((int) ($number / 10)) * 10;
		            $units  = $number % 10;
		            $string = $dictionary[$tens];
		            if ($units) {
		                $string .= $hyphen . $dictionary[$units];
		            }
		            break;
		        case $number < 1000:
		            $hundreds  = $number / 100;
		            $remainder = $number % 100;
		            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
		            if ($remainder) {
		                $string .= $conjunction . $this->numWords($remainder);
		            }
		            break;
		        default:
		            $baseUnit = pow(1000, floor(log($number, 1000)));
		            $numBaseUnits = (int) ($number / $baseUnit);
		            $remainder = $number % $baseUnit;
		            $string = $this->numWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
		            if ($remainder) {
		                $string .= $remainder < 100 ? $conjunction : $separator;
		                $string .= $this->numWords($remainder);
		            }
		            break;
		    }
		   
		    if (null !== $fraction && is_numeric($fraction)) {
		        $string .= $decimal;
		        $words = array();
		        foreach (str_split((string) $fraction) as $number) {
		            $words[] = $dictionary[$number];
		        }
		        $string .= implode(' ', $words);
		    }
		   
		    return $string;
		}
		public function oopsRedirect($year,$code,$type,$permission){
			session_start();
			if(isset($_SESSION['employeeNumber'])){
				$employeeNumber = $_SESSION['employeeNumber'];
				$accountType = $_SESSION['accountType'];	
				$officeCode = $_SESSION['cbo'];
				$perm = $_SESSION['perm'];
				
				
				if($permission == 0){
					if($employeeNumber != '900177'){
						if($officeCode == $code and $accountType == $type){
					
						}else{
							$link = "<script>window.open('../../citydoc" . $year . "/interface/login.php','_self')</script>";
							echo $link;
						}
					}else{
						
					}
					
					
				}else{
					if($officeCode !== $code and $accountType !== $type and $permission !== $perm ){
						$link = "<script>window.open('../../citydoc" . $year . "/interface/login.php','_self')</script>";
						echo $link;
					}
				}
			}else{
				$link = "<script>window.open('../../citydoc" . $year . "/interface/login.php','_self')</script>";
				echo $link;
			}
		}
		
		function checkImageUpload($file_field, $file, $folder){
		//Set file upload path
			

			//Set default file extension whitelist
			$whitelist_ext = array('jpeg','jpg','png','gif');
			//Set default file type whitelist
			$whitelist_type = array('image/jpeg', 'image/jpg', 'image/png','image/gif');

			// Create an array to hold any output
			$out = array('error'=>null);

			

		

			//Make sure that there is a file
			if((!empty($file[$file_field])) && ($file[$file_field]['error'] == 0)) {

				// Get filename
				$file_info = pathinfo($file[$file_field]['name']);
				$name = $file_info['filename'];
				$ext = $file_info['extension'];

				//Check file has the right extension           
				if (!in_array($ext, $whitelist_ext)) {
				  $out['error'][] = "Invalid file Extension";
				}

				//Check that the file is of the right type
				if (!in_array($file[$file_field]["type"], $whitelist_type)) {
				  $out['error'][] = "Invalid file Type";
				}
			}
			return $out;
		}
		function FileChecker($a,$allowed){
			$whitelist_ext = explode(',',strtoupper($allowed));
			$whitelist_type = [];
			for($i = 0 ; $i < sizeof($whitelist_ext); $i++){
				if($whitelist_ext[$i] == "JPG"){
					$type = "image/jpeg";
				}else if($whitelist_ext[$i] == "JPEG"){
					$type = "image/jpeg";
				}else if($whitelist_ext[$i] == "PNG"){
					$type = "image/png";
				}else if($whitelist_ext[$i] == "GIF"){
					$type = "image/gif";
				}else if($whitelist_ext[$i] == "XLS"){
					$type = "application/vnd.ms-excel";
				}else if($whitelist_ext[$i] == "XLSX"){
					$type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
				}else if($whitelist_ext[$i] == "DOC"){
					$type = "application/msword";
				}else if($whitelist_ext[$i] == "DOCX"){
					$type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
				}else if($whitelist_ext[$i] == "PDF"){
					$type = "application/pdf";
				}else if($whitelist_ext[$i] == "DBF"){
					$type = "application/octet-stream";
				}else if($whitelist_ext[$i] == "XML"){
					$type = "application/xml";
				}
				$whitelist_type[$i] = $type;
			}

			$err = 0;
			$file_info = pathinfo($a['name']);
			if((!empty($a)) && ($a['error'] == 0)) {
				// Get filename
				$name = $file_info['filename'];
				// $ext = $file_info['extension'];
				$ext = strtoupper($file_info['extension']);

				//Check file has the right extension  
				if (!in_array($ext, $whitelist_ext)) {
				 	$err = 1;
				}

				//Check that the file is of the right type
				if (!in_array($a["type"], $whitelist_type)) {
				  	$err = 1;
				}
			}
			return $err;
		}	
		function reArrayFiles($file_post) {
		    $file_ary = array();
		    $file_count = count($file_post['name']);
		    $file_keys = array_keys($file_post);
		    for ($i=0; $i<$file_count; $i++) {
		        foreach ($file_keys as $key) {
		            $file_ary[$i][$key] = $file_post[$key][$i];
		        }
		    }
		    return $file_ary;
			//https://www.php.net/manual/en/features.file-upload.multiple.php
		}	
		public function autoSpacer($str, $max){
			$len = strlen($str);
		
			if($len > $max){
				return $str;
			}else{
				$diff = $max - $len;
				$tempStr = $str;
		
				for($i=0; $i<$diff; $i++){
					$tempStr .= "&ensp;";
				}
			}
		
			return $tempStr;
		}

		public function getDB($year){
			if($year >= "2017"){
				$db = 'citydoc' . $year;
			}else{
				$db = 'citydoc';
			}
			return $db;
		}

		public function getDBTableCols($dbname, $table){
			$sql = "SELECT GROUP_CONCAT(column_name SEPARATOR ',') as columns FROM information_schema.columns where table_schema = '".$dbname."' and table_name = '".$table."'";
			$result = $this->query($sql);
			$columns = $this->fetch_array($result);
			$columns = $columns['columns'];

			return $columns;
		}

		public function stringifyForDB($record, $columns, $tnType, $newTN, $primPR){
			$insert = "";
			while($data = $this->fetch_array($record)){
				$vals = "";
				for ($i=0; $i < sizeof($columns); $i++) { 
					if($columns[$i] == "TrackingNumber"){
						$vals .= ",'".$newTN."'";
					}else if($columns[$i] == "PR_TrackingNumber" && $tnType == "PO"){
						$vals .= ",'".$primPR."'";
					}else{
						if($data[$columns[$i]] == ""){
							$vals .= ",null";
						}else{
							$vals .= ",'".addslashes($data[$columns[$i]])."'";
						}
					}
					
				}
				$insert .= ",(".substr($vals, 1).")";
			}

			return substr($insert, 1);
		}
		
		
		//-----------------------------------------------------------Login/Registers
		public function LoginUser($employeeNumber,$password){
			$sql = "SELECT *, a.Id as UserId   FROM 
					citydoc.users a inner join citydoc.employees b on
					a.employeenumber = b.employeenumber
					inner join citydoc.office c  on
  					b.officeCode = c.code
					where a.employeenumber = '" . $employeeNumber . "' and a.password = '" . $password . "' LIMIT 1";
			$result = $this->query($sql);
			return $result;
		}
		public function FindEmployee($employeeNumber,$lastname){
			$sql = "SELECT * FROM citydoc.employees WHERE employeeNumber='" . $employeeNumber . "' and LastName = '" . $lastname . "' LIMIT 1";
			return $this->query($sql);
		}
		public function InsertToUsers($employeeNumber,$password){
			$sql = "Insert into citydoc.users(EmployeeNumber,Password) values ('" . $employeeNumber . "','" . $password . "')";
			return $this->query($sql);
		}
		public function FindUser($employeeNumber){
			$sql = "SELECT * FROM citydoc.users WHERE employeeNumber='" . $employeeNumber . "' LIMIT 1";
			return $this->query($sql);
		}
		public function FindRegistration($employeeNumber){
			$sql = "SELECT * FROM citydoc.users WHERE employeeNumber='" . $employeeNumber . "' LIMIT 1";
			return  $this->query($sql);
		}
		public function UpdatePassword($employeeNumber,$password){
			$sql = "UPDATE citydoc.users SET password = '". $password ."', ResetState = '0' WHERE employeeNumber = '". $employeeNumber ."' LIMIT 1";
			$this->query($sql);
		}
		
		
		public function InsertRegistration($employeeNumber,$password,$dateRegistered){
			$sql = "Insert into citydoc.users (EmployeeNumber,Password,DateRegistered) values ('" . $employeeNumber . "', '" . $password ."','" . $dateRegistered . "')";
			$this->query($sql);
		}
		
		/*public function LoginUser($employeeNo,$password){
			$sql = "SELECT * FROM users a, employees b 
					WHERE a.employeeNumber='" . $employeeNo . "' and  a.password = '" . $password . "' 
					and a.employeeNumber = b.employeeNumber
					LIMIT 1";
			return $this->query($sql);
		}*/
		
		//------------------------------------------------------------------Fund encoded
		
		public function GetEncodedByProgramCode($year,$office,$programCode){
			/*$sql = "SELECT a.Id,Year, OfficeCode,ProgramCode,Name,Approval,
					if(FundType = 'Personal Services','PS',if(FundType = 'Capital Outlay','CO',if(FundType = 'MOOE','MOOE','-'))) as Fund,
					AccountCode as Code,Title,DateEncoded as Encoded,Amount,Approval FROM  
					funds  a inner join fundtitles b on a.AccountCode = b.Code
					inner join programcode c on a.ProgramCode = c.Code
					where
					 a.Year = " . $year . " and a.OfficeCode = '" . $office . "' and a.ProgramCode = '" . $programCode . "' 
					
					order by Approval asc, ProgramCode asc, a.AccountCode asc";*/
					
			$sql = "SELECT a.Id,Year, OfficeCode,ProgramCode,a.Amount,a.Fund,
					
					AccountCode as Code,Title
					FROM  
					funds  a left join fundtitles b on a.AccountCode = b.Code
					
					where
					 a.OfficeCode = '" . $office . "' and a.ProgramCode = '" . $programCode . "' 
					OR
					 a.OfficeCode = 'LUMP' and a.ProgramCode = '" . $programCode . "' 
					order by b.Title asc";
			
			return $this->query($sql);
		
		}
		
		public function SaveEncodedFund($year,$office,$programCode,$code,$amount,$dateEncoded,$encodedBy){
			$sql = "Insert into funds (Year,OfficeCode,ProgramCode,FundType,AccountCode,Amount,DateEncoded,EncodedBy)VALUES 
					('" . $year . "','" . $office . "','" . $programCode . "',(SELECT Fund FROM fundtitles WHERE code = '" . $code . "'),'" . $code . "','" . $amount . "','" . $dateEncoded . "','" . $encodedBy . "')";
			return $this->query($sql);
		}
		public function SearchAccountCode($code){
			$sql = "select * from fundtitles where code = '" . $code . "'";
			return $this->query($sql);
		}
		
		public function UpdateEncodedFund($year,$office,$programCode,$code,$amount,$dateEncoded,$encodedBy){
			$sql = "Update funds set Year  = " . $year . ", OfficeCode = '" . $office . "', AccountCode = '" . $code . "',
											 
											 Amount = '" . $amount . "', DateEncoded = '" . $dateEncoded . "', Encodedby = '" . $encodedBy . "'
											     
											 where year = " . $year . " and ProgramCode = '" . $programCode . "' and OfficeCode = '" . $office . "' and AccountCode = '" . $code . "'";
			return $this->query($sql);
		}
		public function DeleteEncodedFund($year,$office,$programCode,$code){
			$sql = "Delete from funds where AccountCode = '" . $code . "' and Year = '" . $year . "' and OfficeCode = '" . $office . "' and ProgramCode = '" . $programCode . "'";
			return $this->query($sql);
		}
		
		
		public function GetEncodedFund($year,$office){
			$sql = "SELECT Year, OfficeCode,ProgramCode,
					if(FundType = 'Personal Services','PS',if(FundType = 'Capital Outlay','CO',if(FundType = 'MOOE','MOOE','-'))) as Fund,
					AccountCode as Code,Title,a.DateEncoded as Encoded,a.Amount FROM  funds  a inner join fundtitles b on a.AccountCode = b.Code
					where OfficeCode = '" . $office . "'
					order by a.id desc";	
			return $this->query($sql);
		}
		public function GetTotalFund($year,$office){
			$sql = "SELECT ProgramCode,if(FundType = 'Capital Outlay','CO',if(FundType = 'Personal Services','PS',if(FundType = 'MOOE','MOOE','-'))) 
					as FundType, sum(Amount) as Amount 
					FROM funds where year = '" . $year . "' and OfficeCode = '" . $office . "'  
					group by FundType, ProgramCode order by ProgramCode";	
			return $this->query($sql);
		}
		public function FindEncodedCode($year,$office,$programCode,$codes){
			$sql = "SELECT * FROM funds where Year = '" . $year . "' 
											   and OfficeCode = '" . $office . "' 
											   and ProgramCode  = '" . $programCode . "'
											   and AccountCode  = '" . $codes . "'
											   limit 1";
			return $this->query($sql);
		}
		public function GetEncodedByFund($year,$office,$programCode,$fund){
			/*$sql = "SELECT a.Id,Year, OfficeCode,
					if(FundType = 'Personal Services','PS',if(FundType = 'Capital Outlay','CO',if(FundType = 'MOOE','MOOE','-'))) as Fund,
					AccountCode as Code,Title,DateEncoded as Encoded,Amount,Approval FROM  funds  a inner join fundtitles b on a.AccountCode = b.Code
					where a.Year = '" . $year . "' and a.OfficeCode = '" . $office . "' and a.FundType = '" . $fund . "'
					order by a.id desc";*/
			$sql = "SELECT a.Id,Year, OfficeCode,ProgramCode,Name,Approval,
					if(FundType = 'Personal Services','PS',if(FundType = 'Capital Outlay','CO',if(FundType = 'MOOE','MOOE','-'))) as Fund,
					AccountCode as Code,Title,a.DateEncoded as Encoded,a.Amount,Approval FROM  
					funds  a inner join fundtitles b on a.AccountCode = b.Code
					inner join programcode c on a.ProgramCode = c.Code
					
					where a.Year = '" . $year . "' and a.OfficeCode = '" . $office . "' and a.ProgramCode = '" . $programCode . "' and a.FundType = '" . $fund . "'
					order by Approval asc, ProgramCode asc, a.id desc";
			return $this->query($sql);
		}
		public function GetEncodedByFundOrderCode($year,$office,$programCode,$fund){
			/*$sql = "SELECT a.Id,Year, OfficeCode,
					if(FundType = 'Personal Services','PS',if(FundType = 'Capital Outlay','CO',if(FundType = 'MOOE','MOOE','-'))) as Fund,
					AccountCode as Code,Title,DateEncoded as Encoded,Amount,Approval FROM  funds  a inner join fundtitles b on a.AccountCode = b.Code
					where a.Year = '" . $year . "' and a.OfficeCode = '" . $office . "' and a.FundType = '" . $fund . "'
					order by Code asc";*/
					
			$sql = "SELECT a.Id,Year, OfficeCode,ProgramCode,Name,Approval,
					if(FundType = 'Personal Services','PS',if(FundType = 'Capital Outlay','CO',if(FundType = 'MOOE','MOOE','-'))) as Fund,
					AccountCode as Code,Title,a.DateEncoded as Encoded,a.Amount,Approval FROM  
					funds  a inner join fundtitles b on a.AccountCode = b.Code
					inner join programcode c on a.ProgramCode = c.Code
					where a.Year = '" . $year . "' and a.OfficeCode = '" . $office . "' and a.ProgramCode = '" . $programCode . "' and a.FundType = '" . $fund . "'
					order by Approval asc, ProgramCode asc, a.id desc";
			return $this->query($sql);
		}
		public function GetEncodedByOffice($year,$office){
			$sql = "SELECT a.Id,Year, OfficeCode,ProgramCode,Name,Approval,
					if(FundType = 'Personal Services','PS',if(FundType = 'Capital Outlay','CO',if(FundType = 'MOOE','MOOE','-'))) as Fund,
					AccountCode as Code,Title,a.DateEncoded as Encoded,a.Amount,Approval FROM  
					funds  a inner join fundtitles b on a.AccountCode = b.Code
					inner join programcode c on a.ProgramCode = c.Code
					where a.Year = '" . $year . "' and a.OfficeCode = '" . $office . "'
					order by Approval asc, ProgramCode asc, a.id desc";
			return $this->query($sql);
		}
		public function GetEncodedByCondition($condition){
			$sql = "SELECT a.Id,Year, OfficeCode,ProgramCode,Name,Approval,
					if(FundType = 'Personal Services','PS',if(FundType = 'Capital Outlay','CO',if(FundType = 'MOOE','MOOE','-'))) as Fund,
					AccountCode as Code,Title,a.DateEncoded as Encoded,a.Amount,Approval FROM  
					funds  a inner join fundtitles b on a.AccountCode = b.Code
					inner join programcode c on a.ProgramCode = c.Code " . $condition . "
					
					order by Approval asc, ProgramCode asc, a.id desc";
			return $this->query($sql);
		}
		public function
		GetDefault(){
			$sql = "SELECT * FROM defaults";
			return $this->query($sql);
		}
		public function GetOffice(){
			$sql = "Select * from office where Doctrack > 0 or PR > 0 order by name asc";
			return $this->query($sql);
		}
		public function UpdateFundId($fundId,$value){
			$sql = "Update funds set Approval = '" . $value . "' where Id = '" . $fundId . "'";
			return $this->query($sql);
		}
		//----------------------------------------------------------------------------------------------------- view fund
		public function GetPrograms(){
			$sql = "Select * from programcode order by code asc ";
			return $this->query($sql);
		}	
		public function GetProgramCodesByOffice($year,$officeCode){
				
			/*$sql = "Select a.PR_ProgramCode as Code,b.Name, 
			       sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount,sum(JevAmount) as JevAmount
			       from vouchercurrent a join programcode b
			                on a.PR_ProgramCode = b.code
			                where a.year = " . $year . " and a.Office = '" . $officeCode . "'  and a.Status = 'CAO Released' and OBR_Number > 0
			       group by a.PR_ProgramCode          
			       order by a.PR_ProgramCode asc ";	*/
			$sql = "SELECT a.ProgramCode,b.Name,a.Amount as AnnualBudget, c.Amount as Obligated, c.JevTotal as Liquidated, (a.Amount - ifnull(c.Amount,0)) as Savings,   (c.Amount - ifnull(c.JevTotal,0)) as Unliquidated
					FROM 
					(select ProgramCode,sum(Amount) as Amount from funds where year = " . $year . " and OfficeCode = '" . $officeCode . "'  group by ProgramCode) a
					left join 
					programcode b on a.ProgramCode = b.Code
					left join
					(SELECT PR_ProgramCode, sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount, sum(JevAmount) as JevTotal FROM vouchercurrent where year =" . $year . " and Office = '" . $officeCode . "'
					 and obr_number > 0 group by PR_ProgramCode) c
					on a.ProgramCode = c.PR_ProgramCode
					order by a.ProgramCode asc";
			return $this->query($sql);
		}
		public function GetAllProgramCodes($year){
				
			/*$sql = "Select a.PR_ProgramCode as Code,b.Name, 
			       sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount,sum(JevAmount) as JevAmount
			       from vouchercurrent a join programcode b
			                on a.PR_ProgramCode = b.code
			                where a.year = " . $year . " and a.Status = 'CAO Released' and OBR_Number > 0
			       group by a.PR_ProgramCode          
			       order by a.PR_ProgramCode asc ";	*/
			$sql = "SELECT a.ProgramCode,b.Name,a.Amount as AnnualBudget, c.Amount as Obligated, c.JevTotal as Liquidated, (a.Amount - ifnull(c.Amount,0)) as Savings,   (c.Amount - ifnull(c.JevTotal,0)) as Unliquidated
					FROM 
					(select ProgramCode,sum(Amount) as Amount from funds where year = " . $year . " group by ProgramCode) a
					left join 
					programcode b on a.ProgramCode = b.Code
					left join
					(SELECT PR_ProgramCode, sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount, sum(JevAmount) as JevTotal FROM vouchercurrent where year =" . $year . "
					 and obr_number > 0 group by PR_ProgramCode) c
					on a.ProgramCode = c.PR_ProgramCode
					order by a.ProgramCode asc";
			return $this->query($sql);
		}
		
		//------------------------------------------------------------------------------------------------------------------------------------------ doctrack ADD
		
		public function FetchDoctrackID($field,$condition){
			$sql = "Select " . $field . " from office " . $condition;
			return $this->query($sql);
		}
		public function GetClaimType(){
			$sql = "Select * from type order by type asc";
			return $this->query($sql);
		}
		public function FetchPPMP($period,$officeCode){
			$sql = "Select * from bacdb2017.ppmp a  inner join bacdb2017.item_categories b on a.CategoryCode = b.CategoryKey 
					where a.Period = '" . $period . "' and  a.officeCode = '" . $officeCode . "'
					group by a.CategoryCode";
			return $this->query($sql);
		}
		public function FetchCategoryItemsPPMP($period,$office,$category,$month,$programCode){
			$sql = "Select *,b.Quantity as Qty from bacdb2017.ppmp a inner join bacdb2017.milestone_activities b
					on a.ppmpId = b.ppmpId 
					where Period = '" . $period . "' 
					and OfficeCode = '" . $office . "' 
					and CategoryCode = '" . $category . "'
					and ProgramCode = '" . $programCode . "'
					and a.DeleteStatus = 0
					and " . $month . " > 0
					";
			return $this->query($sql);
		}
		public function FetchCategoryItemsPPMPallFunds($period,$office,$category,$month){
			$sql = "Select *,b.Quantity as Qty from bacdb2017.ppmp a inner join bacdb2017.milestone_activities b
					on a.ppmpId = b.ppmpId 
					where Period = '" . $period . "' 
					and OfficeCode = '" . $office . "' 
					and CategoryCode = '" . $category . "'
					
					and a.DeleteStatus = 0
					and " . $month . " > 0
					order by a.ProgramCode
					";
			return $this->query($sql);
		}
		public function FetchonPRrecord($year,$trackingNumber){
			$sql = "SELECT a.TrackingNumber,a.TrackingType,
					a.Amount as TotalAmount,a.TotalAmountMultiple,a.ChargeType,
					a.PR_ProgramCode,a.PR_Number,a.OBR_Number,a.PR_AccountCode,a.Fund,a.PR_Month,a.PR_CategoryCode,
					a.OBR_Number, 
					
					b.* 
					FROM vouchercurrent a right join  prrecord b  on a.TrackingNumber = b.TrackingNumber
					where a.TrackingNumber = '" . $trackingNumber . "' and a.Year = '" . $year . "' group by b.ProgramCode,b.Description
					";
			return $this->query($sql);
		}
		public function FetchonPRrecordForPRForm($year,$trackingNumber){
			/*$sql = "select s.Trackingnumber,sum(s.Qty) as Qty, s.Unit, s.Description,s.Amount as Cost, sum(s.Total) as Total,s.ProgramCode,s.PR_AccountCode,
		       			s.Month,s.CategoryCode,s.Year as Period
					from
					
					(SELECT b.*, a.PR_Month as Month,a.PR_CategoryCode as CategoryCode,a.Year,a.PR_AccountCode
					FROM vouchercurrent a left join  prrecord b
					on a.TrackingNumber = b.TrackingNumber
					where a.Year = '" . $year . "' and  a.trackingnumber = '" . $trackingNumber . "'  group by b.ProgramCode,  b.Description, b.Unit )  s
					
					group by s.Description order by  s.Description asc";*/
					
				/*$sql = "select s.programCode,s.Description,s.Unit, s.Trackingnumber,sum(s.Qty) as Qty, s.Unit, s.Description, s.Amount as Cost, sum(s.Total) as Total,s.ProgramCode,s.PR_AccountCode,
								       			s.Month,s.CategoryCode,s.Year as Period
											from
											(SELECT b.*, a.PR_Month as Month,a.PR_CategoryCode as CategoryCode,a.Year,a.PR_AccountCode
											FROM vouchercurrent a left join  prrecord b
											on a.TrackingNumber = b.TrackingNumber
						                    
											where a.Year = '" . $year . "'  and  a.trackingnumber = '" . $trackingNumber . "'  group by b.ProgramCode,  b.Description,  b.Unit order by b.ProgramCode asc  )  s
						                                         group by s.Description,s.Unit
											order by  s.Description asc";*/
											
				$sql = " select b.*,a.PR_CategoryCode as CategoryCode,a.PR_AccountCode,a.Period,a.Month from 
						(SELECT TrackingNumber, PR_CategoryCode, PR_AccountCode,PR_Month as Month, Year as Period  from vouchercurrent where 
						TrackingNumber =  '" . $trackingNumber . "'  and Year = '" . $year  .  "' group by trackingnumber) a
						left join
						(SELECT TrackingNumber,ProgramCode,sum(Qty) as Qty,Unit,Amount as Cost,sum(Total) as Total,Description
						FROM prrecord  where trackingnumber =  '" . $trackingNumber . "'   group by  Description, Unit order by Description asc) b
						on a.TrackingNumber = b.TrackingNumber";
						
			return $this->query($sql);
		}
		public function FetchCategoryItemsPPMPonPO($year,$trackingNumber){
			/*$sql = "SELECT a.TrackingNumber,a.PR_ProgramCode,a.PR_AccountCode,a.PR_Month,a.PR_CategoryCode, b.* FROM vouchercurrent a right join  porecord b  on a.TrackingNumber = b.TrackingNumber
					where a.TrackingNumber = '" . $trackingNumber . "' and a.Year = '" . $year . "'
					";*/
			$sql = "SELECT a.TrackingNumber,a.TrackingType,
					a.PR_ProgramCode,a.PR_AccountCode,a.PR_Month,
					a.PR_CategoryCode, b.* FROM vouchercurrent a right join  porecord b  on a.TrackingNumber = b.TrackingNumber
					where a.TrackingNumber = '" . $trackingNumber . "' and a.Year = '" . $year . "' group by b.ProgramCode,b.Description
					";
			return $this->query($sql);
		}
		public function fetchAmisPOrecord($year,$trackingNumber){
			$sql = "SELECT * from amis.porecord where TrackingNumber = '". $trackingNumber ."' and year = '".$year."' group by ProgramCode,Description";
			return $this->query($sql);
		}
		public function FetchCategoryItemsPPMP1($period,$office,$category,$month,$prTrackingNumber){
			$sql = "Select *,b.Quantity as Qty from bacdb2017.ppmp a inner join bacdb2017.milestone_activities b
					on a.ppmpId = b.ppmpId 
					where Period = '" . $period . "' 
					and OfficeCode = '" . $office . "'
					and a.DeleteStatus = 0 
					and CategoryCode = '" . $category . "'
					and " . $month . " > 0
					";
			return $this->query($sql);
		}
		public function FetchApprovePR($defaultYear,$office){
			$sql = "Select a.*,b.CategoryName from vouchercurrent a inner join bacdb2017.item_categories b on a.PR_CategoryCode = b.CategoryKey
				    where a.Year = " . $defaultYear . " and a.Office = '" . $office . "' and a.Status = 'PR - CBO Released' group by a.TrackingNumber ";
			return $this->query($sql);
		}
		public function FetchManualApprovePR($defaultYear,$office){
			$sql = "Select a.*,b.CategoryName from vouchercurrent a left join bacdb2017.item_categories b on a.PR_CategoryCode = b.CategoryKey
				    where a.Year = " . $defaultYear . " and a.Office = '" . $office . "' and a.Status = 'PR - CBO Released' group by a.TrackingNumber ";
			return $this->query($sql);
		}
		public function SaveTrackingPR($defaultYear,$office,$trackType,$trackingNumber,$prProgramCode,$prAccountCode,$prTotal,$itemCategory,$prMonth,$employeeNumber,$dateEncoded,$status){
			$sql = "Insert into vouchercurrent (Year,Office,TrackingType,PR_ProgramCode,PR_AccountCode,Amount,TrackingNumber,PR_CategoryCode,PR_Month,EncodedBy,DateEncoded,Status,DateModified,Fund)
					VALUES 
					(" . $defaultYear . ",'" . $office . "','" . $trackType . "','" . $prProgramCode . "','" . $prAccountCode . "','" . $prTotal . "','" . $trackingNumber . "'
					,'" . $itemCategory . "','" . $prMonth . "','" . $employeeNumber . "','" . $dateEncoded . "','" . $status . "','" . $dateEncoded . "','General Fund')";
			$this->query($sql);
		}
		public function SaveTrackingManualPR($defaultYear,$office,$trackType,$trackingNumber,$prProgramCode,$prAccountCode,$prTotal,$itemCategory,$prMonth,$employeeNumber,$dateEncoded,$status,$fund,$prNumber,$remarks){
			$sql = "Insert into vouchercurrent (Year,Office,TrackingType,PR_ProgramCode,PR_AccountCode,Amount,TrackingNumber,PR_CategoryCode,PR_Month,EncodedBy,DateEncoded,Status,DateModified,Fund,PR_Number,Remarks)
					VALUES 
					(" . $defaultYear . ",'" . $office . "','" . $trackType . "','" . $prProgramCode . "','" . $prAccountCode . "','" . $prTotal . "','" . $trackingNumber . "'
					,'" . $itemCategory . "','" . $prMonth . "','" . $employeeNumber . "','" . $dateEncoded . "','" . $status . "','" . $dateEncoded . "','" . $fund . "','" . $prNumber . "','" . $remarks . "')";
			$this->query($sql);
		}
		public function SaveTrackingPO($defaultYear,$office,$trackType,$trackingNumber,$supplier,$prTrackingNumber,$prProgramCode,$prAccountCode,$poTotal,$obrTotal,$itemCategory,$prMonth,$prNumber,$obrNumber,$employeeNumber,$dateEncoded,$status,$dateModified){
	       $sql = "Insert into vouchercurrent (Year,Office,TrackingType,TrackingNumber,Claimant,PR_TrackingNumber,PR_ProgramCode,PR_AccountCode,PO_Amount,Amount,PR_CategoryCode,PR_Month,PR_Number,OBR_Number,EncodedBy,DateEncoded,Status,DateModified,OBR_Approve)
					VALUES 
					(" . $defaultYear . ",'" . $office . "','" . $trackType . "','" . $trackingNumber  . "','" .  $supplier . "','" .  $prTrackingNumber . "','" . $prProgramCode . "','" . $prAccountCode . "','" . $poTotal . "','" . $obrTotal . "'
					,'" . $itemCategory . "','" . $prMonth . "','" . $prNumber . "','" . $obrNumber . "','" . $employeeNumber . "','" . $dateEncoded . "','" . $status . "','" . $dateEncoded . "',1)";
			$this->query($sql);
		}
		public function SaveTrackingMultiplePO($insertCase){
	       $sql = "Insert into vouchercurrent (Year,Office,TrackingType,TrackingNumber,Claimant,PR_TrackingNumber,
		                                       PR_ProgramCode,PR_AccountCode,PO_Amount,Amount,PR_CategoryCode,
											   PR_Month,PR_Number,OBR_Number,EncodedBy,DateEncoded,Status,DateModified,
											   OBR_Approve,ChargeType,TotalAmountMultiple,Fund,ClaimType,TransactionType)VALUES " . $insertCase; 
			$this->query($sql);
		}
		public function SaveTrackingManualPO($insertCase){
	       $sql = "Insert into vouchercurrent (Year,Office,Fund,TrackingType,TrackingNumber,Claimant,PR_TrackingNumber,
		                                       PR_ProgramCode,PR_AccountCode,PO_Amount,Amount,PR_CategoryCode,
											   PR_Month,PR_Number,OBR_Number,EncodedBy,DateEncoded,
											   Status,DateModified,OBR_Approve,ChargeType,TotalAmountMultiple,Remarks,TransactionType,ClaimType)VALUES " . $insertCase; 
			$this->query($sql);
		}
		public function SaveTrackingPY($defaultYear,$office,$trackType,$claimType,$claimant,$fund,$transtype,$trackingNumber,$program,$accountCode,$amount,$periodType,$periodValue,$employeeNumber,$dateEncoded,$status){
			$sql = "Insert into vouchercurrent (Year,Office,TrackingType,ClaimType,Claimant,Fund,TransactionType,TrackingNumber,PR_ProgramCode,PR_AccountCode,Amount,PeriodType,PeriodMonth,EncodedBy,DateEncoded,Status,DateModified)
					VALUES 
					(" . $defaultYear . ",'" . $office . "','" . $trackType . "','" . $claimType . "','" . $claimant . "','" . $fund . "','" . $transtype . "','" . $trackingNumber . "','" . $program . "','" . $accountCode . "'
					,'" . $amount . "','" . $periodType . "','" . $periodValue . "','" . $employeeNumber . "','" . $dateEncoded . "','" . $status . "','" . $dateEncoded . "')";
			$this->query($sql);
			
		}
		public function SaveTrackingPYMultiple($insertCase){
			$sql = "Insert into vouchercurrent (Year,Office,TrackingType,ClaimType,Claimant,Fund,TransactionType,TrackingNumber,
			                                    PR_ProgramCode,PR_AccountCode,Amount,PeriodType,PeriodMonth,EncodedBy,DateEncoded,Status,DateModified,ChargeType,TotalAmountMultiple)VALUES " . $insertCase; 
			$this->query($sql);
		}
		public function SaveTrackingPRMultiple($insertCase){
			$sql = "Insert into vouchercurrent (Year,Office,TrackingType,ClaimType,Claimant,Fund,TransactionType,TrackingNumber,
			                                    PR_ProgramCode,PR_AccountCode,Amount,PeriodType,PeriodMonth,EncodedBy,DateEncoded,Status,DateModified,ChargeType,TotalAmountMultiple,PR_CategoryCode,PR_Month)VALUES " . $insertCase; 
			$this->query($sql);
		}
		public function SaveTrackingManualPRMultiple($insertCase){
			$sql = "Insert into vouchercurrent (Year,Office,TrackingType,ClaimType,Claimant,Fund,TransactionType,TrackingNumber,
			                                    				PR_ProgramCode,PR_AccountCode,Amount,PeriodType,PeriodMonth,EncodedBy,DateEncoded,
												Status,DateModified,ChargeType,TotalAmountMultiple,PR_CategoryCode,PR_Month,PR_Number,Remarks)VALUES " . $insertCase; 
			$this->query($sql);
		}
												
		public function SaveTrackingPYSLP2($insertCase){
			$sql = "Insert into vouchercurrent (Year,Office,TrackingType,ClaimType,Claimant,Fund,DocumentType,TrackingNumber,Amount,PeriodType,PeriodMonth,EncodedBy,DateEncoded,Status,DateModified,ChargeType,TrackingPartner,PayeeNumber,ADV1,NetAmount)
					VALUES " . $insertCase; 
			$this->query($sql);
		}
		public function IncrementTrackingSeries($updateCase,$office){
			$sql = "Update office set " . $updateCase .  "  where code = '" . $office . "'";
			$this->query($sql);
		}

		public function IncrementTrackingSeriesRTK($receivDB,$updateCase,$office){
			$sql = "Update ".$receivDB.".office set " . $updateCase .  "  where code = '" . $office . "'";
			$this->query($sql);
		}
		
		public function InsertToPOrecord($insertThis){
			$sql = "Insert into porecord (TrackingNumber,Qty,Unit,Description,Amount,Total,ProgramCode)VALUES " . $insertThis  ;   
			return $this->query($sql);
		}
		public function InsertToPRrecord($insertThis){
			$sql = "Insert into prrecord (TrackingNumber,Qty,Unit,Description,Amount,Total,ProgramCode)VALUES " . $insertThis  ;   
			return $this->query($sql);
		}
		public function InsertToManualPRrecord($insertThis){
			$sql = "Insert into prrecord (TrackingNumber,Unit,Description,Qty,Amount,Total,ProgramCode)VALUES " . $insertThis  ;   
			return $this->query($sql);
		}
		
		public function LoadProgramPPMP($year,$office){
			$sql = "Select ProgramCode from bacdb2017.ppmp where OfficeCode = '" . $office . "' and Period = '" . $year . "' group by ProgramCode order by ProgramCode";
			return $this->query($sql);
		}
		public function LoadProgramFundsByOffice($year,$office){
			//$sql = "Select * from funds where Year = '" . $year . "' and OfficeCode = '" . $office . "' group by ProgramCode";
			$sql = "Select a.ProgramCode as Code, b.Name from funds a inner join  programcode b on a.ProgramCode = b.Code
					where a.OfficeCode = '" . $office . "'
					or
					a.OfficeCode = 'LUMP'
					 group by a.ProgramCode";

			return $this->query($sql);
		}
		//--------------------------------------------------------------------------------------------------------------------------------------------- doctrack Update
		
		public function SearchByTrackingNumber($trackingNumber){
			$sql = "SELECT x.Title,a.*,c.Name as OfficeName,d.LastName,d.FirstName,d.MiddleName, e.Name as Program,f.CategoryName FROM 
					vouchercurrent a left join office c on a.Office = c.Code
          			left join fundtitles x on a.PR_AccountCode = x.Code           
					inner join citydoc.employees d on a.EncodedBy = d.EmployeeNumber
					left join programcode e on a.PR_ProgramCode = e.Code
					left join bacdb2017.item_categories f on a.PR_CategoryCode = f.CategoryKey
					where a.TrackingNumber = '" . $trackingNumber . "' group by PR_ProgramCode,PR_AccountCode";
			return $this->query($sql);
		}
		public function SearchByManualTrackingNumber($trackingNumber){
			$sql = "SELECT x.Title,a.*,c.Name as OfficeName,d.LastName,d.FirstName,d.MiddleName, e.Name as Program,f.description FROM 
					vouchercurrent a left join office c on a.Office = c.Code
          			left join fundtitles x on a.PR_AccountCode = x.Code           
					inner join citydoc.employees d on a.EncodedBy = d.EmployeeNumber
					left join programcode e on a.PR_ProgramCode = e.Code
					left join prrecord f on a.TrackingNumber = f.trackingnumber
					where a.TrackingNumber = '" . $trackingNumber . "' group by PR_ProgramCode,PR_AccountCode";
			return $this->query($sql);
		}
		public function UpdateTrackingStatus($updateCase,$trackingNumber,$trackingyear){
			$sql = "Update citydoc$trackingyear.vouchercurrent set " . $updateCase .  "  where TrackingNumber = '" . $trackingNumber . "'";
			$this->query($sql);
		}
		public function UpdateTrackingStatusIn($updateCase,$trackingNumber){
			$sql = "Update vouchercurrent set " . $updateCase .  "  where TrackingNumber in (" . $trackingNumber . ")";
			$this->query($sql);
		}
		public function UpdateTrackingStatus2018($updateCase,$trackingNumber,$db){
			$sql = "Update " . $db . "vouchercurrent set " . $updateCase .  "  where TrackingNumber = '" . $trackingNumber . "'";
			$this->query($sql);
		}

		public function UpdateTrackingStatusRemote($updateCase,$trackingNumber,$db){
			$sql = "Update ".$db.".vouchercurrent set " . $updateCase .  "  where TrackingNumber = '" . $trackingNumber . "'";
			$this->query($sql);
		}

		public function UpdateTrackingStatusLocal($updateCase,$trackingNumber,$year){
			$sql = "UPDATE bacfiles.prmain SET " . $updateCase .  "  WHERE Year = '".$year."' AND TrackingNumber = '" . $trackingNumber . "'";
			$this->query($sql);
		}


		public function uploadArchivesPDF($DB, $insertCase){ // Inventory - Acceptance & Inspection Report
			$sql = "Insert " . $DB . "archives(PO_Number,Subject,filename,year,month,UploadedBy,DateUploaded) values(" . $insertCase . "')"; 
			$this->query($sql);
		}

		public function UpdateAMISVoucherCurrentTrackingStatus($DB, $updateCase,$trackingNumber){ // Inventory - Acceptance & Inspection Report
			$sql = "Update " . $DB . "amisvoucher set " . $updateCase .  "  where TrackingNumber = '" . $trackingNumber . "'";
			$this->query($sql);
		}


		public function insertAIRQuery($DB, $insertCase){ // Inventory - Acceptance & Inspection Report
			$sql = "Insert " . $DB . "invoicerecord(PO_Number,PODateReceive,InvoiceDate, Encodedby, DateEncoded) values(" . $insertCase . "')"; 
			$this->query($sql);
		}

		public function FetchInvoiceID($DB, $field){ // Inventory - Acceptance & Inspection Report
			$sql = "Select ". $field . " as InvoiceId from  " . $DB . "invoicerecord ";
			return $this->query($sql);
		}

		public function searchAmisPODetailsRecord($trackingNumber){ // Inventory
		$sql = "Select * from amis.porecord where trackingnumber ='" . $trackingNumber . "' order by ProgramCode,Description asc";
		return $this->query($sql);
		}


		public function searchAmisInspection_Report($trackingNumber){ // Inventory
		$sql = "Select * from amis.inspection_report where trackingnumber ='" . $trackingNumber . "' order by ProgramCode,Description asc";
		return $this->query($sql);
		}
	
		public function searchTrackingNumberOnInventory($trackingnumber){ // inventory
		$sql = "SELECT a.*,b.Name,c.LastName,c.FirstName,c.MiddleName,d.Name as ProgramName,e.Title,g.ARNumber,f.Description as  CategoryName
							FROM vouchercurrent a 
							left join office b on a.office = b.code  
							left join citydoc.employees c on a.encodedby = c.employeenumber
							left join programcode d on a.PR_ProgramCode = d.code
							left join fundtitles e on a.PR_AccountCode = e.Code
							left join ppmpcategories f on a.PR_CategoryCode = f.Code
							left join amis.invoicerecord g on a.PO_Number = g.PO_Number
							where a.TrackingNumber = '" . $trackingnumber . "'
							order by a.pr_programcode,a.pr_accountcode";
		return $this->query($sql);
		}

		public function searchTrackingNumber2022($trackingNumber,$trackingyear) {

			$sql = "SELECT * FROM citydoc$trackingyear.vouchercurrent WHERE TrackingNumber = '".$trackingNumber."'";
			if($_SESSION['accountType'] == 1){
				$office = $this->charEncoder($_SESSION['cbo']);
				$sql = "SELECT * FROM vouchercurrent WHERE Office = '".$office."' AND TrackingNumber = '".$trackingNumber."'";
			}

			$record = $this->query($sql);
			$numRows = $this->num_rows($record);
			$newRecord = [];

			if ($numRows > 0) {
				$grp = '';
				$logOffice = $_SESSION['gso'];
				$acct = $_SESSION['accountType'];
				if($_SESSION['accountType'] >= 2){
					$className = 'label19';
				}else{
					$className = 'hide';	
				}

				$codes = [];
				$prgCodes = '';
				$accCodes = '';
				$cnt = 0;
				while($data = $this->fetch_array($record)) {
					if($cnt == 0) {
						$newRecord['TrackingNumber'] = $data['TrackingNumber'];
						$newRecord['TrackingType'] = $data['TrackingType'];
						$newRecord['Status'] = $data['Status'];
						$newRecord['Year'] = $data['Year'];
						$newRecord['Office'] = $data['Office'];
						$newRecord['TrackingPartner'] = $data['TrackingPartner'];
						$newRecord['PR_TrackingNumber'] = $data['PR_TrackingNumber'];
						$newRecord['PR_Number'] = $data['PR_Number'];
						$newRecord['PO_Number'] = $data['PO_Number'];
						$newRecord['OBR_Number'] = $data['OBR_Number'];
						$newRecord['PR_Month'] = $data['PR_Month'];
						$newRecord['ControlNo'] = $data['ControlNo'];
						$newRecord['PeriodType'] = $data['PeriodType'];

						$netAmount = $data['NetAmount'];
						if($data['PeriodType'] == 2){
							$netAmount = $data['Amount'];
						}
						$newRecord['NetAmount'] = $netAmount;

						$newRecord['PayeeNumber'] = $data['PayeeNumber'];
						$newRecord['Fund'] = $data['Fund'];

						$amount = $data['PO_Amount'];
						$total = $data['TotalAmountMultiple'];
						if($data['TrackingType'] == "PO"){
							if($total > 0){
								$poAmount = $total;
							}else{
								$poAmount = $amount;
							}
						}else{
							$amount = $data['Amount'];
							$poAmount = $amount;
						}

						$newRecord['TotalAmountMultiple'] = $total;
						$newRecord['PO_Amount'] = $poAmount;
						$newRecord['Amount'] = $amount;
						$newRecord['Remarks'] = $data['Remarks'];
						$newRecord['Remarks1'] = $data['Remarks1'];
						$newRecord['Claimant'] = $data['Claimant'];
						$newRecord['ClaimType'] = $data['ClaimType'];
						$newRecord['CheckNumber'] = $data['checknumber'];
						$newRecord['CheckDate'] = $data['checkdate'];
						$newRecord['DocumentType'] = $data['DocumentType'];

						$adv =  $data['ADV1'];
						$adv2 =  $data['ADV2'];
						if($adv < 1  ){
							$adv = '';
							if($acct == '2' ){
								if($newRecord['Status'] == "CBO Released"){
									$adv = '';
								}
								
							}
							if($acct == 4 ){
								$adv = '';
							}
							if($acct == 5 ){
								$adv = 99999;
								if($newRecord['Status'] == "CBO Received" || $newRecord['Status'] == "Encoded"){
										$adv = '0';
								}
							}
							if($acct == 9 ){
								if($newRecord['DocumentType'] == 'LTO COMPUTER FEE'){
									$adv = $data['ADV2'] . 'a' ;
								}
							}	
						}
						$newRecord['ADV'] = $adv;

						$newRecord['SubCode'] = $data['SubCode'];
						$newRecord['PeaceOfficeId'] = $data['PeaceOfficeId'];
						$newRecord['PeriodType'] = $data['PeriodType'];
						$newRecord['PeriodMonth'] = $data['PeriodMonth'];
						$newRecord['PR_CategoryCode'] = $data['PR_CategoryCode'];
						$newRecord['ChargeType'] = $data['ChargeType'];
						$newRecord['Complex'] = $data['Complex'];
						$newRecord['DateEncoded'] = $data['DateEncoded'];
						$newRecord['DateModified'] = $data['DateModified'];
						$newRecord['Completion'] = $data['Completion'];
						$newRecord['ModeOfProcurement'] = $data['ModeOfProcurement'];
						$newRecord['BatchTracking'] = $data['BatchTracking'];
						$newRecord['EmployeeNumber'] = $data['EncodedBy'];
						$newRecord['CategoryName'] = '';
						$newRecord['PR_ProgramCode'] = $data['PR_ProgramCode'];	
						$newRecord['ConformDate'] = $data['ConformDate'];	
						$newRecord['NatureOfPayment'] = $data['NatureOfPayment'];	
						$newRecord['PaymentTerm'] = $data['PaymentTerm'];	

						$newRecord['Specifics'] = $data['Specifics'];
						if($newRecord['Specifics'] == 'Combi') {
							$newRecord['Specifics'] = 'Agricultural products & other Goods/Items';
						}	

						$newRecord['CAOOfficer'] = $data['CAOOfficer'];
						$newRecord['GSOValidator'] = $data['GSOValidator'];
						$newRecord['DRRMO'] = $data['ProjectId'];

					}

					$cnt++;

					$prg = $data['PR_ProgramCode'];
					$acc = $data['PR_AccountCode'];
					$amount = $data['PO_Amount'];
					$total = $data['TotalAmountMultiple'];
					if($newRecord['TrackingType'] == "PO"){
						if($total > 0){
							$poAmount = $total;
						}else{
							$poAmount = $amount;
						}
					}else{
						$amount = $data['Amount'];
						$poAmount = $amount;

						// 2023-07-12 - Gi-add kay mag-error sa SINGLE records na wlay TotalAmountMultiple
						if(floatval($total) == 0) {
							$total = $amount;
						}

					}

					if($prg != "") {
						$codes[$prg][$acc] = $prg . '~' . $acc . '~' . $amount . '~' . $total;
						$prgCodes .= ",'".$prg."'";
						$accCodes .= ",'".$acc."'";
					}
				}

				unset($data);

				$sql = "SELECT * FROM office WHERE Code = '".$newRecord['Office']."' LIMIT 1";
				$record = $this->query($sql);
				$data = $this->fetch_array($record);
				$newRecord['OfficeName'] = $data['Name'];

				unset($data);

				$sql = "SELECT LastName, FirstName, MiddleName FROM citydoc.employees WHERE EmployeeNumber = '".$newRecord['EmployeeNumber']."' LIMIT 1";
				$record = $this->query($sql);
				$data = $this->fetch_array($record);
				$newRecord['EncodedBy'] = utf8_encode($data['FirstName'] . ' ' . $data['MiddleName'] . ' ' . $data['LastName']);
				// $newRecord['EncodedBy'] = mb_convert_encoding($data['FirstName'] . ' ' . $data['MiddleName'] . ' ' . $data['LastName'], 'UTF-8', 'ISO-8859-1');

				unset($data);
				$grp = '';

				$forDRRMO = 0;
				if($prgCodes != "") {
					$prgNames = [];
					$sql = "SELECT Code, Name FROM programcode WHERE Code IN (".substr($prgCodes, 1).")";
					$record = $this->query($sql);
					while($data = $this->fetch_array($record)) {
						$code = $data['Code'];
						$name = $data['Name'];

						$prgNames[$code] = $name;

						if($code == '9940' || $code == '9940-A' || $code == '9940-30') {
							$forDRRMO = 1;
						}

					}

					unset($data);

					$accNames = [];
					$sql = "SELECT Code, Title FROM fundtitles WHERE Code IN (".substr($accCodes, 1).")";
					$record = $this->query($sql);
					while($data = $this->fetch_array($record)) {
						$code = $data['Code'];
						$title = $data['Title'];

						$accNames[$code] = $title;
					}

					unset($data);

					foreach ($codes as $progCode => $waccCodes) {
						foreach ($waccCodes as $accCode => $grpPart1) {
							// $grp .= $grpPart1.'~'.$prgNames[$progCode].'~'.$accNames[$accCode].'*';
							$prgName1 = '';
							if(isset($prgNames[$progCode])) {
								$prgName1 = $prgNames[$progCode];
							}
							$grp .= $grpPart1.'~'.$prgName1.'~'.$accNames[$accCode].'*';
						}
					}

					unset($codes);
					unset($prgNames);
					unset($accNames);	
				}

				$newRecord['GRP'] = $grp;

				if($newRecord['DRRMO'] > 0) {
					$forDRRMO = 1;
				}

				$newRecord['ForDRRMO'] = 0;
				if($forDRRMO == 1) {
					$newRecord['ForDRRMO'] = 1;
					if($newRecord['DRRMO'] != "") {
						// $sql = "SELECT * FROM drrmoprojects WHERE Id = '".$newRecord['DRRMO']."' LIMIT 1";
						$sql = "SELECT * FROM disasterprojects WHERE Id = '".$newRecord['DRRMO']."' LIMIT 1";
						$record = $this->query($sql);
						$data = $this->fetch_array($record);
						$newRecord['DRRMO'] = $data['Name'];
					}
				}


				if(strlen(trim($newRecord['CAOOfficer'] ?? '')) > 0) {
					$sql = "SELECT * FROM inframanpower WHERE EmployeeNumber = '".$newRecord['CAOOfficer']."' LIMIT 1";
					$record = $this->query($sql);
					$data = $this->fetch_array($record);

					$newRecord['CAOOfficerName'] = $data['LastName'].', '.$data['FirstName'].' '.substr($data['MiddleName'], 0, 1).'.';
				}else {
					$newRecord['CAOOfficerName'] = '';
				}

				if(strlen(trim($newRecord['GSOValidator'] ?? '')) > 0) {
					$sql = "SELECT * FROM inframanpower WHERE EmployeeNumber = '".$newRecord['GSOValidator']."' LIMIT 1";
					$record = $this->query($sql);
					$data = $this->fetch_array($record);

					$newRecord['GSOValidatorName'] = $data['LastName'].', '.$data['FirstName'].' '.substr($data['MiddleName'], 0, 1).'.';
				}else {
					$newRecord['GSOValidatorName'] = '';
				}
				

				if($newRecord['TrackingType'] == "PO"){
					
					$newRecord['PR_Data'] = urlencode($newRecord['Year'] . '~' . $newRecord['Office'] . '~' . $newRecord['OfficeName'] . '~' . $newRecord['PR_CategoryCode'] .'~' . $newRecord['PR_Month'] . '~' . $newRecord['CategoryName'] . '~' . $newRecord['PR_TrackingNumber']);	
					
					// $sql = "SELECT * FROM supplier.supplierinfo WHERE Name = \"". utf8_encode($newRecord['Claimant']) ."\" OR Alias = \"". utf8_encode($newRecord['Claimant']) ."\" LIMIT 1";
					// $sql = "SELECT * FROM supplier.supplierinfo WHERE Name = \"". utf8_decode($newRecord['Claimant']) ."\" OR Alias = \"". utf8_decode($newRecord['Claimant']) ."\" LIMIT 1";
					$sql = "SELECT * FROM supplier.supplierinfo WHERE Name = \"". $newRecord['Claimant'] ."\" OR Alias = \"". $newRecord['Claimant'] ."\" LIMIT 1";
					$record = $this->query($sql);
					$data = $this->fetch_array($record);
					$newRecord['SuppClassification'] = $data['Classification'];
					$newRecord['SuppType'] = $data['Type'];
					$newRecord['SuppTIN'] = $data['TIN'];
					$newRecord['SuppCode'] = $data['Code'];
		   
					if($newRecord['SuppType'] == 'NV') {
						$newRecord['SuppType'] = "NON-VAT";
					}else {
						$newRecord['SuppType'] = "VAT";
					}

					// $sql = "SELECT InvoiceNumber, InvoiceDate, PoDate FROM particulars WHERE TrackingNumber = '".$trackingNumber."' LIMIT 1";
					$sql = "SELECT InvoiceNumber, InvoiceDate, PoDate FROM particulars WHERE TrackingNumber = '".$trackingNumber."' LIMIT 1";
					// $sql = "SELECT * FROM particulars WHERE TrackingNumber = '".$trackingNumber."' LIMIT 1";
					$record = $this->query($sql);
					$data = $this->fetch_array($record);
					$newRecord['InvoiceNumber'] = $data['InvoiceNumber'];
					$newRecord['InvoiceDate'] = $data['InvoiceDate'];
					$newRecord['PoDate'] = $data['PoDate'];
					$newRecord['DeliveryDate'] = "";

					$newRecord['RetentionTN'] = $newRecord['TrackingPartner'];;


				}elseif($newRecord['TrackingType'] == 'PX'){

					// $sql = "SELECT * FROM supplier.supplierinfo WHERE Name = \"". utf8_encode($newRecord['Claimant']) ."\" LIMIT 1";
					$sql = "SELECT * FROM supplier.supplierinfo WHERE Name = \"". $newRecord['Claimant'] ."\" LIMIT 1";
					$record = $this->query($sql);

					if($this->num_rows($record) > 0) {
						$data = $this->fetch_array($record);
						$newRecord['SuppClassification'] = $data['Classification'];
						$newRecord['SuppType'] = $data['Type'];
						$newRecord['SuppTIN'] = $data['TIN'];
						$newRecord['SuppCode'] = $data['Code'];
					}else {
						$newRecord['SuppClassification'] = "";
						$newRecord['SuppType'] = "";
						$newRecord['SuppTIN'] = "";
						$newRecord['SuppCode'] = "";
					}
		   
					if($newRecord['SuppType'] == 'NV') {
						$newRecord['SuppType'] = "NON-VAT";
					}elseif($newRecord['SuppType'] == 'V') {
						$newRecord['SuppType'] = "VAT";
					}
		   
					$sql = "SELECT * FROM vouchercurrent WHERE TrackingNumber = '".$newRecord['TrackingPartner']."' LIMIT 1";
					$record = $this->query($sql);

					if($this->num_rows($record) > 0) {
						$data = $this->fetch_array($record);
						$newRecord['PO_Nature'] = $data['NatureOfPayment'];
						$newRecord['PO_Specifics'] = $data['Specifics'];
						$newRecord['PO_ModeOfProc'] = $data['ModeOfProcurement'];
						$newRecord['PO_PYTerm'] = $data['PaymentTerm'];
						$newRecord['PO_PRTN'] = $data['PR_TrackingNumber'];
						$newRecord['RetentionTN'] = $data['TrackingPartner'];
					}else {
						$newRecord['PO_Nature'] = '';
						$newRecord['PO_Specifics'] = '';
						$newRecord['PO_ModeOfProc'] = '';
						$newRecord['PO_PYTerm'] = '';
						$newRecord['PO_PRTN'] = '';
						$newRecord['RetentionTN'] = "";
					}

					$sql = "SELECT * FROM particulars WHERE TrackingNumber = '".$newRecord['TrackingPartner']."' LIMIT 1";
					$record = $this->query($sql);
					$data = $this->fetch_array($record);
					$newRecord['InvoiceNumber'] = $data['InvoiceNumber'];
					$newRecord['InvoiceDate'] = $data['InvoiceDate'];
					$newRecord['PoDate'] = $data['PoDate'];
					$newRecord['GasAccount'] = "";
					$newRecord['GasName'] = "";
					$newRecord['DeliveryDate'] = "";

					if($newRecord['Office'] == 'ONE1' && $newRecord['PO_Specifics'] == 'Gasoline') { 
						
						$sql = "SELECT * FROM particulars WHERE TrackingNumber = '".$newRecord['TrackingNumber']."' LIMIT 1";
						$record = $this->query($sql);
						$data = $this->fetch_array($record);
						$newRecord['InvoiceNumber'] = $data['InvoiceNumber'];
						$newRecord['InvoiceDate'] = $data['InvoiceDate'];
						$newRecord['GasAccount'] = $data['AccountNumber'];

						$sql = "SELECT * FROM gasaccounts WHERE AccountNumber = '".$newRecord['GasAccount']."' LIMIT 1";
						$record = $this->query($sql);
						$data = $this->fetch_array($record);
						if($this->num_rows($record) > 0) {
							$newRecord['GasName'] = $data['Office'];
						}else {
							$newRecord['GasName'] = "";
						}

					}

					$sql = "SELECT * FROM particulars WHERE TrackingNumber = '".$trackingNumber."' LIMIT 1";
					$record = $this->query($sql);
					$data = $this->fetch_array($record);

					$newRecord['Particulars'] = "";
					if(strlen(trim($data['Particulars'])) > 0) {
						$newRecord['Particulars'] = $data['Particulars'];
					}

					$newRecord['AccountNumber'] = $data['AccountNumber'];

					$newRecord['GasAccountName'] = "";
					if(strlen(trim($newRecord['AccountNumber'])) > 0) {
						$sql = "SELECT * FROM gasaccounts WHERE AccountNumber = '".$newRecord['AccountNumber']."' LIMIT 1";
						$record = $this->query($sql);
						$data = $this->fetch_array($record);
						$newRecord['GasAccountName'] = $data['Office'];
					}
					
					// $newRecord['RetentionTN'] = "";

   
					$newRecord['PR_Data'] = urlencode($newRecord['Year'] . '~' . $newRecord['Office'] . '~' . $newRecord['OfficeName'] . '~' . $newRecord['PR_CategoryCode'] .'~' . $newRecord['PR_Month'] . '~' . $newRecord['CategoryName'] . '~' . $newRecord['TrackingNumber']);

				}else{
					$newRecord['PR_Data'] = urlencode($newRecord['Year'] . '~' . $newRecord['Office'] . '~' . $newRecord['OfficeName'] . '~' . $newRecord['PR_CategoryCode'] .'~' . $newRecord['PR_Month'] 
														. '~' . $newRecord['CategoryName'] . '~' . $newRecord['TrackingNumber']);	
				}
			}else {
				echo "<div class='norecord'>
						<p>No record found.</p>
					  </div>";
			}
			
			return $newRecord;
		}

		public function UpdateVoucherHistory($trackingNumber,$trackingyear){
			$sql = "UPDATE citydoc$trackingyear.voucherhistory SET " . $this->completion("datemodified") . ", ".$this->completionPerHour("datemodified")." where id =  (select maxID from (SELECT MAX(Id)  MaxId FROM voucherhistory where TrackingNumber = '" . $trackingNumber . "') as t)";
			// echo $sql;
			$this->query($sql);
		}

		public function UpdateVoucherHistoryRemote($trackingNumber,$db){
			$sql = "UPDATE ".$db.".voucherhistory SET " . $this->completion("datemodified") . ", ".$this->completionPerHour("datemodified")." where id =  (select maxID from (SELECT MAX(Id)  MaxId FROM ".$db.".voucherhistory where TrackingNumber = '" . $trackingNumber . "') as t)";
			$this->query($sql);
		}

		public function UpdateVoucherHistoryRemote21Below($trackingNumber,$db){
			$sql = "UPDATE ".$db.".voucherhistory SET " . $this->completion("datemodified") . " where id =  (select maxID from (SELECT MAX(Id)  MaxId FROM ".$db.".voucherhistory where TrackingNumber = '" . $trackingNumber . "') as t)";
			$this->query($sql);
		}

		public function UpdateVoucherHistoryBatchRemote($db,$setTn){
			//update history
			$sql = "SELECT Max(Id) as MaxId   FROM " . $db . ".voucherhistory where TrackingNumber in (" . $setTn . ") group by TrackingNumber asc order by id desc";
			$record = $this->query($sql);
			$ids = '';
			while($data = $this->fetch_array($record)){
				$ids .= ',' .$data['MaxId'];
			}
			$ids = substr($ids,1, strlen($ids));
			$sql = "UPDATE " . $db . ".voucherhistory SET " . $this->completion("datemodified") . ", " .  $this->completionPerHour("datemodified") . " where id in (" . $ids . ")";
			$record = $this->query($sql);
		}

		public function InsertToVoucherHistoryBatchRemote($db,$insertValuesHistory){
			$sql = "INSERT INTO " . $db . ".voucherhistory (TrackingNumber,ModifiedBy,DateModified,Status) VALUES " . $insertValuesHistory . "";
			$this->query($sql);
		}

		public function UpdateVoucherHistoryIn($trackingNumber){
			//$sql = "UPDATE voucherhistory SET " . $this->completion("datemodified") . ", ".$this->completionPerHour("datemodified")." where id =  (select maxID from (SELECT MAX(Id)  MaxId FROM voucherhistory where TrackingNumber in (" . $trackingNumber . ")) as t)";
			$sql = "SELECT Max(Id) as MaxId   FROM voucherhistory where TrackingNumber in (" . $trackingNumber . ") group by TrackingNumber asc order by id desc";
			$record = $this->query($sql);
			$count = $this->num_rows($record);
			if($count >  1){
				$ids = '';
				while($data = $this->fetch_array($record)){
					$ids .= ',' .$data['MaxId'];
					
				}
				$ids = substr($ids,1);
				$sql = "UPDATE voucherhistory SET " . $this->completion("datemodified") . ", " .  $this->completionPerHour("datemodified") . " where id in (" . $ids . ")";
			}else{
				$sql = "UPDATE voucherhistory SET " . $this->completion("datemodified") . ", ".$this->completionPerHour("datemodified")." where id =  (select maxID from (SELECT MAX(Id)  MaxId FROM voucherhistory where TrackingNumber = " . $trackingNumber . ") as t)";
			}
			$record = $this->query($sql);
		}
		public function InsertToVoucherHistory($trackingNumber,$modifiedBy,$dateModified,$status,$completion,$trackingyear){
			if($completion != ''){
				$completion = $completion . " <span style = \'color:red\'>Total</span>";
			}else{
				$completion = '';
			}
			$sql = "Insert into citydoc$trackingyear.voucherhistory (TrackingNumber,ModifiedBy,DateModified,Status,Completion) values ('" . $trackingNumber . "','" . $modifiedBy . "','" . $dateModified . "','" . $status . "','" . $completion . "')";
			$this->query($sql);
		}
		public function InsertToVoucherHistoryIn($trackingNumber,$modifiedBy,$dateModified,$status,$completion){
			if($completion != ''){
				$completion = $completion . " <span style = \'color:red\'>Total</span>";
			}else{
				$completion = '';
			}
			$case = '';
		
			$arr = explode(',',$trackingNumber);	
			$size = sizeof($arr);
			if($size > 1){
				for($i  = 0; $i < $size; $i++){
					$case .= ",('" . $arr[$i] . "','" . $modifiedBy . "','" . $dateModified . "','" . $status . "','" . $completion . "')";
				}
				$case = substr($case,1);
				
				$sql = "Insert into voucherhistory (TrackingNumber,ModifiedBy,DateModified,Status,Completion) values " . $case;
			}else{
				$sql = "Insert into voucherhistory (TrackingNumber,ModifiedBy,DateModified,Status,Completion) values ('" . $trackingNumber . "','" . $modifiedBy . "','" . $dateModified . "','" . $status . "','" . $completion . "')";
			}
			$this->query($sql);
		}
		public function InsertToVoucherHistoryNew($trackingNumber,$modifiedBy,$dateModified,$status,$completion,$db){
			if($completion != ''){
				$completion = $completion . " <span style = \'color:red\'>Total</span>";
			}else{
				$completion = '';
			}
			$sql = "INSERT INTO ".$db.".voucherhistory (TrackingNumber,ModifiedBy,DateModified,Status,Completion) values ('" . $trackingNumber . "','" . $modifiedBy . "','" . $dateModified . "','" . $status . "','" . $completion . "')";
			$this->query($sql);
		}
		public function FetchByAdv($adv,$fund,$year){
			$sql = "SELECT Id,TrackingNumber FROM vouchercurrent where 
					ADV1 = '" . $adv . "' and Fund = '" . $fund . "' and Year = '" . $year . "' 
					or 
					ADV2 = '" . $adv . "' and Fund = '" . $fund . "' and Year = '" . $year . "'
					limit 1";
			return $this->query($sql);
		}
		public function CountControl($fund,$transaction,$date){
			if($fund == "GENERAL FUND"){
				$sql = "SELECT count(distinct controlno) as ctrl FROM vouchercurrent 
					WHERE  Fund = '" . $fund . "' and ClaimType = '" . $transaction . "'  and status = 'CAO Released' and substring(DateModified,1,10) = '" . $date . "' AND  ControlNo > 0";
			}else{
				$sql = "SELECT count(distinct controlno) as ctrl FROM vouchercurrent 
					    WHERE  Fund = '" . $fund . "' and status = 'CAO Released' and substring(DateModified,1,10) = '" . $date . "' AND  ControlNo > 0";
			}
			return $this->query($sql);
		}
		
		public function FindOBR($obrNo,$defaultYear){
			$sql = "Select TrackingNumber from vouchercurrent where Year = '" . $defaultYear . "' and OBR_Number = '" . $obrNo . "' limit 1";
			return $this->query($sql);
		}
		
		//------------------------------------------------------------------------------------------------ budget balance
		public function LoadAppropriationStatus($year,$officeCode){
			$sql = "select s.OfficeCode,s.ProgramCode,s.Name,s.FundName,s.Total as AB,t.Total as OBRTotal,sum(u.Amount) as PRAmount, sum(u.Total) as PaidAmount
				      from 
				          (SELECT Year, OfficeCode,ProgramCode,Name,FundType as FundName,if(FundType = 'Personal Services',3,if(FundType = 'Capital Outlay',1,if(FundType = 'MOOE',2,0))) as Fund,Title,sum(Amount) as Total          
				              FROM  
				              funds a inner join fundtitles b on a.AccountCode = b.Code
				                      inner join programcode c on a.ProgramCode = c.Code
				              where a.Year = '" . $year . "' and a.OfficeCode = '" . $officeCode . "' and Approval = 1
				                      group by ProgramCode,Fund) s  
				      left join
				          (SELECT  a.Office,a.PR_ProgramCode,if(b.FundType = 'Personal Services',3,if(b.FundType = 'Capital Outlay',1,if(b.FundType = 'MOOE',2,0))) as Fund, sum(a.Amount) as Total
				                FROM vouchercurrent 
				                    a inner join funds b on  CONCAT(a.PR_ProgramCode,a.PR_AccountCode) = CONCAT(b.ProgramCode,b.AccountCode)
				                    where a.Year = '" . $year . "' and a.Office = '" . $officeCode . "' and TrackingType != 'PO' and OBR_Approve = 1 and a.Fund = 'General Fund'
				                    group by a.PR_ProgramCode,Fund) t
				        on CONCAT(s.ProgramCode,s.Fund) = CONCAT(t.PR_ProgramCode,t.Fund)
				    left join
				          (SELECT  a.Office,a.PR_ProgramCode,if(b.FundType = 'Personal Services',3,if(b.FundType = 'Capital Outlay',1,if(b.FundType = 'MOOE',2,0))) as Fund,
				                   a.Amount, sum(a.PO_Amount) as Total
				                FROM vouchercurrent 
				                    a inner join funds b on  CONCAT(a.PR_ProgramCode,a.PR_AccountCode) = CONCAT(b.ProgramCode,b.AccountCode)
				                    where a.Year = '" . $year . "' and a.Office = '" . $officeCode . "' and PO_Amount != 0 and OBR_Approve = 1
				                    group by a.PR_ProgramCode,Fund,a.PR_TrackingNumber) u
				        
				         on CONCAT(s.ProgramCode,s.Fund) = CONCAT(u.PR_ProgramCode,u.Fund)    
				         
				    group by ProgramCode,FundName     
				    order by s.ProgramCode,FundName asc	
				  ";
		
			return $this->query($sql);
		}
		
		public function FetchAppropriationStatusByFund($year,$officeCode,$programCode,$fund){
			$sql = "select s.OfficeCode,s.ProgramCode,s.FundName,AccountCode,s.Title,s.Total as AB, t.Total as OBRTotal,sum(u.Total) as PR, sum(u.PO_Amount)as PO from 
            
			              (SELECT Year, OfficeCode,ProgramCode,Name,FundType as FundName,if(FundType = 'Personal Services',3,if(FundType = 'Capital Outlay',1,if(FundType = 'MOOE',2,0))) as Fund,
			                  Title,AccountCode,sum(Amount) as Total          
			                  FROM  
			                  funds a inner join fundtitles b on a.AccountCode = b.Code
			                          inner join programcode c on a.ProgramCode = c.Code
			                  where a.Year = '" . $year . "' and a.OfficeCode = '" . $officeCode . "' and Approval = 1
			                          group by ProgramCode,Fund,AccountCode) s 
			          inner join
			              (SELECT  a.Office,a.PR_ProgramCode,if(b.FundType = 'Personal Services',3,if(b.FundType = 'Capital Outlay',1,if(b.FundType = 'MOOE',2,0))) as Fund,
			                    a.TrackingType,a.PR_AccountCode, 
			                    sum(a.Amount) as Total,a.PR_TrackingNumber
			                    FROM vouchercurrent 
			                        a inner join funds b on  CONCAT(a.PR_ProgramCode,a.PR_AccountCode) = CONCAT(b.ProgramCode,b.AccountCode)
			                        where a.Year = '" . $year . "' and a.Office = '" . $officeCode . "'  and TrackingType != 'PO' and OBR_Approve = 1
			                        group by ProgramCode,Fund,AccountCode
			                       ) t
			                
			            on CONCAT(s.ProgramCode,s.Fund,s.AccountCode) = CONCAT(t.PR_ProgramCode,t.Fund,t.PR_AccountCode)
			         
			         left join
			              (SELECT  a.Office,a.PR_ProgramCode,if(b.FundType = 'Personal Services',3,if(b.FundType = 'Capital Outlay',1,if(b.FundType = 'MOOE',2,0))) as Fund,
			                    a.TrackingType,a.PR_AccountCode,sum(a.PO_Amount) as PO_Amount, 
			                    a.Amount as Total
			                    FROM vouchercurrent 
			                        a inner join funds b on  CONCAT(a.PR_ProgramCode,a.PR_AccountCode) = CONCAT(b.ProgramCode,b.AccountCode)
			                        where a.Year = '" . $year . "' and a.Office = '" . $officeCode . "' and TrackingType = 'PO'   and OBR_Approve = 1
			                        group by ProgramCode,Fund,AccountCode,PR_TrackingNumber
			                       ) u  
			                       
			            on CONCAT(s.ProgramCode,s.Fund,s.AccountCode) = CONCAT(u.PR_ProgramCode,u.Fund,u.PR_AccountCode)
			            
			         where s.OfficeCode = '" . $officeCode . "' and s.ProgramCode = '" . $programCode . "' and s.FundName = '" . $fund . "' 
			         group by s.ProgramCode,s.FundName,AccountCode
			         order by s.ProgramCode,FundName,s.AccountCode asc
						
			";
			return $this->query($sql);
		}		
		//----------------------------------------------------------------------------------------------- tracker
		public function LoadTracker(){
			$sql = "Select a.*,b.Title,c.OBRType FROM vouchercurrent a inner join fundtitles b on a.PR_AccountCode = b.Code left join type c on a.ClaimType = c.Type group by a.TrackingNumber";
			return $this->query($sql);
		}
		public function FethVoucherByCondition($conditon){
			//$sql = "Select a.*,b.Title FROM vouchercurrent a left join fundtitles b on a.PR_AccountCode = b.Code " . $conditon . " group by a.TrackingNumber order by id desc" ;
			$sql = "Select a.*,b.Title,c.OBRType FROM vouchercurrent a left join fundtitles b on a.PR_AccountCode = b.Code 
						   left join type c on a.ClaimType = c.Type " . $conditon . " group by a.TrackingNumber order by id desc limit 10" ;
			return $this->query($sql);
		}
		public function FetchClaimType(){
			$sql = "Select * from type order by type asc";
			return $this->query($sql);
		}
		public function UpdateToReceive($year,$modifiedBy,$dateModified,$status,$updateCase){
			$sql = "UPDATE vouchercurrent  SET Year = '" . $year . "', ModifiedBy = '" . $modifiedBy . "',DateModified = '" . $dateModified . "', Status = '" . $status . "'  WHERE " . $updateCase;
			$this->query($sql);
		}
		public function InsertToVoucherHistoryMultiple($condition){
			$sql = "Insert into voucherhistory (TrackingNumber,ModifiedBy,DateModified,Status) values " . $condition;
			$this->query($sql);
		}
		public function UpdateHistory($updateHistory){
			$sql = "UPDATE voucherhistory  set " . $this->completion("DateModified") . " where " . $updateHistory;
			$this->query($sql);
		}
		public function UpdateEditOBRs($code,$amount,$total,$id){
			$sql = "UPDATE vouchercurrent  SET PR_AccountCode = '" . $code . "', Amount = '" . $amount . "', TotalAmountMultiple = '" . $total . "'   WHERE Id = '" . $id . "'";
			$this->query($sql);
		}
		//----------------------------------------------------------------------------------------------- doctrack transmittal
		public function FetchReport($sign1,$claimType,$sign2,$fundType,$sign3,$status,$sDate,$from,$until){
			$sql = "select * from vouchercurrent where  ClaimType " . $sign1 . "'". $claimType ."' 
					and Fund " . $sign2 . "'" . $fundType . "'
					and Status " . $sign3 . "'" . $status . "'
					and substr(DateModified,1,10) = '" . $sDate . "'
					and ControlNo >= '" . $from . "' and ControlNo <= '" . $until . "' group by TrackingNumber
					order by ControlNo asc";			
			return $this->query($sql);
		}
		
		public function FetchReportAll($sign1,$claimType,$sign2,$fundType,$sign3,$status,$sDate){
			$sql = "select * from vouchercurrent where  ClaimType " . $sign1 . "'". $claimType ."' 
					and Fund " . $sign2 . "'" . $fundType . "'
					and Status " . $sign3 . "'" . $status . "'
					and substr(DateModified,1,10) like '%" . $sDate . "%' group by TrackingNumber
					
					order by claimant asc";		
			return $this->query($sql);
		}
		public function FetchByTransmitalOffice($searchValue){
				$sql = "select * from vouchercurrent where office = '" . $searchValue . "' order by DateModified desc";
				return $this->query($sql);
		}
		public function FetchByTransmitalValue($field,$searchValue){
				if($field == "ADV"){
					$sql = "select * from vouchercurrent where ADV1 like '%" . $searchValue . "%' or ADV2 like '%" . $searchValue . "%' group by trackingnumber order by DateModified desc";	
				}else{
					$sql = "select * from vouchercurrent where " . $field . " like '%" . $searchValue . "%' group by trackingnumber  order by DateModified  desc";	
				}
				return $this->query($sql);
		}
		//--------------------------------------------------------------------------------------------------------------------forum
		public function InsertForumMessage($sender,$receiver,$message,$messageType,$date,$senderOffice,$senderName){
			$sql = "INSERT INTO forum (Sender,Receiver,Message,MessageType,Date,SenderOffice,SenderName)
			VALUES ('" . $sender . "', '" . $receiver . "', '" . $message . "', '" . $messageType . "', '" . $date . "','" . $senderOffice . "','" . $senderName . "')";
			$this->query($sql);
		}	
		
		public function FetchForumMessagesByOffice($officeName){
			/*$sql = "SELECT a.*,b.LastName,b.FirstName,b.MiddleName,c.Name 
					FROM 
					forum a inner join employees b on a.Sender = b.employeenumber 
					inner join office c on b.officecode = c.code 
					where  a.receiver = 'all' OR a.receiver = '" . $officeName . "' OR c.Name= '" . $officeName . "'
					order by a.id desc limit 10";*/
					
			$sql = "SELECT t1.*, t2.* FROM forum  t1,
				    (SELECT a.Code,a.Name,b.OfficeCode,b.EmployeeNumber,b.FirstName,b.MiddleName,b.LastName  
					FROM office a inner join citydoc.employees b on a.Code = b.OfficeCode) as t2 where 
				    t1.Sender = t2.EmployeeNumber and t1.Receiver = 'all' or 
					t1.Sender = t2.EmployeeNumber and t1.Receiver = '" . $officeName . "' or 
					t1.Sender = t2.EmployeeNumber and t2.Name= '" . $officeName . "'
					order by t1.id desc limit 10";	
					
			
					
			return $this->query($sql);
		}	
		
		public function FetchMoreMessages($forumId,$officeName){
			
			$sql = "SELECT t1.*, t2.* FROM forum  t1,
				    (SELECT a.Code,a.Name,b.OfficeCode,b.EmployeeNumber,b.FirstName,b.MiddleName,b.LastName  
					FROM office a inner join citydoc.employees b on a.Code = b.OfficeCode) as t2 where 
				    t1.Sender = t2.EmployeeNumber and t1.Receiver = 'all' and t1.Id < '". $forumId ."' or 
					t1.Sender = t2.EmployeeNumber and t1.Receiver = '" . $officeName . "' and t1.Id < '". $forumId ."' or 
					t1.Sender = t2.EmployeeNumber and t2.Name= '" . $officeName . "' and t1.Id < '". $forumId ."'
					order by t1.id desc limit 10";
							
			return $this->query($sql);
		}
		
		public function FetchForumMessages(){
			/*$sql = "SELECT a.*,b.LastName,b.FirstName,b.MiddleName,c.Name 
					FROM 
					forum a inner join employees b on a.Sender = b.employeenumber 
					inner join office c on b.officecode = c.code order by a.id desc limit 10";*/
					
			$sql = "SELECT t1.*, t2.* FROM forum  t1,
				    (SELECT a.Code,a.Name,b.OfficeCode,b.EmployeeNumber,b.FirstName,b.MiddleName,b.LastName  
					FROM office a left join citydoc.employees b on a.Code = b.OfficeCode) as t2 where 
				    t1.Sender = t2.EmployeeNumber  order by t1.id desc limit 10";	
			return $this->query($sql);
		}
		
		
		public function FetchAllForumMessages($forumId){
			/*$sql = "SELECT a.*,b.LastName,b.FirstName,b.MiddleName,c.Name 
					FROM 
					forum a inner join employees b on a.Sender = b.employeenumber 
					inner join office c on b.officecode = c.code
					where a.Id < '". $forumId ."'
					order by a.id desc limit 10";*/
					
			$sql = "SELECT t1.*, t2.* FROM forum  t1,
				    (SELECT a.Code,a.Name,b.OfficeCode,b.EmployeeNumber,b.FirstName,b.MiddleName,b.LastName  
					FROM office a inner join citydoc.employees b on a.Code = b.OfficeCode) as t2 where 
				    t1.Sender = t2.EmployeeNumber  
					and t1.Id < '" . $forumId ."'
					order by t1.id desc limit 10";
			return $this->query($sql);
		}
		
		
		
		public function GetAnnouncement(){
			$sql ="select a.* from (SELECT * FROM citydoc2018.poster order by id desc) a group by a.office order by a.id desc ";
			return $this->query($sql);
		}
		public function FetchSingleForumMessage($messageId){
			$sql = "SELECT t1.*, t2.* FROM forum  t1,
				    (SELECT a.Code,a.Name,b.OfficeCode,b.EmployeeNumber,b.FirstName,b.MiddleName,b.LastName  
					FROM office a inner join citydoc.employees b on a.Code = b.OfficeCode) as t2 where 
				    t1.Sender = t2.EmployeeNumber and t1.iD = '" . $messageId . "' limit 1";
					
						
					
			return $this->query($sql);
		}
		public function FetchForumMessagesGoTo($goToMessageVal){
			$sql = "SELECT a.*,b.LastName,b.FirstName,b.MiddleName,c.Name 
					FROM 
					forum a inner join citydoc.employees b on a.Sender = b.employeenumber 
					inner join office c on b.officecode = c.code 
					where a.Id <= '" . $goToMessageVal ."'
					order by a.id desc limit 10";
			return $this->query($sql);
		}
		public function InsertForumReply($forumId,$sender,$message,$senderName,$senderOffice,$dateEncoded){
			
	
			$sql = "INSERT INTO forumreplies (ForumId,SenderId,Message,SenderFullname,SenderOffice,DateEncoded)
			VALUES ('" . $forumId . "', '" . $sender . "', '" . $message . "', '"  . $senderName . "', '"  . $senderOffice . "', '"  . $dateEncoded. "')";
			$this->query($sql);
		}
		
		public function UpdateReplyCount($forumId){
			$sql = "UPDATE forum SET Replies = Replies + 1  WHERE Id = '" . $forumId . "' limit 1";				
			$result = $this->query($sql);
		}
		
		public function FetchReplies($messageId){
			$sql = "SELECT a.*,b.LastName,b.FirstName,b.MiddleName,c.Name FROM forumreplies a inner join citydoc.employees b on a.SenderId = b.employeenumber inner join office c on b.officecode = c.code  where ForumId = '" . $messageId . "' order by Id asc";
			return $this->query($sql);
		}
		
		
		
		public function DeleteForum($forumId){
			$sql = "Delete from forum   WHERE Id = '" . $forumId . "' limit 1";				
			$result = $this->query($sql);
		}
		
		public function DeleteForumReplies($forumId){
			$sql = "Delete from forumreplies   WHERE ForumId = '" . $forumId . "'";				
			$result = $this->query($sql);
		}
		
		public function InsertToAnnouncement($sender,$name,$date,$message,$office){
			$sql = "INSERT INTO poster (PostedBy,Name,DatePosted,Message,Office)
			VALUES ('" . $sender . "', '" . $name . "', '" . $date . "', '" . $message . "','" . $office . "')";
			$this->query($sql);
		}
		public function InsertForumLogs($messageId,$messageType,$sender,$receiver,$date){
			$sql = "INSERT INTO forumlogs (MessageId,MessageType,Sender,Receiver,Date)
			VALUES ('" . $messageId . "', '" . $messageType . "', '" . $sender . "', '"  . $receiver . "', '"  . $date . "')";
			$this->query($sql);
		}
	
		public function FetchForumLog($officeName){
			$senderName = $_SESSION['fullName'];
			if($_SESSION['accountType'] > 2){
				$sql = 'SELECT * from forumlogs where Receiver = "' . $officeName . '" or Receiver = "client" or Sender = "' . $senderName . '" order by id desc limit 25';
			}else{
				
				$sql = 'SELECT * from forumlogs where Receiver = "' . $officeName . '" or Receiver = "all" or Sender = "' . $senderName . '" order by id desc limit 25';
			}		
			return $this->query($sql);
		}
		//---------------------------------------------------------------------------------settings
		public function SaveNewAccount($fund,$code,$title){
			$sql = "INSERT INTO fundtitles (Fund,Code,Title)VALUES ('" . $fund. "', '" . $code . "', '" . $title . "')";
			$this->query($sql);
		}
		public function FetchAllAccountTitles(){
			$sql = 'SELECT * from fundtitles order by id desc';
			return $this->query($sql);
		}
		public function DeleteAccountTitle($code){
			$sql = "Delete from fundtitles   WHERE code = '" . $code . "'";				
			$result = $this->query($sql);
		}
		public function FindAccountTitle($code){
			$sql = 'SELECT * from fundtitles where code = "' . $code . '"';
			return $this->query($sql);
		}
		
		public function DeleteProgram($code){
			$sql = "Delete from programcode   WHERE code = '" . $code . "'";				
			$result = $this->query($sql);
		}
		public function FindProgram($code){
			$sql = 'SELECT * from programcode where code = "' . $code . '"';
			return $this->query($sql);
		}
		public function SaveNewProgram($code,$name,$fund,$bnkAccount){
			
			$bnk = "NULL";
			if(strlen(trim($bnkAccount)) > 0) {
				$bnk = "'".$bnkAccount."'";
			}

			$sql = "INSERT INTO programcode (Code,Name,Fund,BankAccount) VALUES ('" . $code . "', '" . $name . "', '" . $fund . "', ".$bnk.")";
			$this->query($sql);
			
		}
		public function FetchAllProgram(){
			$sql = 'SELECT * from programcode order by id desc';
			return $this->query($sql);
		}
		//-------------------------------------------------------------------------------------main
		public function FetchBudgetForApproval(){
			$sql = "SELECT b.Name FROM funds a  inner join office b on a.OfficeCode = b.Code 
						where  a.Approval is NULL or a.Approval = 0 group by a.officeCode";
			return $this->query($sql);
		}
		//------------------------------------------------------------------------------------- OBRS
		
		public function FetchAllOBRs(){
			$sql = 'SELECT * from vouchercurrent where OBR_Number > 0 group by trackingnumber order by OBR_Number desc';
			return $this->query($sql);
		}
		//----------------------------SAAOB
		public function FetchDisbursement($office,$programCode){
			$sql = 'SELECT a.*,b.DateModified as OBR_Date FROM vouchercurrent a left join (Select * from voucherhistory c where c.status = "CAO Received" ) b
					on 
					a.TrackingNumber = b.TrackingNumber
					where
					a.office = "' . $office . '" and a.PR_programCode = "' . $programCode . '" and OBR_Number != 0 and a.status = "CAO Released"';
			
			return $this->query($sql);
			
		}		
		public function FetchOBRDisbursement($office){
			$sql = 'SELECT a.ADV1,a.ADV2,a.PR_ProgramCode,a.OBR_Number,sum(a.amount)as AmountTotal,a.TotalAmountMultiple,a.Claimant, b.DateModified as OBR_Date 
					FROM vouchercurrent a left join (Select * from voucherhistory c where c.status = "CAO Received" ) b
					on 
					a.TrackingNumber = b.TrackingNumber
					where
					a.office = "' . $office .'"  and OBR_Number != 0 and a.status = "CAO Released" group by OBR_Number,PR_ProgramCode order by b.DateModified';
			return $this->query($sql);
		}
		public function FetchOBRDisbursementByOBR($obrNumber,$year){
			$sql = 'SELECT a.ADV1,a.ADV2,a.PR_ProgramCode,a.OBR_Number,a.Amount, a.TotalAmountMultiple,a.TrackingNumber,
			        a.PR_AccountCode, a.TotalAmountMultiple,a.Claimant, a.DateModified,
			        a.ChargeType,a.Status,a.TransactionType,a.ClaimType,a.JevNo,
			        b.DateModified as OBR_Date 
					FROM vouchercurrent a left join (Select * from voucherhistory c where c.status = "CBO Received" or c.Status = "PR - CBO Received" ) b
					on 
					a.TrackingNumber = b.TrackingNumber
					where
					a.year = ' . $year . ' and
					OBR_Number = "' . $obrNumber . '" order by b.DateModified';
					
			return $this->query($sql);
		}
		public function FetchDisbursementLiquidatedBy($year,$field,$searchValue){
			$sql = 'SELECT a.Id,a.PR_CategoryCode as Category,a.TrackingType,a.ADV1,a.ADV2,a.PR_ProgramCode,a.OBR_Number,
					a.CheckNumber,
					a.CheckDate,
					a.Amount,a.TrackingNumber,
			       		 a.PR_AccountCode,
			       		 a.PO_Amount, 
			       		 a.TotalAmountMultiple,
					d.Category_Name as CategoryName, 
					a.Claimant, 
					a.DateModified,
			               a.ChargeType,
			               a.Status,
			         
			               a.ClaimType,
			               a.JevNo,
			               c.DateEncoded as JevDate
					
					FROM vouchercurrent a 
					left join (Select x.Trackingnumber,x.DateEncoded from liquidated x  group by trackingnumber)  c
					on a.TrackingNumber = c.TrackingNumber
					
					          
          			       left join bacdb2017.item_categories d          
				         on a.PR_CategoryCode = d.Category_Key	
					
					where
					a.year = ' . $year . ' and
					' . $field . ' = "' . $searchValue . '"';
					
			return $this->query($sql);
		}
		
		
		//------------- saaob encoded
		public function InsertToLiquidated($year,$office,$trackingNumber,$jevYear,$jevMonth,$jevNo,$adv,$obrNumber,$programCode,$accountCode,$amount,$claimant,$claimType, $employeeNumber,$dateEncoded){
			$sql = "INSERT INTO liquidated (Year,Office,TrackingNumber,JevYear,JevMonth,JevNo,Adv,OBRnumber,ProgramCode,AccountCode,Amount,Claimant,ClaimType,EncodedBy,DateEncoded)VALUES 
					('" . $year . "','" . $office . "','" . $trackingNumber . "','" . $jevYear ."','" . $jevMonth . "','" . $jevNo . "','" . $adv . "' , '" . $obrNumber . "','" . $programCode . "',
					 '" . $accountCode . "','" . $amount . "','" . $claimant . "','" . $claimType . "','"  . $employeeNumber . "','" . $dateEncoded . "')";
			$this->query($sql);
		}
		public function InsertToLiquidatedMultiple($insertThis){
			$sql = "INSERT INTO liquidated (Year,Office,TrackingNumber,JevYear,JevMonth,JevNo,Adv,OBRnumber,ProgramCode,AccountCode,Amount,Claimant,ClaimType,EncodedBy,DateEncoded)VALUES " . $insertThis  ;   
			return $this->query($sql);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
		}
		public function DeleteToLiquidation($year,$trackingNumber){
			$sql = "delete from liquidated where Year = '" . $year . "' and TrackingNumber = '" . $trackingNumber . "'";
			$this->query($sql);
		}
		public function UpdateLiquidated($trackingNumber,$jevNo,$employeeNumber,$dateEncoded){
			$sql = "update liquidated set JevNo = '" . $jevNo . "', EncodedBy = '" . $employeeNumber . "', DateEncoded = '" . $dateEncoded  . "' where TrackingNumber = '" . $trackingNumber . "'";
			$this->query($sql);
		}
		public function UpdateJevNo($year,$trackingNumber,$jevNo,$amount){
			$sql = "UPDATE vouchercurrent SET JevNo = '" . $jevNo . "',JevAmount = '" . $amount . "'  WHERE Year = '" . $year  . "' and  TrackingNumber = '" . $trackingNumber . "'";				
			$result = $this->query($sql);
		}
		
		public function FindLiquidated($trackingNumber){
			$sql = "SELECT id from liquidated where trackingnumber = '" . $trackingNumber . "' limit 1";
			return $this->query($sql);		
		}
		
		public function FetchAccountCodes($year,$office,$programCode){
			/*	
			$sql = "SELECT TrackingType,PR_ProgramCode, Pr_accountcode as Code,b.Title as Title,
			        sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount,sum(JevAmount) as JevAmount
			        FROM vouchercurrent a inner join fundtitles b
							on a.PR_accountcode = b.code
							where a.year = " . $year . " and office = '" . $office . "'  
			        and pr_programcode = '" . $programCode ."' and Status = 'CAO Released' 
			        group by Code
			        order by Code";	*/
			        
			$sql = "SELECT a.AccountCode,b.Title,a.Amount as AnnualBudget, c.Amount as Obligated, c.JevTotal as Liquidated, (a.Amount - ifnull(c.Amount,0)) as Savings,   (c.Amount - ifnull(c.JevTotal,0)) as Unliquidated
					FROM 
					(select AccountCode, Amount from funds where year =" . $year  . " and officecode = '" . $office  .  "' and ProgramCode = '" . $programCode . "') a
					left join 
					fundtitles b on a.AccountCode = b.Code
					left join
					(SELECT PR_AccountCode, sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount, sum(JevAmount) as JevTotal FROM 
					vouchercurrent where 
					year =" . $year  . " and office = '" . $office  .  "' and PR_ProgramCode = '" . $programCode . "'  and obr_number > 0 
					group by PR_AccountCode) c
					on a.AccountCode = c.PR_accountCode
					order by a.AccountCode asc";        
			        
			return $this->query($sql);
		}
		public function FetchAllAccountCodes($year,$programCode){
			/*$sql = "SELECT a.AccountCode,b.Title,a.Amount as AnnualBudget, c.Amount as Obligated, c.JevTotal as Liquidated, (a.Amount - c.Amount) as Savings,   (c.Amount - c.JevTotal) as Unliquidated
					FROM 
					(select AccountCode, Amount from funds where year =" . $year  . "  and ProgramCode = '" . $programCode . "') a
					left join 
					fundtitles b on a.AccountCode = b.Code
					left join
					(SELECT PR_AccountCode, sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount, sum(JevAmount) as JevTotal FROM vouchercurrent where year =" . $year  . " and PR_ProgramCode = '" . $programCode . "' 
					and Status = 'CAO Released' and obr_number > 0 group by PR_AccountCode) c
					on a.AccountCode = c.PR_accountCode
					order by a.AccountCode asc";*/
			$sql = "SELECT a.AccountCode,b.Title,a.Amount as AnnualBudget, c.Amount as Obligated, c.JevTotal as Liquidated, (a.Amount - ifnull(c.Amount,0)) as Savings,  (c.Amount - ifnull(c.JevTotal,0)) as Unliquidated
					FROM 
					(select AccountCode, Amount from funds where year =" . $year  . "  and ProgramCode = '" . $programCode . "') a
					left join 
					fundtitles b on a.AccountCode = b.Code
					left join
					(SELECT PR_AccountCode, sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount, sum(JevAmount) as JevTotal FROM 
					vouchercurrent where 
					year =" . $year  . " and PR_ProgramCode = '" . $programCode . "' and obr_number > 0 group by PR_AccountCode) c
					on a.AccountCode = c.PR_accountCode
					order by a.AccountCode asc";
			return $this->query($sql);
		}
		public function FetchOverAllAccountCodes($year){
				
			$sql = "SELECT a.AccountCode,b.Title,a.Amount as AnnualBudget, c.Amount as Obligated, c.JevTotal as Liquidated, (a.Amount - ifnull(c.Amount,0)) as Savings,   (c.Amount - ifnull(c.JevTotal,0)) as Unliquidated
					FROM 
					(select AccountCode, Amount from funds where year =" . $year  . " group by AccountCode) a
					left join 
					fundtitles b on a.AccountCode = b.Code
					left join
					(SELECT PR_AccountCode, sum(if(PO_Amount > 0,PO_Amount,Amount)) as Amount, sum(JevAmount) as JevTotal FROM vouchercurrent where year =" . $year  . " 
					and Status = 'CAO Released' and obr_number > 0 group by PR_AccountCode) c
					on a.AccountCode = c.PR_accountCode
					order by a.AccountCode asc";        
			return $this->query($sql);
		}
		public function FetchDisbursementInLiquidated($year,$office,$programCode,$accountCode){
			
			/*$sql = "SELECT TrackingNumber,ADV1,ADV2,OBR_Number,JevNo,Claimant,if(PO_Amount > 0,PO_Amount,Amount) as Amount,JevNo,DateModified from vouchercurrent 
					where year = '" . $year . "' and office = '" . $office . "'  and pr_programcode = '" .$programCode . "' and pr_accountCode = '" . $accountCode . "'
						and status ='CAO Released' and OBR_Number != '' order by claimant" ;*/
			$sql = "SELECT TrackingNumber,ADV1,ADV2,OBR_Number,JevNo,Claimant,if(PO_Amount > 0,PO_Amount,Amount) as Amount,JevNo,DateModified from vouchercurrent 
					where year = '" . $year . "' and office = '" . $office . "'  and pr_programcode = '" .$programCode . "' and pr_accountCode = '" . $accountCode . "'
						and status ='CAO Released' and OBR_Number > 0 order by claimant" ;
		
			return $this->query($sql);
		}
		public function FetchAllDisbursementInLiquidated($year,$programCode,$accountCode){
			
			$sql = "SELECT TrackingNumber,ADV1,ADV2,OBR_Number,JevNo,Claimant,if(PO_Amount > 0,PO_Amount,Amount) as Amount,JevNo,DateModified from vouchercurrent 
					where year = '" . $year . "'  and pr_programcode = '" .$programCode . "' and pr_accountCode = '" . $accountCode . "'
						and status ='CAO Released' and OBR_Number != '' order by claimant" ;
		
			return $this->query($sql);
		}
		public function FetchOverAllDisbursementInLiquidated($year,$accountCode){
			
			$sql = "SELECT TrackingNumber,ADV1,ADV2,OBR_Number,JevNo,Claimant,if(PO_Amount > 0,PO_Amount,Amount) as Amount,JevNo,DateModified from vouchercurrent 
					where year = '" . $year . "' and pr_accountCode = '" . $accountCode . "' and PR_ProgramCode != ''
						and status ='CAO Released' and OBR_Number != '' order by claimant" ;
		
			return $this->query($sql);
		}
		public function UpdateInsertTransfer($updateCase){
			$this->query($updateCase);
			return $this->affected_rows($this->connection);
		}
		public function CompareChecks($accountCode,$checks){
			$sql = "select checknumber,if(TrackingType = 'PY',Amount,PO_Amount) as Amount  from vouchercurrent where PR_AccountCode = '" . $accountCode . "' and checknumber IN("  . $checks . ")";
			return $this->query($sql);
		}
		public function UpdateInsertSpecial($case){
			$this->query($case);
			return $this->affected_rows($this->connection);
		}
		public function FetchThis($case){
			return $this->query($case);
		}
		public function ConsolidatedRecord(){
			$sql= "SELECT TrackingType, PR_ProgramCode as Program, PR_AccountCode as Code,sum(Amount) as OBR,sum(PO_Amount) as PO,sum(JevAmount) as Total  
					 FROM vouchercurrent 
					 where  JevNo != ''
					 group by PR_ProgramCode,PR_AccountCode
					 order by PR_ProgramCode,PR_AccountCode ";
			return $this->query($sql);
		}
		//-----------------------------------------------------------------------------------pr 
		
		public function SelectCategoryList($office){
			$sql = "select item_category as Category,count(ppmp_id) as Count from bacdb2017.ppmp 
			        where office_code = '" . $office . "' group by item_category order by item_category";
			return $this->query($sql);
		}
		public function SelectCategoryItems($office,$category){
			$sql = "select * from bacdb2017.ppmp where office_code = '" . $office . "' and  item_category = '" . $category . "'";
			return $this->query($sql);
		}
		public function SelectQuery($sql){
			return $this->query($sql);
		}
		public function SearchTracking($trackingNumber){
			/*$sql = "SELECT a.*,b.Name,c.LastName,c.FirstName,c.MiddleName,d.Name as ProgramName,e.Title,f.category_name as  CategoryName
							FROM vouchercurrent a 
							inner join office b on a.office = b.code  
							inner join citydoc.employees c on a.encodedby = c.employeenumber
							left join programcode d on a.PR_ProgramCode = d.code
							left join fundtitles e on a.PR_AccountCode = e.Code
							left join bacdb2017.item_categories f on a.PR_CategoryCode = f.category_key
							where trackingnumber  = '" . $trackingNumber . "'";	*/		
				/*$sql = "SELECT a.*,b.Name,c.LastName,c.FirstName,c.MiddleName,d.Name as ProgramName,e.Title,f.category_name as  CategoryName
							FROM vouchercurrent a 
							left join office b on a.office = b.code  
							left join citydoc.employees c on a.encodedby = c.employeenumber
							left join programcode d on a.PR_ProgramCode = d.code
							left join fundtitles e on a.PR_AccountCode = e.Code
							left join bacdb2017.item_categories f on a.PR_CategoryCode = f.category_key
							where trackingnumber  = '" . $trackingNumber . "' order by ";*/
							
							
				$sql = "SELECT a.*,b.Name,c.LastName,c.FirstName,c.MiddleName,d.Name as ProgramName,e.Title,f.Description as  CategoryName
								FROM vouchercurrent a 
								left join office b on a.office = b.code  
								left join citydoc.employees c on a.encodedby = c.employeenumber
								left join programcode d on a.PR_ProgramCode = d.code
								left join fundtitles e on a.PR_AccountCode = e.Code
								left join ppmpcategories f on a.PR_CategoryCode = f.Code  where trackingnumber  = '" . $trackingNumber . "' order by pr_programcode,pr_accountcode";
			
			return $this->query($sql);
		}
		public function SearchTrackingToVoucher($trackingNumber){
		
				$sql = "SELECT a.*,b.Name,c.LastName,c.FirstName,c.MiddleName,d.Name as ProgramName,e.Particulars
							FROM vouchercurrent a 
							left join office b on a.office = b.code  
							left join citydoc.employees c on a.encodedby = c.employeenumber
							left join programcode d on a.PR_ProgramCode = d.code
							left join particulars  e on a.TrackingNumber = e.TrackingNumber
							
							where a.trackingnumber  = '" . $trackingNumber . "'";
			
			return $this->query($sql);
		}
		public function SearchPRrecord($trackingNumber){
			$sql = "Select * from prrecord where trackingnumber ='" . $trackingNumber . "' order by ProgramCode,Description asc";
			return $this->query($sql);
		}
		public function SearchPOrecord($trackingNumber){
			$sql = "Select * from porecord where trackingnumber ='" . $trackingNumber . "' order by ProgramCode,Description asc";
			return $this->query($sql);
		}
		public function SearchPXrecord($trackingNumber){
			$sql = "SELECT * FROM pxrecord where TrackingNumber = '".$trackingNumber."' ORDER BY ProgramCode ASC, Code ASC, Description ASC";
			return $this->query($sql);
		}
		public function SearchVC($trackingNumber){
			$sql = "Select * from vouchercurrent where trackingnumber ='" . $trackingNumber . "'";
			return $this->query($sql);
		}
		public function EditLogs($trackingNumber,$field,$oldValue,$newValue,$office,$fullName,$date){
			$sql = "INSERT INTO editlogs (TrackingNumber,Field,OldValue,NewValue,Office,EditedBy,DateEdited) VALUES('". $trackingNumber ."','". $field ."','". $oldValue ."','". $newValue ."','" . $office . "', '". $fullName ."','". $date ."')";
			$this->query($sql);
		}
		public function EditLogs2018($trackingNumber,$field,$oldValue,$newValue,$office,$fullName,$date,$db){
			$sql = "INSERT INTO " . $db . "editlogs (TrackingNumber,Field,OldValue,NewValue,Office,EditedBy,DateEdited) VALUES('". $trackingNumber ."','". $field ."','". $oldValue ."','". $newValue ."','" . $office . "', '". $fullName ."','". $date ."')";
			$this->query($sql);
		}
		public function EditLogsRemote($trackingNumber,$field,$oldValue,$newValue,$office,$fullName,$date,$db){
			$sql = "INSERT INTO ".$db.".editlogs (TrackingNumber,Field,OldValue,NewValue,Office,EditedBy,DateEdited) VALUES('". $trackingNumber ."','". $field ."','". $oldValue ."','". $newValue ."','" . $office . "', '". $fullName ."','". $date ."')";
			$this->query($sql);
		}
		public function VaxLogs($year,$trackingNumber,$field,$action,$oldValue,$newValue,$fullName,$date){
			$sql = "INSERT INTO bacfiles.vaxlogs (Year, TrackingNumber, Field, Action, OldValue, NewValue, EditedBy, DateEdited)
					VALUES ('".$year."','".$trackingNumber."','".$field."','".$action."','".$oldValue."','".$newValue."','".$fullName."','".$date."')";
			$this->query($sql);
		}

		public function vCrypt($passText){
			
			$sql = "select * from citydoc.defaults";
			$pagination =  $this->queryV($sql);
			$margin =  $this->fetch_array($pagination);
			$padding = strlen($margin['Title']);
			
			$x = substr($passText,$padding * 10 + 10);
			$y = substr(strrev($x),1);
			$z = strrev($y);
			
			$j = 1; 
			$k = 1;
			$p = '';
			for($i  = 0 ;$i < strlen($passText); $i++){
				if($j == $padding){
					$p .= $passText[$i];
					$j = 0;
					$k++;
				}
				$j++;
				if($k >$z){
					break;
				}
			}
			return strrev($p);
		}
		Private function tokenizeNye($arryTok,$nye,$string){
			$newTokker = '';
			for($i = 0;$i < strlen($arryTok); $i++){
				$match  = strrpos($string,$arryTok[$i]);
				if($match !== false){
				}else{
					if($this->checkNye($nye,$arryTok[$i]) != 1){
						$newTokker = $arryTok[$i]; 
						break;
					}
				}
			}
			if($newTokker){
				return $newTokker;
			}else{
				return '}';
			}
		}
		Private function checkNye($nye,$tok){
			$loc = array_key_exists($tok, $nye);
			return $loc;
		}
		public function vArrange($text){
			$tokker = ''; 
			$eAscii  = array('Ç','ü','é','â','â','ä','à','å','ç','ê','ê','è','ï','î','ì','Ä','Å','É','æ','Æ','ô','ö','ò','û','ù','ÿ','Ö','Ü','¢','£','¥',
			                 '₧','ƒ','á','í','ó','ú','ñ','Ñ','ª','º','¿','⌐','¬','½','¼','¡','«','»','░','▒','▓','│','┤','╡','╢','╖','╕','╣','║','╗','╝','╜','╛','┐','└','┴','┬','├'
			                 ,'─','┼','╞','╟','╚','╔','╩','╦','╠','═','╬','╧','╨','╤','╧','╙','╘','╒','╓','╫','╪','┘','┌','█','▄','▌','▐','▀','α','ß','Γ','π','Σ','σ','µ','τ','Φ','Θ','Ω'
			                 ,'δ','∞','φ','ε','∩','≡','±','≥','≤','⌠','⌡','÷','≈','°','∙','·','√','ⁿ','²','■',' ','™');
			$arryTok = "~!@#$%^&*()-_+-=|/\\'?:;\"'.,>?<][{123456789";
			$nye = array();
					
			for($i = 0; $i < sizeOf($eAscii) ; $i++){
				$x = strrpos($text,$eAscii[$i]);
				if($x !== false){
					$tokker = $this->tokenizeNye($arryTok,$nye,$text);
					$text = str_replace($eAscii[$i],$tokker,$text);
					$nye[$tokker] = $eAscii[$i];
				}
			}
			
			$sql = "select * from citydoc.defaults";
			$pagination =  $this->query($sql);
			$margin =  $this->fetch_array($pagination);
			$padding = $margin['Title'];
			$sp =  $padding[1];
			
			$index  = strrpos($text,$sp);
			$crunchier = substr($text,0,$index);
			$crunchLen = substr($text,$index+1);
			$lenKey = 0;
			for($i = 0;$i < strlen($padding);$i++){
				$lenKey  +=  ord($padding[$i]);
			}
			while($lenKey > 0){
				if($lenKey - $crunchLen > 0){
					$lenKey = ($lenKey) - ($crunchLen);
				}else{
					break;
				}
			}
			$j = 3;
			$x =  $lenKey;
			$j = 1;
			$ss = '';
			$cL = strlen($crunchier);
			for($i = 0; $i < $cL; $i++){
				$char = $crunchier[$i];
				if($i >= $x){
					if($j % 2 == 0){
						$ss .=  $char;
					}	
					$j++;
				}else{
					$ss .= $char;
				}
			}
		  
			$ss1 = '';
			$j = 1;
			for($i = 0; $i < strlen($ss); $i++){
				if($j % 2 == 0){
					$ss1 .= $ss[$i];
				}	
				$j++;
				
			}
			$z = strlen($ss1) / 2;
			$z = intval($z);
			$a = substr($ss1,$z);
			$b = substr($ss1,0,$z);
			$a1 = array();
			$a1Len = strlen($a);
			$len = $a1Len;
			$m = 1;
			$ser = 0;
			for($i = 0; $i < $a1Len; $i++){
				if($m %  2 == 1){
					$a1[$ser++] = $a[$i];
				}else{
					$a1[--$len] = $a[$i];
				}
				$m++;
			}
			ksort($a1);
			$af =  implode("",$a1);
			$bf = $b;
			$f = strrev($bf) . $af;
			
			if($nye){
				foreach ($nye as $key => $value) {
				    $f = str_replace($key, $value , $f);  
				}
			}
			return $f ;
		}

		public function vArrange1($text){
			$x = substr(substr($text,4),0,strlen(substr($text,4))-3);
			return base64_decode($x);
		}
		
		public function smsEngine($numbers,$msg){
			$link = "https://accounts.davaocity.gov.ph/api/gsm?numbers=" . $numbers . "&msg=" . urlencode($msg) . "";
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $link);
			curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_exec($ch);
			if (curl_errno($ch)) {
			    $error_msg = curl_error($ch);
			}
			curl_close($ch);
			if (isset($error_msg)) {
				echo $error_msg;
			}
		}	

		public function youtubeLink($link){
			$pos = strpos($link,"embed");
			if($pos !=  ''){
				$x = strpos($link,"https://www.youtube.com/embed/");
				if(sizeof($x) == 0){
					$video = '';
				}
			}else{
				$x = strpos($link,"https://youtu.be");
				if(strlen($x) > 0){
					$arr = parse_url($link);
					$video = "https://www.youtube.com/embed" . $arr['path'];
				}else{
					$video = '';
				}
			}
			return $video;
		} 


		public function InsertToVoucherHistoryChild($trackingNumber,$modifiedBy,$dateModified,$status,$completion, $forCount){
			if($completion != ''){
				$completion = $completion . " <span style = \'color:red\'>Total</span>";
			}else{
				$completion = '';
			}
			$sql = "Insert into voucherhistory (TrackingNumber,ModifiedBy,DateModified,Status,Completion,Child) values ('" . $trackingNumber . "','" . $modifiedBy . "','" . $dateModified . "','" . $status . "','" . $completion . "','" . $forCount . "')";
			$this->query($sql);

			$sql = "UPDATE voucherhistory SET Child = 1 WHERE TrackingNumber = '".$trackingNumber."' AND Status IN ('Forwarding Transmittal','Check Preparation - CTO','Forwarded to Admin - Administration','Forwarded to Admin - Operation','Forwarded to SP - Admin','Check Advised','Check Released')";
			$this->query($sql);
		}

		public function lastIDInserted(){
            $sql = 'select last_insert_id() as lastID';
            return $this->query($sql);
        }

		public function createThumbnail($source, $dest, $ext) {
			$ext = strtoupper($ext);
			if($ext == "JPEG" || $ext == "JPG") {
				$sourceImage = imagecreatefromjpeg($source);
			}elseif($ext == "PNG") {
				$sourceImage = imagecreatefrompng($source);
			}

			$origWidth = imagesx($sourceImage);
       	 	$origHeight = imagesy($sourceImage);
			// $thumbWidth = floor($origWidth * $reduction);
			if($origWidth > $origHeight) {
				$thumbWidth = 500;
			}else {
				$thumbWidth = 300;
			}
			$thumbHeight = floor(($origHeight / $origWidth) * $thumbWidth);

			// echo "\nOrig - width:".$origWidth." ------ height:".$origHeight."\nThumb - width:".$thumbWidth ." ------ height:". $thumbHeight;

			$destImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
			imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);

			if($ext == "JPEG" || $ext == "JPG") {
				imagejpeg($destImage, $dest);
			}elseif($ext == "PNG") {
				imagepng($destImage, $dest);
			}


			imagedestroy($sourceImage);
			imagedestroy($destImage);			
		}


		function balhinFile($source, $destination, $filename)
		{

			// $source = '/../../../uploads/infra/reduced/';
			// $destination = '../../tempUpload/';
			$filename = $this->checkExtension($source, $filename);

			if ($filename != 0) {
				$file = realpath(dirname(__FILE__)) . $source . $filename;
				$file_handle = fopen($destination . $filename, 'a+');
				fwrite($file_handle, file_get_contents($file));
				fclose($file_handle);
			}

			return $filename;
		}

		function checkExtension($path,$filename){
			$file = realpath( dirname(__FILE__) ). $path . $filename . ".jpg";
			if (is_file($file)) {
				return $filename .".jpg";
			}else{
				$file = realpath( dirname(__FILE__) ). $path . $filename . ".JPG";
				if (is_file($file)) {
					return $filename .".JPG";
				}else{
					$file = realpath( dirname(__FILE__) ). $path . $filename . ".png";
					if (is_file($file)) {
						return $filename .".png";
					}else{
						$file = realpath( dirname(__FILE__) ). $path . $filename . ".PNG";
						if (is_file($file)) {
							return $filename .".PNG";
						}else{
							$file = realpath( dirname(__FILE__) ). $path . $filename . ".jpeg";
							if (is_file($file)) {
								return $filename .".jpeg";
							}else{
								$file = realpath( dirname(__FILE__) ). $path . $filename . ".JPEG";
								if (is_file($file)) {
									return $filename .".JPEG";
								}else{
									$file = realpath( dirname(__FILE__) ). $path . $filename . ".pdf";
									if (is_file($file)) {
										return $filename .".pdf";
									}else{
										$file = realpath( dirname(__FILE__) ). $path . $filename . ".PDF";
										if (is_file($file)) {
											return $filename .".PDF";
										}else{
											return 0;
										}
									}
								}
							}
						}
					}
				}
			}
		}	


	}
	
	$database = new MySQLDatabase();

	

?>