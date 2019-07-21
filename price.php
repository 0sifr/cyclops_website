<html>											<!-- price.php -->
<title> Calculator </title>
<div id="content1" style="background-color:#ffae00;height:800px;width:1050px;float:left;">
	
<!-- TEXT ----------------------------------------------------------------------------------------------------------------------- -->
		<?php include("includes/text.html"); ?>

<!-- DISTANCE CALCULATOR -------------------------------------------------------------------------------------------------------- -->
	<h2>Distance-Price Calculator</h2>
	<body>
	<form action="<?php $_SERVER['PHP_SELF']; ?>">		<!-- submit the form data back to the page -->

		Please input customer pickup address or postcode:			
				<input type="text" name="origin" size="15"
 					value=
 						"<?php if ($_GET['origin']) {			//sticky form
							echo $_GET['origin'];
							}else{
							echo 'WC1E7DB';	} 					//with a default value
						 ?>" 
				/> <br> 
		Please input customer delivery address or postcode:			
				<input type="text" name="destination" size="15"
					value=
						"<?php if ($_GET['destination']) echo $_GET['destination']; //sticky form	
						 ?>" 
				/>
				<input type="submit" value="Calculate">
	</form>

<!-- GOOGLEMAPS API ---------------------------------------------------------------------------------------------------------- -->
	<?php			#get postcode & send googlemapsAPI http request		   

	//input the variables for the googlemapsAPI
		//$destination="se19tg";
		$origin=$_GET['origin'];
		$destination=$_GET['destination'];
		$origin_url = str_replace(' ', '_', $origin);		//replace any spaces with underscores (URLS cannot contain whitespace!)
		$destination_url = str_replace(' ', '_', $destination);		//replace any spaces with underscores (URLS cannot contain whitespace!)
		$APIkey="<insert_key_here!!!>";
		$url='https://maps.googleapis.com/maps/api/directions/json?origin='.$origin_url.'&destination='.$destination_url.'&region=uk&sensor=false&key='.$APIkey;
		
		if ($destination) {												//only do http request if destination is input
			$json = file_get_contents($url);					//do http request to googlemapsAPI server
			}
  	  
	//	 $json2 = json_decode($json,true);

	//process googlemapsAPI JSON response using PHP    
		//strstr &#151; Find the first occurrence of a string
		$haystack =	$json;		//The input string		
		$needle = "value";		//If needle is not a string, it is converted to an integer and applied as the ordinal value of a character
			$distance1 = strstr ( $haystack , $needle);
			$distance2 = substr ($distance1, 8, 5);		//cut a string after X characters: substr ($original, $start, $length)
	
			$string = substr ($distance1, 8, 80);			//cut a string after X characters: substr ($original, $start, $length)
			$distance = filter_var($string, FILTER_SANITIZE_NUMBER_INT);
			$distance_miles = number_format((float)($distance/1609.344), 1, '.', ''); 		//echo distance in metres and miles (1 d.p.)
			
		if (!isset($_GET['origin'])) {$origin='WC1E7DB';}		//if no data is submitted to form, $origin='WC1E7DB'   
		if ($origin=='WC1E7DB')	{										
			$origin='42 Store St, London, WC1E 7DB';				//display 'WC1E7DB' as '42 Store St, London, WC1E 7DB' 
			}
		echo "Distance from: <b>".$origin."</b> 
				<br>To <b>".$destination."</b>: <br /> <b>".$distance_miles." miles</b> (route as calculated by googlemaps)";
	?>
	<br />

<!-- PRICE CALCULATOR ------------------------------------------------------------------------------------------------------------>
		
		Speed: 							10mph 		<br />
		Arrival & pick-up time: 	10 minutes 	<br />
		
		<?php													#DELIVERY TIME
		$speed=((10*1600)/60);							//266.67 metres per minute
		$aptime=10;											//arrival and pickup time is 10 mins
		$dtime=$distance/$speed;						//time = distance/speed
		$dtime=$dtime+5;									//add 5 mins to delivery time
		echo "Delivery time + 5 mins: <b>"			//delivery time (mins)rounded to 1 d.p.
					.number_format((float)$dtime, 1, '.', '').
					" minutes</b>";
		?>	<br />

		<?php													//Overall time
			$otime=$dtime+$aptime;
			if ($destination) {
				echo "Overall time: <b>".number_format((float)$otime, 1, '.', '')." minutes</b><br />";						//overall time (mins)rounded to 1 d.p.
				}
		?>
	
Price per hour: 				&pound;16 <br />
	
	<!-- The overall cost (written in green)-->
			
				<?php
				$price_ph=16;																	//Set delivery price per hour
				$otime=$otime/60;																//overall time in mins -> overall time in hours
				$ocost=$otime*$price_ph;													//(overall time (hours) X price per hour
				$ocost=round($ocost,1);														//round to 1 d.p.
				$ocost=number_format((float)$ocost, 2, '.', '');					//then display $ocost with 2 d.p. (with floating point)
				
				?>

<!-- ERROR MESSAGES ---------------------------------------------------------------------------------------------------------->

<?php
//error1: Please input destination address or postcode
		if ($destination) {																		//is desination submitted or ($_GET['postcode'])=NULL? 
			$statement="Overall cost: &pound;".$ocost;						//
			}else{
			$error1=1;	}

//error2: Destination not found
	$error2_position = strpos($json, 'NOT_FOUND');				// in $json, find string POSITION of text: 'NOT_FOUND' 
	if ($error2_position !== false) {
		$error2=1;															//if found, SET variable: $error2
		}else{
		unset($error2);	}												//otherwise UNSET variable: $error2

//error3: Distance is too far - "please contact us for a quote at email@address.com" 
	if ($distance_miles > 5) {
		$error3=1;															//if found, SET variable: $error3
		}else{
		unset($error3);	}												//otherwise UNSET variable: $error3
		
if ($error1) {										//error1:	destination not input
	$statement="Please input destination address or postcode";}
if ($error2) {										//error2:	destination not found
	$statement="Destination not found";}
if ($error3) {										//error3:	distance too far
	$statement="distance is more than 5 miles <br> please contact us for a quote at email@address.com";}

// FINALLY PRINT STATEMENT!!!!! ---------------------------------------------------------------------------------

$statement="<font size='4' color='green'><b><div style='text-align: center'>".$statement."</div></b></font>";	//add html formatting to statement
echo $statement;															//Echo overall cost statement

// DEBUGGING ---------------------------------------------------------------------------------------------------- 
//echo "<b>Debugging:</b><br/>";
//echo $json;
?>

<!-- ADD: 
	security features eg. stripslashes

Prices section/quote section (public)
Node 1: [please input customer pickup point]
Node 2: [please input customer delivery point]

Customer section
Node 1: [customer pickup point is set]
Node 2: [please input customer delivery point] 
-->
	
</div>

<!-- 000webhost advert ------------------------------------------------------------------------------------------------------>
		<?php include("includes/000webhostad.html");	?>

</html>
