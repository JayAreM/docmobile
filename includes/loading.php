	
	<style>
		.loadState1{
			top:0;
			left:0;
			height:100%;
			width:100%;
			margin:0 auto;
			position:fixed;
			opacity:.7;
			z-index:100;
		}
		.loadState2{
			width:200px;
			height:200px;
			position:fixed;
			top:40%;
			background:url('../images/ajaxloader.gif');
			background-repeat:no-repeat;
			background-size:100px 100px;
			background-position:48% 48%;
			z-index:100;
			display:none;
		}
	</style>
	

	
	<input id ="loadValue"  value = "0" type = "hidden">
	<div id ="state1"  class = "loadState1"></div>
	<div id ="state2" class = "loadState2"></div>
	
	
	
	<script> 
		document.getElementById('loadValue').value = "0";
		document.getElementById('state1').style.display = "none";
		
		
		
		function go1(){
			
			if(document.getElementById('loadValue').value  == '0'){
				document.getElementById('loadValue').value  = 1;
				document.getElementById('state2').style.display = "block";
				document.getElementById('state1').style.display = "block";
				document.getElementById('state2').style.left = (document.body.offsetWidth /2 -100) + "px";
			}else{
				document.getElementById('loadValue').value  = 0;
				document.getElementById('state2').style.display = "none";
				document.getElementById('state1').style.display = "none";
				
			}
		}
		
		
		
	</script>