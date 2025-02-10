
<?php
	// if(isset($_COOKIE["PHPSESSID"])){
	// 	header('Set-Cookie: PHPSESSID='.$_COOKIE["PHPSESSID"].'; SameSite=None');
	// }

	require_once("../includes/database.php");

	$sql = "select * from citydoc.defaults";
	$pagination =  $database->queryV($sql);
	$margin =  $database->fetch_array($pagination);
	$padding = $margin['Title'];
?>
<script>
		
		var processorLink = "../ajax/dataprocessor.php";
		var uploadLink = '../ajax/uploadFile.php';
		// var year = "2023";
		
		function ajaxGetAndConcatenate(queryString,processorLink,container,ajaxType){
				
				var ajaxRequest;
				try{
					ajaxRequest = new XMLHttpRequest();
				} catch (e){
					try{
						ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try{
							ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e){
							alert("Your browser broke!");
							return false;
						}
					}
				}
				ajaxRequest.open("GET", processorLink + queryString, true);
				ajaxRequest.send(null); 
				ajaxRequest.onreadystatechange = function(){	

					if(ajaxRequest.readyState == 4){
						var result =  ajaxRequest.responseText.trim();

						if(ajaxType == "returnOnly"){
							container.innerHTML = result;	
						}else if(ajaxType == "returnNothing"){
							
						}else if(ajaxType == "returnOnlyLoader"){
							loader();
							container.innerHTML = result;	
						}else if(ajaxType == 'returnModalLoader') {
							loader();
							theAbsolute(result);
						}else if(ajaxType == "searchTrackingNumberQR"){ 
							// loader();
							container.innerHTML = result;
						}else if(ajaxType == "receiveTrackingNumberQR"){ 
							// loader();
							container.innerHTML = result;
						}else if(ajaxType == "revertTrackingNumberQR"){ 
							// loader();
							container.innerHTML = result;
						}else if(ajaxType == "fujxyza"){
							// document.getElementById("cont").innerHTML = result + "<br/>";
							// console.log(result);
							if(result == 1){
								setCookie("valbalangue",1, 1, '/; samesite=strict');
								// window.location.href = 'main.php'; 
								window.open('main.php', '_self');
							}else if(result == 2){
								alert('Please wait for the activation of your account.')
							}else if (result == 3){
								alert('Not found. Please try again.');
							}else if(result == 4){
								alert("Please don\'t.");
							
							}
						}else if(ajaxType == "Logout"){
								
								// setCookie("valbalangue",2, 1);
								// window.open('login.php', '_self');
								window.open('login.php', '_self');

						}
						else{
							alert("Variable not found.");
						}
					
					}

				}
		}
		function ajaxGetAndConcatenate1(queryString,processorLink,container,ajaxType){
				var ajaxRequest;
				try{
					ajaxRequest = new XMLHttpRequest();
				} catch (e){
					try{
						ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try{
							ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e){
							alert("Your browser broke!");
							return false;
						}
					}
				}
				ajaxRequest.open("GET", processorLink + queryString, true);
				ajaxRequest.send(null); 
				ajaxRequest.onreadystatechange = function(){	

					if(ajaxRequest.readyState == 4){
						var result =  ajaxRequest.responseText.trim();
						
						if(ajaxType == "returnOnly"){
							container.innerHTML = result;	
						}else if(ajaxType == "returnNothing"){
							
						}else if(ajaxType == "returnOnlyLoader"){
							loader();
							container.innerHTML = result;	
						}else if(ajaxType == 'returnModalLoader') {
							loader();
							theAbsolute(result);
						}else{
							alert("Variable not found.");
						}

					}

				}
		}																																																																																				
		function ajaxPost(queryString,processorLink, container,ajaxType) {
			var xmlHttp = null;
			if(window.XMLHttpRequest) {		
				xmlHttp = new XMLHttpRequest();
			}else if(window.ActiveXObject) {	
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}	
			var ajaxRequest =  xmlHttp;		
			ajaxRequest.open("POST", processorLink, true);			
			ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			ajaxRequest.send(queryString);	
			
			ajaxRequest.onreadystatechange = function() {

				if (ajaxRequest.readyState == 4) {
					var result = ajaxRequest.responseText.trim();

					if(ajaxType == "returnOnly"){
						container.innerHTML = result;	
					}else if(ajaxType == "returnNothing"){
						
					}else if(ajaxType == "returnOnlyLoader"){
						loader();
						container.innerHTML = result;	
					}else if(ajaxType == 'returnModalLoader') {
						loader();
						theAbsolute(result);
					}else{
						alert("Variable not found.");
					}

				}

			}
		}
		function round2(n) {
			//https://stackoverflow.com/questions/10015027/javascript-tofixed-not-rounding?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa
			// answered Sep 16 '15 at 9:45 Shura
			var digits = 2;
	        if (digits === undefined) {
	            digits = 0;
	        }
	        var multiplicator = Math.pow(10, digits);
	        n = parseFloat((n * multiplicator).toFixed(11));
	        x = Math.round(n) / multiplicator;
	        return x.toFixed(digits);
	    }

		// function round2(n) {
		// 	return parseFloat(n).toFixed(2);
		// }

		function trimTwoDecimals(num){
			var n = num.toString();
			var arr = n.split('.');
			if(arr.length > 1){
				var a = arr[0];
				var b = arr[1].substring(0,4);
				var c = a + '.' + b;
				var num = c;
			}
			return num;
		}

		function ajaxPost1(queryString,processorLink, container,ajaxType) {
			var xmlHttp = null;
			if(window.XMLHttpRequest) {		
				xmlHttp = new XMLHttpRequest();
			}else if(window.ActiveXObject) {	
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}	
			var ajaxRequest =  xmlHttp;		
			ajaxRequest.open("POST", processorLink, true);			
			ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			ajaxRequest.send(queryString);	
			
			ajaxRequest.onreadystatechange = function() {

				if (ajaxRequest.readyState == 4) {
					var result = ajaxRequest.responseText.trim();
						
					if(ajaxType == "returnOnly"){
						container.innerHTML = result;	
					}else if(ajaxType == "returnNothing"){
						
					}else if(ajaxType == "returnOnlyLoader"){
						loader();
						container.innerHTML = result;	
					}else if(ajaxType == 'returnModalLoader') {
						loader();
						theAbsolute(result);
					}else{
						alert("Variable not found.");
					}
					
				}

			}
		}
		
		function ajaxFormUpload(formData,uploadLink,ajaxType){
			var xhr = new XMLHttpRequest();
			xhr.open('POST',uploadLink, true);

			xhr.onload = function (){
				if (xhr.status === 200) {
				  	var result = xhr.responseText.trim();
					
					if(ajaxType == "returnOnly"){
						container.innerHTML = result;	
					}else if(ajaxType == "returnNothing"){
						
					}else if(ajaxType == "returnOnlyLoader"){
						loader();
						container.innerHTML = result;	
					}else if(ajaxType == 'returnModalLoader') {
						loader();
						theAbsolute(result);
					}else{
						alert("Variable not found.");
					}
					
				} else {
				   	alert('An error occurred!');
				}
			};

			xhr.send(formData);
		}
		// function ajaxFormUpload1(formData,uploadLink,ajaxType,container){
		// 	var xhr = new XMLHttpRequest();
		// 	xhr.open('POST',uploadLink, true);

		// 	xhr.onload = function (){
		// 		if (xhr.status === 200) {
		// 		  	var result = xhr.responseText.trim();
					
		// 			if(ajaxType == "returnOnly"){
		// 				container.innerHTML = result;	
		// 			}else if(ajaxType == "returnNothing"){
						
		// 			}else if(ajaxType == "returnOnlyLoader"){
		// 				loader();
		// 				container.innerHTML = result;	
		// 			}else if(ajaxType == 'returnModalLoader') {
		// 				loader();
		// 				theAbsolute(result);
		// 			}else if(ajaxType == "saveInfraUploadPre") {
		// 				loader();
		// 				document.getElementById('infraVideoLinkPre').value = "";
		// 				document.getElementById('infraVideoLinkPre').value = "";
		// 				document.getElementById('uploadInfraContainer1').innerHTML = "";
       	// 				document.getElementById('infraUpFileLabelPre').innerHTML = "Browse file/s";
		// 			}else{
		// 				alert("Variable not found.");
		// 			}

		// 		} else {
		// 		alert('An error occurred!');
		// 		}
		// 	};

		// 	xhr.send(formData);
		// }	
		
		function ajaxFormUpload1(formData,uploadLink,ajaxType,container){ //peter mod 30-1-25
			var xhr = new XMLHttpRequest();
			xhr.open('POST',uploadLink, true);
			xhr.onload = function (){
				  if (xhr.status === 200) {
				  	var result = xhr.responseText.trim();
					if(ajaxType == "saveInfraUploadPre") {
						
						container.innerHTML = result;
					}else if(ajaxType == "updateInfraUploadFull") {
						loader();
						resetUpdateProgress();
						container.innerHTML = result;
					}else{
						alert("Ajax type variable undefined.");
					}
				  } else {
				   	alert('An error occurred!');
				  }
			};
			xhr.send(formData);
		}			
		
		function ajaxAuth(id,pass){
				var url = "localhost/ajax/receiver.php";
				var queryString = "?id='" + id + "'&pass='" + pass + "'";
				
				//var cname = "tokeen";

				var xhr = new XMLHttpRequest();
				xhr.open("GET", url+queryString, true);
				xhr.setRequestHeader('Content-type','application/json; charset=utf-8');
				xhr.onreadystatechange = function () {
					var result = "";
					if (xhr.readyState == 4) {
						result = JSON.parse(xhr.responseText);
						alert(result);
						//var d = new Date();
					    //d.setTime(d.getTime() + (1*24*60*60*1000));
					    //var expires = "expires="+ d.toUTCString();
					    //document.cookie = cname + "=" + result.token + ";" + expires + ";path=/";
					} else {
						console.error(result);
					}
				}
				xhr.send(null);
		}
		
		
		//---------------------------------------------------------------soft seeking
		function printViewer(title,sheet){
		
			
			newWin= window.open("");
			newWin.document.write('<html><head><title>' + title + '</title>');
			newWin.document.write('<link rel="icon" href="/city/images/print.png">');
			newWin.document.write('<link rel="stylesheet" href="../style/custom.css">');
			newWin.document.write('</head><body>');
			newWin.document.write(sheet);
			newWin.document.write('</body></html>');
			newWin.document.close();
		}
		function exportToExcel(filename,table){
			var htmls = "";
	        var uri = 'data:application/vnd.ms-excel;base64,';
	        var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'; 
	        var base64 = function(s) {
	            return window.btoa(unescape(encodeURIComponent(s)))
	        };

	        var format = function(s, c) {
	            return s.replace(/{(\w+)}/g, function(m, p) {
	                return c[p];
	            })
	        };

	        htmls =  table.innerHTML;
	        var ctx = {
	            worksheet : 'Worksheet',
	            table : htmls
	        }
	        var link = uri + base64(format(template, ctx));  
	       	var downloadLink = document.createElement("a");
			downloadLink.href = link;
			
			downloadLink.download = filename + ".xls";
			document.body.appendChild(downloadLink);
			downloadLink.click();
			document.body.removeChild(downloadLink);  
		}
		
		var limit= 0;
		var entered = 0;
		var index = 0;
		function getAcctgEntry(){ // para sa pagkuha sa accounting entries unya ibutang sa array
				var ajaxRequest;
				try{
					ajaxRequest = new XMLHttpRequest();
				} catch (e){
					try{
						ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
					} catch (e) {
						try{
							ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
						} catch (e){
							alert("Your browser broke!");
							return false;
						}
					}
				}
				var queryString = "?loadAccountTiles=1";
				ajaxRequest.open("GET", processorLink + queryString, true);
				ajaxRequest.send(null); 
				ajaxRequest.onreadystatechange = function(){
					if(ajaxRequest.readyState == 4){
						var result = ajaxRequest.responseText.trim();	
						list  =  result.split('~?'); 
					}
				}
			}
		function search(me){
			 var i = 0, id = 1;
			 var key = me.value;
		     var parent = me.parentNode;
			 var result = parent.children[2];
			 if(entered == 1){
				result.innerHTML = "";
				entered = 0;
			 }else{ 
			 	if(key != ""){
					
					result.style.border= "1px dashed #289ED5";	
					result.style.borderTop= "0px dashed #289ED5";		
					result.style.padding= "10px";
					result.style.paddingTop= "0px";
					result.innerHTML ="";
					firstKey =  key.substring(0,1);
					if(isNumber(firstKey)){
						while(i < list.length){
							var data  =  list[i];
							if(key.toLowerCase() == data.substr(0,key.length).toLowerCase()){
								var span = document.createElement('span');
								
								var ind = data.indexOf(" ");
								var code = data.substr(0,ind);
								var title = data.substr(ind);
								
								span.innerHTML = "<ss style = 'color:rgb(105, 147, 173);'>" + code + "</ss> " + title;
								span.id = "span" + id; 
								span.className = "spanFound";
								if(span.addEventListener) {   
					                span.addEventListener ("click", clickEntry, false);
					            }
								result.appendChild(span);
								id++;
							}
							i++;
						}
					 }else{
						while(i < list.length){
							var data  =  list[i];
							if(key.toLowerCase() == data.substr(4,key.length).toLowerCase()){
								var span = document.createElement('span');
							
								var ind = data.indexOf(" ");
								var code = data.substr(0,ind);
								var title = data.substr(ind);
								span.innerHTML = "<ss style = 'color:rgb(105, 147, 173);'>" + code + "</ss> " + title;
								
								span.id = "span" + id; 
								span.className = "spanFound";
								if (span.addEventListener) {   
					                span.addEventListener ("click", clickEntry, false);
					            }
								result.appendChild(span);
								id++;
							}
							i++;
						}
					 }
					if(id == 1){
						result.style.display = "none";
					}
				 	document.getElementById("span"  + index ).style.color = "red";
					document.getElementById("span"  + index ).style.fontSize = "13px";
					document.getElementById("span"  + index ).style.fontWeight = "bold";	
				 }else{
				  	result.style.display = "none";
				 }
			 } 
		}
		//--------------------------------------------------------------------------------------------
		function validExtensions(extensions,selected){
			var arr =  extensions.split(",");
			var selected = selected.substring(selected.lastIndexOf('.')+1);
			x = 0;
			for(var i=0; i < arr.length; i++){
			  if(selected === arr[i]){
			  	x = 1;
			  }
			}
			return x;
		}
		function numberWithCommas(x) {
		    var parts = x.toString().split(".");
		    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		    return parts.join(".");
		}
		function getSelectText(id) {
		    return id.options[id.selectedIndex].text;
		}
		function selectToIndexZero(id){
			document.getElementById(id).selectedIndex = "0";
		}
		function selectToIndexZeroA(obj){
			obj.selectedIndex = "0";
		}
		function setSelectedIndex(s, v) {
		
		    for ( var i = 0; i < s.options.length; i++ ) {
		    	
		        if ( s.options[i].text == v ) {
		            s.options[i].selected = true;
		            return;
		        }
		    }
		}
		
		function clickEntry(func){
			var selected = this.textContent;
			var ind = selected.indexOf(" ");
			var code = selected.substr(0,ind);
			var title = selected.substr(ind).trim();
			entered = 1;
			this.parentNode.style.display= "none";
			setterValue(this,code,title)
		}
		function setterValue(me,code,title){
			var cookieValue = readCookie("lastMainMenu").trim();
			if(cookieValue == 1){
				document.getElementById('inputCode').value = code;
				document.getElementById('textareaDescription').value = title + " - PY";
			}else if(cookieValue == 2){
				document.getElementById('keywordFund').value = code;
				focusNext('fundAmountId');
			}
		}
		
		function keyPress(me,evt,func){
			var press =(evt.which) ? evt.which : event.keyCode;
			var parent = me.parentNode;
			var result = parent.children[2];
			if(parent.children.length == 2 ){	
				parent.appendChild(createResultDiv(me.offsetLeft));
			}
			var limit = result.childNodes.length;
			if(result.style.display == "none"){
				result.style.display = "block";
			}
			if(press == 27){
				result.style.display = "none";
				me.value = "12";
			}
			if(limit > 0){
				if(press == 38){
					index--;
					if(index < 1 ){
						index = 1;
					}
					document.getElementById("span"  + index ).style.color = "red";
				 }else if(press == 40){
					if(index < limit ){
						index++;	
					}
					document.getElementById("span"  + index ).style.color = "red";
				 }else if(press == 13){
				 	entered = 1;
					result.style.display = "none";
				 	var selected =  document.getElementById("span"  + index ).textContent;
					var ind = selected.indexOf(" ");
					var code = selected.substr(0,ind);
					var title = selected.substr(ind).trim();
					func(me,code,title);
					
				 }else{
				 	index = 1;
				 }
			}
		}
		function createResultDiv(me){
			var res = document.createElement('div');
			res.id = 'result';
			res.className = 'resultAccounts';
			
			res.style.marginLeft = (me) + "px"; 
			return res;
		}
		
		function toZero(value){
			if(value.length == 0 || value == ""  || value == "&nbsp;"   ){
				return 0;
			}else{
				return value;
			}
		}
		function toEmpty(value){
			if(value.length == 0 || value == ""  || value == "&nbsp;"   ){
				return '';
			}else{
				return value;
			}
		}
		function toNothing(value){
			
			if(value == 0){
				return "";
			}else if(value == null){
				return "";
			}else{
				return value;
			}
		}
		//--------------------------------------------------------------soft seek end
		function isValueNumber(me,evt){
			var id = me.value; 	 
			
			var charCode = (evt.which) ? evt.which : event.keyCode;
			
			if((charCode >= 37 && charCode <= 40) || (charCode >= 96 && charCode <= 105) || charCode >= 48 && charCode <= 57  || charCode == 8 || charCode == 46 || charCode == 13){
				return true;
			}else{
			 	return false;
			}     
		}
		function isAmount(me,evt){
			
			var id = me.value; 	 
			var charCode = (evt.which) ? evt.which : event.keyCode;
			var dashArray = id.match(/\./g);
			if(dashArray){
				if(charCode == 190 || charCode == 110){
				  if(dashArray.length == 1){
					return false;
				  }
				}	
			}	
			
			if (charCode == 190 || charCode == 110 || (charCode >= 37 && charCode <= 40) || (charCode >= 96 && charCode <= 105) || charCode >= 48 && charCode <= 57  || charCode == 8 || charCode == 46){
			 	return true;
			}else{
			 	return false;
			}    
		}
		function interVal(own,t){ 
			setTimeout(
				function(){		
					own();
				},t);
		}//time before execution
		function interVal1(own,t,par){ 
			setTimeout(
				function(){		
					own(par);
				},t);
		}
		function clearInputbox(containerId){
			var container = document.getElementById(containerId);
			var inputs = container.getElementsByTagName('input');
			for(var i = 0; i < inputs.length ; i++){
				inputs[i].value = "";
			}
		}
		
		function checkEmptyField(container){
			var empty = 0;
			var inputs = container.getElementsByTagName('input');
			var select  = container.getElementsByTagName('select');
			var textArea  = container.getElementsByTagName('textArea');
			for(var j = 0 ; j < 3; j++ ){
				if(j == 1){
					inputs = select;
				}else if(j == 2){
					inputs = textArea;
				}	
				for(var i = 0; i < inputs.length ; i++){
					if(inputs[i].value.trim().length == 0 ){
						if(inputs[i].parentNode.children.length <= 1){ //filter para dili ma doble ang empty action
							if(empty == 0){
								var qoute = document.createElement('span');
								qoute.className = 'qoute empty';
								qoute.innerHTML = '&nbsp;Please complete the required fields.';
								inputs[i].parentNode.appendChild(qoute);
							}else{
								var mark = document.createElement('span');
								mark.className = 'labelX empty';
								mark.innerHTML = 'x';
								inputs[i].parentNode.appendChild(mark);
							}
							inputs[i].addEventListener("focus", removeInvalids);
							inputs[i].className += " inputTextEmpty";
						}
						empty++;
					}
				}
			}
			return empty;
		}
		function checkEmptyField1(container,originalClass ){
			var empty = 0;
			var inputs = container.getElementsByTagName('input');
			var select  = container.getElementsByTagName('select');
			var textArea  = container.getElementsByTagName('textArea');
			for(var j = 0 ; j < 3; j++ ){
				if(j == 1){
					inputs = select;
				}else if(j == 2){
					inputs = textArea;
				}	
				for(var i = 0; i < inputs.length ; i++){
					if(inputs[i].value.trim().length == 0 ){
						if(inputs[i].parentNode.children.length <= 1){ //filter para dili ma doble ang empty action
							if(empty == 0){
								var qoute = document.createElement('span');
								qoute.className = 'qoute empty';
								qoute.innerHTML = '&nbsp;Please complete the required fields.';
								inputs[i].parentNode.appendChild(qoute);
							}else{
								var mark = document.createElement('span');
								mark.className = 'labelX empty';
								mark.innerHTML = 'x';
								inputs[i].parentNode.appendChild(mark);
							}
							inputs[i].addEventListener("focus", removeInvalidInfra);
							inputs[i].className += " inputTextEmpty";
						}
						empty++;
					}
				}
			}
			return empty;
		}
		function checkInvalidField(arrayInvalid){//para sa invalid value
			for(var i = 0; i < arrayInvalid.length ; i++){
				var inp = document.getElementById(arrayInvalid[i]);
				if(inp.parentNode.children.length <= 1){
					if(i == 0){
						var qoute = document.createElement('span');
						qoute.className = 'qoute empty';
						qoute.innerHTML = '&nbsp;Mali ni! Taronga.';
						inp.parentNode.appendChild(qoute);
					}else{
						var mark = document.createElement('span');
						mark.className = 'labelX empty';
						mark.innerHTML = 'x';
						inp.parentNode.appendChild(mark);
					}
					inp.addEventListener("focus", removeInvalids);
					inp.className = "inputTextEmpty";
				}
			}
			return arrayInvalid.length;
		}
		function removeInvalids(){
			clickInput1(this);
		}
		function removeInvalidInfra(){
			clickInputInfra(this);
		}
		function clickInputInfra(me){
			var parent =  me.parentNode;
			var child = parent.children.length;
			if(child > 1 ){
				me.parentNode.removeChild(me.parentNode.children[1]);
			}
			me.className = "inputProject";
		}
		function clickInput1(me){
			var parent =  me.parentNode;
			var child = parent.children.length;
			if(child > 1 ){
				me.parentNode.removeChild(me.parentNode.children[1]);
			}
			//var className = me.className.replace(" inputTextEmpty","");
			//me.className = className;
			me.className = "inputText";
		}
		function clickInput(me){
			me.style.backgroundColor = "transparent";
		}
		
		
		
		function checkEmptyNew(container, obj, msg,allowList,func){
			var emp = 0;
			var allow = allowList.split(',');
			var arrObj = obj.split(',');
			for(var k  = 0; k < arrObj.length; k++){
				var inputs = container.getElementsByTagName(arrObj[k]);
				var q = container.getElementsByClassName("qoute").length;
				for(var i = 0; i < inputs.length ; i++){
					
					for(var j = 0 ; j < allow.length; j++){
						if(allow[j] != inputs[i].id){
							var hit = 0;
						}else{
							var hit  = 1;
							break;
						}
					}
					if(hit < 1){
						if(inputs[i].value.trim().length == 0 || inputs[i].value.trim() == ''){
							var arr =inputs[i].className.split(' ');//filter para dili ma doble ang empty action
							if(arr.length == 1){
								if(q == 0){
									if(msg){	
										qouter(inputs[i],"qoute empty", msg);
									}
									q = 1;
								}else{
									qouter(inputs[i],"labelX empty","x");
								}
								classter(inputs[i],func);
							}
							emp++;
						}
					}	
				}
			}
			return emp;
		}
		function classter(me,func){
			me.className += " inputTextEmpty";
			me.addEventListener("focus", func);
		}
		function qouter(me,className,msg){
			var parent = me.parentNode;
			var exist = 0;
			for(var i = 0 ; i < parent.children.length; i++){
				var  cls = parent.children[i].className;
				res = cls.match(/empty/g);
				if(res){
					exist  = 1;
				}
			}
			if(exist == 0 ){
				var qoute = document.createElement('span');
				qoute.className = className;
				qoute.innerHTML = '&nbsp;' + msg;
				me.parentNode.insertBefore(qoute, me.parentNode.children[1]);
			}
			
		}
		
		function remover(me,textClass){
			var parent =  me.parentNode;
			var child = parent.children.length;
			me.className = textClass;
			for(var i = 0; i < child; i++){
				if(parent.children[i].className == "qoute empty" || parent.children[i].className == "labelX empty"){
					me.parentNode.removeChild(me.parentNode.children[i]);
				}
			}
		}
		
		
		function error(t){ 
			t.className += " wiggle"
			setTimeout(
				function(){		
					t.className = t.className.replace(/\b wiggle\b/,'');
					
				},800);
		}//wiggle
		function keypressNext(evt,id){
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if(charCode == 13){
				var target = document.getElementById(id);
				target.focus();
				clickInput(target);
			}
		}
		function keypressNext1(evt,id){
			
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if(charCode == 13){
				var target = document.getElementById(id);
				target.focus();
			}
		}
		function focusNext(id){
			document.getElementById(id).focus();
		}
		function keypressAndWhat(me,evt,func){
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if(charCode == 13){
				if(func){
					func();
				}
			}
		}
		function keypressAndWhat1(me,evt,func,para){
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if(charCode == 13){
				if(func){
					func(me,para);
				}
			}
		}
		function keypressAndWhatClear(me,evt,func,para){
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if(charCode == 13){
				if(func){
					func(me,para);
				}
			}else if(charCode == 17){
				
				searchClear(me);
			}
		}
		function searchClear(me){
			//wala lang
		}
		function keypressAndWhatClearAndAmisSearch(me,evt,func,para){
			var charCode = (evt.which) ? evt.which : event.keyCode;
			
			if(charCode == 13){
				if(func){
					func(me,para);
				}
			}else if(charCode == 17){
				searchClear(me);
			}
		}
		/*function searchClear(me){
			
		}*/
		function keypressAndUpDown(me,evt,func,enter,up,down){
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if(charCode == 13){
				if(func == 0){
					focusNext(enter);
				}else{
					func();
				}
				
			}else if(charCode == 38){
				focusNext(up);
			}else if(charCode == 40){
				focusNext(down);
			}
		}
		function clearOneInput(id){
			document.getElementById(id).value = "";
		}
		function keypressSubmit(evt,id){
			var charCode = (evt.which) ? evt.which : event.keyCode;
			if(charCode == 13){
				var target = document.getElementById(id);
				target.click();
			}	  
		}
		//--------------------------------------------------------------------------------- cookie
		function setCookie ( name, value, days){
			var cookie_string = name + "=" + escape ( value );	
		    if(days){
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				cookie_string += "; expires="+date.toGMTString();
			}
			document.cookie = cookie_string;
		}
		
		function readCookie(cookieName) {
		
			var cValue = -1;
			var ca = document.cookie.split(';');
			
			for(var i = 0 ; i < ca.length;i++){
				var c = ca[i].split('=');
				if(cookieName.trim() == c[0].trim()){	
					cValue = c[1];
					break;
				}
			}
			
			return cValue;
		}
		function cookieLabel(ind,containerId){
			
			var parent = document.getElementById(containerId);
			if(parent.children[ind]){
				return parent.children[ind].textContent;
			}
		} 
		
		function isNumber(n) {
		  return !isNaN(parseFloat(n)) && isFinite(n);
		}
		//getAcctgEntry();
		var sc = 0;
		
		function editor(fieldName,fieldId,oldValue,func){
			
			var id =  fieldId;
			var sheet = "<div class = 'editorContainer'><table class='editorTable' style ='font-family:Oswald;'>";
				sheet += "<tr><td class = 'editorHeader' colspan = '2' >Editor<div onclick ='closeAbsolute(this)' class = 'closeEditor'></div></td></tr>";
			    sheet += "<tr><td class = 'editorLabel' >" + fieldName + "</td><td style = 'padding-bottom:20px; padding-top:40px;padding-right:40px;'>";
				
				sheet += "</td></tr id = 'editorPeriod' >";
				sheet += "<tr style = 'display:none;'><td colspan = '2' style = ''><table style = 'border:1px solid white;margin-left:57px;margin-bottom:20px;background-color:rgba(192, 192, 192,.3);'>";
				sheet += " <tr><td class = 'editorLabel' style = 'vertical-align:top;padding-top:10px;' >Period</td><td style = ''>";
				sheet += " <select id = 'editorP1' class = 'select2' style= 'width:200px;font-family:Oswald;font-size:20px;'><option>Monthly</option><option>Quarterly</option><option>First Half</option><option>Second Half</option></select></td></tr>";
				sheet += " <tr><td class = 'editorLabel' style = 'vertical-align:top;padding-top:10px;' >Value</td><td style = ''>";
				sheet += " <select id = 'editorP2' class = 'select2' style= 'width:200px;font-family:Oswald;font-size:20px;'><option>January</option><option>February</option><option>March</option><option>April</option><option>May</option><option>June</option><option>July</option><option>August</option><option>September</option><option>October</option><option>November</option><option>December</option><option>1st Quarter</option><option>2nd Quarter</option><option>3rd Quarter</option><option>4th Quarter</option></select></td></tr>";
				
				sheet += "</table></td></tr>";
				sheet += "<tr><td colspan = '2' style = 'padding-bottom:20px;text-align:center;'><input type = 'hidden' id = 'hiddens' value = '"  + oldValue +  "'><div   id = '" + id + "' class ='button1 b1' onclick= 'goUpdate(this)'>Save</div></td></tr>";
				sheet += "</table></div>";
			theAbsolute(sheet);
			//document.getElementById("old" + oldValue).focus();
		}
		function editor1(fieldName,fieldId,oldValue,func){
			var id =  fieldId;
			var sheet = "<div class = 'remarkEditorContainer'><table class='editorTable'>";
				sheet += "<tr><td class = 'editorHeader' colspan = '2' >Editor<div onclick ='closeAbsolute(this)' class = 'closeEditor'></div></td></tr>";
			    sheet += "<tr><td class = 'editorLabel' >" + fieldName + "</td><td style = 'padding-bottom:20px; padding-top:40px;padding-right:40px;'>";
				sheet += "<input class='select2' style = 'padding:10px;width:auto;' id = 'amountEntered" + id + "'  value = '" + oldValue + "' onkeydown = 'return isAmount(this,event)'  />";
				sheet += "</td></tr>";
				sheet += "<tr><td colspan = '2' style = 'padding-bottom:20px;text-align:center;'><div   id = '" + id + "' class ='button1' onclick= " + func + ">Save</div></td></tr>";
				
				sheet += "</table></div>";
			theAbsolute(sheet);
		}
		function remarks(title,fieldName,fieldId,oldValue,func){
			var id =  fieldId;
			var sheet = "<div class = 'editorContainer' ><table style = 'font-family:Oswald;margin:0px;' class='editorTable'>";
				sheet += "<tr><td class = 'editorHeader' colspan = '2' style = 'background-color:rgb(234, 59, 149);color:white;' >" + title + " <b style ='font-size:20px;'>" +  id +"</b><div id = 'closeRem' onclick ='closeAbsolute(this)' class = 'closeEditor'></div></td></tr>";
			    sheet += "<tr><td class = 'editorLabel' style = 'vertical-align:top;padding-top:47px;' >" + fieldName + "</td><td style = 'padding-bottom:20px; padding-top:40px;padding-right:40px;'>";
				sheet += "<textarea class='select2' style = 'padding:10px;width:250px;height:120px;font-size:16px;' placeholder = '' id = 'remValue'/></textarea>";
				sheet += "</td></tr>";
				sheet += "<tr><td colspan = '2' style = 'padding-bottom:20px;text-align:center;padding-left:50px;'><div   id = '" + id + "' class ='button1' onclick = '" + func + "'>Save</div></td></tr>";
				sheet += "</table></div>";
			theAbsolute(sheet);
			
		}
		function remarks1(title,fieldName,fieldId,func){
			var id =  fieldId;
			var sheet = "<div class = 'editorContainer' ><table style = 'font-family:Oswald;margin:0px;' class='editorTable'>";
				sheet += "<tr><td class = 'editorHeader' colspan = '2' style = 'background-color:rgb(8, 149, 196);color:white;' >" + title + " <div id = 'closeRem' onclick ='closeAbsolute(this)' class = 'closeEditor'></div></td></tr>";
			    sheet += "<tr><td class = 'editorLabel' style = 'vertical-align:top;padding-top:47px;' >" + fieldName + "</td><td style = 'padding-bottom:20px; padding-top:40px;padding-right:40px;'>";
				sheet += "<textarea class='select2' maxlength ='200' style = 'padding:10px;width:250px;height:120px;font-size:16px;' placeholder = '' id = 'remValue'></textarea>";
				sheet += "</td></tr>";
				sheet += "<tr><td colspan = '2' style = 'padding-bottom:20px;text-align:center;padding-left:50px;'><div  id = '" + id + "' class ='button1' onclick = '" + func + "'>Save</div></td></tr>";
				sheet += "</table></div>";
			theAbsolute(sheet);
			
		}
		
		function msg(message){
			var sheet = "<div class = 'editorContainer'><table class='editorTable'>";
				sheet += "<tr><td class = 'tdMessage' >" + message.trim() + "</td>";
				sheet += "<tr><td style ='text-align:center;'><input class = 'hiddenInput' type = 'hidden' id = 'hiddenInput' onkeydown = 'keypressAndWhat(this,event,closeAbsolute)' /><input id = 'messageBoxClose' type = 'submit'  class = 'closeMessage' onclick ='closeAbsolute(this)' value = 'Close'/></td>";
				sheet += "</table></div>";
			
			theAbsolute(sheet);
			document.getElementById('absoluteHolder').style.zIndex = 106;
		}
		
		function msg1(message){
			var sheet = "<div class = 'editorContainer'><table class='editorTable'>";
				sheet += "<tr style = 'background-color:white;'>";
				sheet += "<td style = 'float:right;'><input class = 'hiddenInput' type = 'hidden' id = 'hiddenInput'onkeydown = 'keypressAndWhat(this,event,closeAbsolute)' /><input id = 'clickClose' type = 'submit' class = 'button2' style = 'padding:10px;cursor:pointer;font-weight:normal;' onclick ='closeAbsolute(this)' value = 'Cancel'/></td>";
				sheet += "<tr><td class = 'tdMessage' >" + message.trim() + "</td>";
				sheet += "</table></div>";
			
			theAbsolute(sheet);
			document.getElementById('absoluteHolder').style.zIndex = 106;
		}
		function msg2(message){
			var sheet = "<div class = 'editorContainer'><table class='editorTable'>";
				sheet += "<tr style = 'background-color:white;'>";
				sheet += "<td style = 'float:right;'><input class = 'hiddenInput' type = 'hidden' id = 'hiddenInput'onkeydown = 'keypressAndWhat(this,event,closeAbsolute)' /><input id = 'clickClose' type = 'submit' class = 'button2' style = 'background-color:rgb(248, 226, 230); text-shadow:0px 0px 1px white; padding:2px 5px;cursor:pointer;font-weight:bold;' onclick ='closeAbsolute(this)' value = '&#215;'/></td>";
				sheet += "<tr><td class = 'tdMessage' >" + message.trim() + "</td>";
				sheet += "</table></div>";
			
			theAbsolute(sheet);
			document.getElementById('absoluteHolder').style.zIndex = 106;
		}
		function msg3(message){
			var sheet = "<div class = 'editorContainer' style ='background-color:rgba(252, 255, 255,.1);padding:10px 15px;'><table class='editorTable'>";
				sheet += "<tr style = 'background-color:transparent;'>";
				sheet += "<td style = 'float:right;'><input class = 'hiddenInput' type = 'hidden' id = 'hiddenInput'onkeydown = 'keypressAndWhat(this,event,closeAbsolute)' /><input id = 'clickClose' type = 'submit' class = 'button2' style = 'display:none;background-color:rgb(248, 226, 230); text-shadow:0px 0px 1px white; padding:2px 5px;cursor:pointer;font-weight:bold;' onclick ='closeAbsolute(this)' value = '&#215;'/></td>";
				sheet += "<tr><td class = 'tdMessage' >" + message.trim() + "</td>";
				sheet += "</table></div>";
			
			theAbsolute(sheet);
			document.getElementById('absoluteHolder').style.zIndex = 106;
		}
		function loader(){
			var sheet = "<div class = 'loaderContainer' ><table id = 'loader' style ='z-Index:100px;'  >";
				sheet += "<tr style = ''>";
				sheet += "<td style = 'float:right;'><div class = 'loader' ></div></td>";
				sheet += "</table></div>";
			
			var exist = document.getElementById("loader");
			if(exist){
				closeAbsolute(1);
			}else{
				theAbsolute1(sheet);
				document.getElementById('absoluteHolder').style.zIndex = 106;
			}
		}
		function theAbsolute(sheet){
			var table = document.createElement('table');
			table.id = "absoluteHolder";
			table.className = "absoluteHolder";
			sc = document.body.scrollLeft;
			document.body.scrollLeft = 0;
			document.body.style.overflowX = 'hidden';
			
			scTop = document.body.scrollTop;
			document.body.scrollTop = 0;
			document.body.style.overflowY = 'hidden';
			
			var row = table.insertRow(0);
		    	var cell = row.insertCell(0);
			cell.innerHTML = sheet;
			document.body.insertBefore(table, document.body.children[0]);
		}
		
		function theAbsolute1(sheet){
			var table = document.createElement('table');
			table.id = "absoluteHolder";
			table.className = "absoluteHolder1";
			sc = document.body.scrollLeft;
			document.body.scrollLeft = 0;
			document.body.style.overflowX = 'hidden';
			
			scTop = document.body.scrollTop;
			document.body.scrollTop = 0;
			document.body.style.overflowY = 'hidden';
			
			var row = table.insertRow(0);
		      var cell = row.insertCell(0);
			cell.innerHTML = sheet;
			document.body.insertBefore(table, document.body.children[0]);
		}
		function closeAbsolute(me){
			document.body.scrollLeft = sc;
			document.body.scrollTop = scTop;
			
			var parent = document.getElementById('absoluteHolder');
			parent.parentNode.removeChild(parent);
			document.body.style.overflowX = 'auto';
			document.body.style.overflowY= 'auto';
		}
		//----------------------------------------------------------- appropriations
		function menuChanger(me,menuSelected,menuType,container,containerClass){
		
			var parent =  me.parentNode;
			var label = me.textContent;
			var className = me.className;
			var containerBody = document.getElementById(container);
			if(className != menuSelected){
				for(var i = 0 ; i < parent.children.length; i++){
					parent.children[i].className = className;
					if(containerBody.children[i]){
						containerBody.children[i].className ="hide";
					}
					if(label == parent.children[i].textContent){
						me.className = menuSelected;
						if(containerBody.children[i]){
								containerBody.children[i].className = containerClass;
						}
					
						setCookie(menuType,i, 100);
					}
				}	
			}
		}
		function closex(){
			closeAbsolute(1);
		}
		//-------------------------------------------
		function createRowHeader(Id,fields){
			
				var table = document.getElementById(Id);
				var tableWidth = table.offsetWidth;
				
				var length = table.children[0].children[0].children.length;
				if(length){
					
				
					var td = table.children[0].children[0].children;
					
					var field = fields.split(",");
					
					var sheet = "<table style = 'width:" + tableWidth + "px;border-spacing:1;'><tr>";
					var align = "left"
					for(var i = 0; i < td.length ; i++){
						var width = td[i].offsetWidth;
						if(i == 7){
							align =  "left;";
						}else{
							align =  "center";
						}
						sheet += "<td width=" + width + " class = 'tdSAAOBHeader3' style = ' font-size:12px; text-align:" + align + ";'>" + field[i] +  "</td>";
					}
					sheet += "</tr></table>";
				
					return sheet;
				}
			}
		
		function scrollTop(){
			document.body.scrollTop = 0;
		}
		function scrollBottom(){
			document.body.scrollTop = document.body.scrollTop;
		}
		var th = ['','thousand','million', 'billion','trillion'];
			// uncomment this line for English Number System
			// var th = ['','thousand','million', 'milliard','billion'];
		var dg = ['zero','one','two','three','four','five','six','seven','eight','nine']; 
		var tn =['ten','eleven','twelve','thirteen', 'fourteen','fifteen','sixteen','seventeen','eighteen','nineteen']; 
		var tw = ['twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety']; 


		function convertWordCurrency(ss){
			var ss = ss.toString(); 
			var num = ss.split('.');
			 if(num.length == 2){
				var numA = parseInt(num[0]);
				var numB =num[1];
				if(numB.length == 1){              //decimal remove trailing zero
					numB = numB + "0";
				}else{
					if(numB.substr(0,1) == '0'){
						numB = parseInt(numB);
					}
				}
				if(numB > 1){
					var cen = " CENTAVOS";	
					cen = '';
					var inWords = toWords(numA) +  ' PESOS AND ' +   numB +  '/100' + cen;
				}
				 if(numB == 1){
					var cen = " CENTAVO";	
					cen = '';
					var inWords = toWords(numA) +  ' PESOS AND  ' +   numB +  '/100' +  cen;
				}
				if(numB == 0){
					var inWords = toWords(numA) +  ' PESOS'
				}
			}else{
				var numA =  num[0];
				var inWords = toWords(numA) +  ' PESOS'
			}
			return  inWords.toUpperCase();
		}
		function toWords(s){
			s = s.toString(); 
			s =s.replace(/[\, ]/g,''); 
			if (s != parseFloat(s)) return 'not a number'; 
			var x = s.indexOf('.'); 
			if (x == -1) x = s.length; 
			if (x > 15) return 'too big'; 
			var n =s.split(''); 
			var str = ''; 
			var sk = 0; 
			for (var i=0; i < x; i++) {
				if((x-i)%3==2) {
					if (n[i] == '1') {
						str += tn[Number(n[i+1])] + ' '; 
						i++; 
						sk=1;
				}else if (n[i]!=0) {
					str += tw[n[i]-2] + ' ';
					sk=1;}
				} else if (n[i]!=0) {
					str +=dg[n[i]] +' '; 
					if ((x-i)%3==0) str += 'hundred ';
						sk=1;
					} 
					if ((x-i)%3==1) {
						if (sk)str += th[(x-i-1)/3] + ' ';sk=0;
					}
				} if (x != s.length) {
					var y = s.length; str +='and '; 
				for (var i=x+1; i<y; i++) str += dg[n[i]] +' ';
				} 
				return str.replace(/\s+/g,' ');
		}
		function vScram1(text){
			var x = Math.random();
			var crambles = x.toString(36).substr(2);
			return crambles.substring(3,7) +  btoa(unescape(encodeURIComponent(text))) + crambles.substring(0,3);
		}
		function vScram(b){
			var key = "<?php echo $padding;?>";
			
			var pads = '';
			var x1 = 0;
			for(var i = 0 ; i < key.length; i++){
				var x = key.charCodeAt(i).toString(10);
				x1 = parseInt(x1) + parseInt(x);
				pads += x;
			}
			var a = b.substr(0);
			var ind = parseInt(a.length / 2);
			var first = a.substr(ind);
			var second = a.substring(0,ind);
			
			var s = '';
			
			var f1 = first.split('').reverse().join("");	
			var lastlen =  first.length;
			
			for(var i = 0; i < lastlen; i++){
				if(s.length < lastlen){
					s += first[i];
				}
				if(s.length < lastlen){
					s += f1[i];
				}else{
					break;
				}
			}
			var text = second.split('').reverse().join("") + s;
			var x = Math.random();
			var crambles = x.toString(36).substr(2,8);
			var crunch = '';
			var j = 0;
			for(var i = 0; i < text.length; i++){
				if(j < crambles.length){
					var cramb =  crambles[j];
					j++;
				}else{
					j = 0;
					var cramb =  crambles[j];
				}
				var t = text[i];
				crunch += cramb + t ;
			}
			
			var cLen = crunch.length;
			if(x1 <= cLen ){
				t = x1;
			}else{
				var t = x1;
				while(t > 0){
					if(t - cLen > 0){
						t = parseInt(t) - parseInt(cLen);
					}else{
						break;
					}
				}
			}
			var crunchier =  ''
			for(var i = 0; i < cLen;i++){
				crunchier += crunch[i];
				if(i >= (t-1)){
					var yInd  = Math.floor((Math.random() * crambles.length-1) + 1);
					crunchier +=  crambles[yInd];
				}
			}
			crunchier = crunchier + key[1] + cLen;
			return crunchier;
		}
		function sendSms(trackingNumber){
			
			interVal1(actualSender,5000,trackingNumber)		
		}
		function actualSender(trackingNumber){
			var container = "";
			var queryString = "?sendSMS=1&trackingNumber=" + trackingNumber ;
			ajaxGetAndConcatenate(queryString,processorLink,container,"sendSMS");
		}
		function sendSmsAlways(trackingNumber){	
			interVal1(actualSenderAlways,5000,trackingNumber)		
		}
		function actualSenderAlways(trackingNumber){
			var container = "";
			var container = document.getElementById('doctrackUpdateContainer');		
			var queryString = "?sendSMSAlways=1&trackingNumber=" + trackingNumber ;
			ajaxGetAndConcatenate(queryString,processorLink,container,"sendSMSAlways");
		}
		function numberToMonth(month){
			if(month == 1){
				month = "January";
			}else if(month == 2){
				month = "February";
			}else if(month ==3){
				month = "March";
			}else if(month == 4){
				month = "April";
			}else if(month == 5){
				month = "May";
			}else if(month == 6){
				month = "June";
			}else if(month == 7){
				month = "July";
			}else if(month == 8){
				month = "August";
			}else if(month == 9){
				month = "September";
			}else if(month == 10){
				month = "October";
			}else if(month == 11){
				month = "November";
			}else if(month == 12){
				month = "December";
			}
			return month;
		}

		function fileCheckJS(file, allowed) {
			var fileName = file.name;
			var fileType = file.type;
			var fileExtn = fileName.split('.').pop().toUpperCase();

			var allowedExt = allowed.toUpperCase().split(',');
			var allowedType = [];
			var type = "";
			for(var i = 0 ; i < allowedExt.length; i++){
				type = "";
				if(allowedExt[i] == "JPG"){
					type = "image/jpeg";
				}else if(allowedExt[i] == "JPEG"){
					type = "image/jpeg";
				}else if(allowedExt[i] == "PNG"){
					type = "image/png";
				}else if(allowedExt[i] == "GIF"){
					type = "image/gif";
				}else if(allowedExt[i] == "XLS"){
					type = "application/vnd.ms-excel";
				}else if(allowedExt[i] == "XLSX"){
					type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
				}else if(allowedExt[i] == "DOC"){
					type = "application/msword";
				}else if(allowedExt[i] == "DOCX"){
					type = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
				}else if(allowedExt[i] == "PDF"){
					type = "application/pdf";
				}else if(allowedExt[i] == "DBF"){
					type = "application/octet-stream";
				}
				if(type != "") {
					allowedType[i] = type;
				}
			}

			var err = 0;
			if(allowedType.length == allowedExt.length) {
				
				if(!allowedExt.includes(fileExtn)) {
					err = 1;
				}

				if(!allowedType.includes(fileType)) {
					err = 1;
				}

			}else {
				err = 1;
			}

			return err;
		}
		function checkDuplicate(arr){
			var x = 0;
			for(var j = 0; j < arr.length; j++){
				for(var k = 0; k < arr.length; k++){
					if(j != k){
						var a = arr[j].trim();
						var b = arr[k].trim();
						if(a == b ){
					    	x =1;
					    	break
					    }
					}
				}  
				if(a == b ){
					break
				}   
			}
			return x;	
		}

		function loader2(){
			var sheet = "<div class = 'loaderContainer'><table id = 'loader' style ='z-Index:100px;'>";
				sheet += "<tr style = ''>";
				sheet += "<td style = 'float:right;'><div class = 'loader2'></div></td>";
				sheet += "</table></div>";
			
			var exist = document.getElementById("loader");
			if(exist){
				closeAbsolute(1);
			}else{
				theAbsolute2(sheet);
				document.getElementById('absoluteHolder').style.zIndex = 106;
			}
		}

		function theAbsolute2(sheet){
			var table = document.createElement('table');
			table.id = "absoluteHolder";
			table.className = "absoluteHolder2";
			sc = document.body.scrollLeft;
			document.body.scrollLeft = 0;
			document.body.style.overflowX = 'hidden';
			
			scTop = document.body.scrollTop;
			document.body.scrollTop = 0;
			document.body.style.overflowY = 'hidden';
			
			var row = table.insertRow(0);
		    var cell = row.insertCell(0);
			cell.innerHTML = sheet;
			document.body.insertBefore(table, document.body.children[0]);
		}

		function theAbsolute3(sheet){
			var table = document.createElement('table');
			table.id = "absoluteHolder";
			table.className = "absoluteHolder2";
			sc = document.body.scrollLeft;
			document.body.scrollLeft = 0;
			document.body.style.overflowX = 'hidden';
			
			scTop = document.body.scrollTop;
			document.body.scrollTop = 0;
			document.body.style.overflowY = 'hidden';
			
			var row = table.insertRow(0);
		    var cell = row.insertCell(0);
			cell.innerHTML = sheet;
			cell.style.verticalAlign = 'top';
			document.body.insertBefore(table, document.body.children[0]);
		}

		function checkDateValidity(strDate, curWeek) {

			var error = 0;
			if(strDate.trim().length > 10) {
				error = 1;
			}

			if(strDate.trim().length < 10) {
				error = 2;
			}

			var temp = strDate.split('-');
			if(temp.length > 3) {
				error = 3;
			}else {

				var year = parseInt(temp[0]);
				var month = parseInt(temp[1]);
				var day = parseInt(temp[2]);

				var d = new Date();
				var curYear = d.getFullYear();
				if(year != curYear) {
					error = 4;
				}

				if(month < 1 || month > 12) {
					error = 5;
				}

				var lastDay = new Date(year, month, 0).getDate();

				if(day < 1 || day > lastDay) {
					error = 6;
				}

				currentDate = new Date(strDate);
				startDate = new Date(year, 0, 1);

				var days = Math.floor((currentDate - startDate) / (24 * 60 * 60 * 1000));
				var weekNumber = Math.ceil(days / 7);
				
				if(weekNumber != curWeek) {
					error = 7;
				}

			}


			return error;

		}

		function checkDateValidity1(strDate) {

			var error = 0;
			if(strDate.trim().length > 10) {
				error = 1;
			}

			if(strDate.trim().length < 10) {
				error = 2;
			}

			var temp = strDate.split('-');
			if(temp.length > 3) {
				error = 3;
			}else {

				var year = parseInt(temp[0]);
				var month = parseInt(temp[1]);
				var day = parseInt(temp[2]);

				var d = new Date();
				var curYear = d.getFullYear();
				if(year != curYear) {
					error = 4;
				}

				if(month < 1 || month > 12) {
					error = 5;
				}

				var lastDay = new Date(year, month, 0).getDate();

				if(day < 1 || day > lastDay) {
					error = 6;
				}

			}

			return error;

		}


</script>
