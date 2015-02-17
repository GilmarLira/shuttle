<?php
include('link.php');
// Connecting to mySQL database
$link = mysql_connect($db_host, $db_username, $db_password);
if (!$link) {
	die('Could not connect: ' . mysql_error());
	}
$db_selected = mysql_select_db($db_database, $link);
if (!$db_selected) {
    die ('Can\'t use foo : ' . mysql_error());
}

// Setters and Getters
$self = $_SERVER['PHP_SELF'];
$origin = $_POST["origin"]; 
$destination = $_POST["destination"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />

	<meta name="viewport" content="user-scalable=no, width=device-width" />

	<title>AAU Shuttle Schedule</title>

	<link rel="stylesheet" type="text/CSS" href="css/reset.css" />
	<link rel="stylesheet" type="text/CSS" href="css/structure.css" />
	<link rel="stylesheet" type="text/CSS" href="css/style.css" />
<!-- 	<link rel="stylesheet" type="text/CSS" href="css/test.css" /> -->
		
	<link rel="apple-touch-icon" href="apple-touch-icon.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="apple-touch-icon-iphone4.png" />
	
	<script src="js/jquery-1.10.1.js"></script>
	<script src="js/jquery.easing.1.3.js"></script>

	<script src="js/interactions.js"></script>
<!-- 	<script src="js/test.js"></script> -->
	
</head>
<body>
	<div id="container">
		<div id="lister">

			<header>
				<div class="search"> 
<!-- !Search form -->				
<?php
echo ('<form name="search" action="'.$self.'" method="post">
		<select name="origin" onchange=this.form.destination.focus()><option>ORIGIN:</option>');
	
	// Obtaining list of origin AAU building from the database	
	$query = "select * from Buildings order by street, number";
	$result=mysql_query($query, $link);
	if (!$result) {
		die ("Could not query the database: ".mysql_error());
	}
	while ($row = mysql_fetch_assoc($result)){
		echo '<option value="'.$row["Building_ID"].'" ';
		if (!$origin){
			if ($row["Building_ID"] == 9) {
				echo 'selected="selected"';	
			} 
		} else {
			if ($row["Building_ID"] == $origin) {
				echo 'selected="selected"';
			}
		}
		echo '>'.$row["Acronym"].'</option>', PHP_EOL;
	}
	echo '</select>';
	
	// Obtaining list of destination AAU buildings from the database (which is different from origin)
	echo '<select name="destination" onchange="this.form.submit()"><option>DESTINATION:</option>';	
	$query = "select * from Buildings order by street, number";
	$result=mysql_query($query, $link);
	if (!$result) {
		die ("Could not query the database: ".mysql_error());
	}
	while ($row = mysql_fetch_assoc($result)){
		echo '<option value="'.$row["Building_ID"].'" ';
		
		if ($row["Building_ID"] == $destination) {
			echo 'selected="selected"';
		}
	
		echo '>'.$row["Acronym"].'</option>', PHP_EOL;
	}
	echo '</select>';					
?>
				</div>
	
	<!-- !List results -->
			<div class="filters">
	<!-- 		<div class="bit"></div> -->
				<h4 class="oneoffour column line">line</h4>
				<h4 class="oneoffour column departure">departs</h4>
				<h4 class="oneoffour column arrival">arrives</h4>
				<h4 class="oneoffour column timer"><span>timer</span></h4>
			</div>
		
		</header>		
		<div class="results">
			
<?php
	if ($origin != NULL && $destination != NULL) {
		$now = date("G:i");
/* 		$now = '07:00';		 */
		$queryMaster = "SELECT Routes.Route, left(S1.Time, 5) AS Departure, left(S2.Time, 5) AS Arrival,
						timediff(S1.Time, curtime()) as delta
						FROM Schedule AS S1						
						INNER JOIN Schedule AS S2 ON S1.Route_ID = S2.Route_ID
						AND S1.Building_ID =  '".$origin."'
						AND S2.Building_ID =  '".$destination."'
						AND S1.Time >  '".$now."'
						AND S1.Time < S2.Time
						INNER JOIN Routes ON Routes.Route_ID = S1.Route_ID
						AND Routes.Route_ID = S2.Route_ID
						AND Routes.MF =  '1'
						GROUP BY Departure
						ORDER BY Departure ASC						
						";
	
		$resultMaster = mysql_query($queryMaster, $link);
		if (!$resultMaster) {
			die ("Could not do the master query: ".mysql_error());
		}
		
		// Printing search results
		$masterResults=0;		
		while ($row = mysql_fetch_assoc($resultMaster)) {
			$masterResults++;			
			if (substr($row['delta'], 0, 2) == 00) {
				$hourFlag=1;				
				if (substr($row['delta'], 3, 2) < 2) {
					$delta = substr($row['delta'], 4, 1);
					$display = $delta;					
					$scope = " minute";
				} else {
					$scope = " minutes";
					if (substr($row['delta'], 3, 2) < 10) {
						$delta = substr($row['delta'], 4, 1);						
						$display = $delta;
					} else {					
						$delta = substr($row['delta'], 3, 2);
						$display = $delta;
					}
				}
				
			} else {
				$hourFlag=0;
				if(substr($row['delta'], 0, 2) == 01) {
					$scope = "remaining";		
					$delta = substr($row['delta'], 1, 1);
					$display = $delta."h";
				} else {					
					$scope = "remaining";
					$delta = (substr($row['delta'], 0, 2) + 0);
					$display = $delta."h";
				}
			}

			echo ('<div id="result'.$masterResults.'" class="list');
			/* if ($masterResults == 1) { echo " active";} */
			
			// Building result line
			echo ('">
						<h2 id="line'.$masterResults.'" class="oneoffour column line">'.$row['Route'].'</h2>
						<h3 class="oneoffour column time">'.$row['Departure'].'</h3>				
						<h3 class="oneoffour column time">'.$row['Arrival'].'</h3>
						<h2 class=" timer">'.$display.'</h2>
						<div class="circle"></div>				
						');
					
			if($hourFlag==1){
				echo ('				
						<canvas id="can'.$masterResults.'" ></canvas>
						');
			}
						
			echo ('</div>'), PHP_EOL;

			// Building canvas arc		
			if($hourFlag==1){
				echo ('<script type="text/javascript">						
							var can'.$masterResults.';
							var ctx'.$masterResults.';						
								
							can'.$masterResults.' = document.getElementById("can'.$masterResults.'");
							ctx'.$masterResults.' = can'.$masterResults.'.getContext("2d");						
							
							can'.$masterResults.'.width = CANVAS_SIZE * BACKING_SCALE; 
							can'.$masterResults.'.height = CANVAS_SIZE * BACKING_SCALE; 
									
							ctx'.$masterResults.'.strokeStyle = "white";
					    	ctx'.$masterResults.'.lineWidth = CANVAS_LINE_WIDTH * BACKING_SCALE ;
					    	ctx'.$masterResults.'.lineCap = "round";
					        
						    ctx'.$masterResults.'.arc(can'.$masterResults.'.width / 2, can'.$masterResults.'.height / 2, (CANVAS_RADIUS * BACKING_SCALE), (- Math.PI / 2)*'.$hourFlag.', (((2 * Math.PI) - ('.$delta.' * 6) * Math.PI / 180 )- (Math.PI / 2))*'.$hourFlag.', false);
					    	ctx'.$masterResults.'.stroke();
						</script>'), PHP_EOL;
			}
			// jQuery behavior prototype	

			echo ('<script type="text/javascript">
						$("#result'.$masterResults.'").click(function(){		
							$(this).addClass("active").siblings().removeClass("active");
							$("#detailer").removeClass("inactive").addClass("active");
							var j;
							$(".detail h1").css("visibility", "visible").text("'.$row['Route'].'");
							$(".countdown p").css("visibility", "visible").text("'.$display.' '.$scope.'");
							$(".schedule").css("visibility", "visible");
							$(".schedule i.departure").text("'.$row['Departure'].'");
							$(".schedule i.arrival").text("'.$row['Arrival'].'");');
						
				if($hourFlag==1){
					echo ('		
							var bigcan;
							var bigctx;
															
							bigcan = document.getElementById("bigcanvas");
							bigctx = bigcan.getContext("2d");						
							
							bigcan.width = BIG_CANVAS_SIZE * BACKING_SCALE;
							bigcan.height = BIG_CANVAS_SIZE * BACKING_SCALE;		
							bigctx.strokeStyle = "white";
					    	bigctx.lineWidth = BIG_CANVAS_LINE_WIDTH * BACKING_SCALE;
					    	bigctx.lineCap = "round";
					        
						    bigctx.arc(bigcan.width / 2, bigcan.height / 2, (BIG_CANVAS_RADIUS * BACKING_SCALE), (- Math.PI / 2)*'.$hourFlag.', (((2 * Math.PI) - ('.$delta.' * 6) * Math.PI / 180 )- (Math.PI / 2))*'.$hourFlag.', false);
					    	bigctx.stroke();					    	

					    	
					 ');
				} 			
					    	
			echo '});  </script>', PHP_EOL;
						
		}	
	} else {			
	
		// Print something here to avoid a completely blank screen
/*
		echo ('<div class="empty">	
				<h2 class="oneoffour column line"></h2>
						<h3 class="threeoffour column time">About this App</h3>				
						<h2 class=" timer">></h2>');
						
*/
	
	}
?>
				
			
		</div>
				
	</div>
		<div id="detailer" class="inactive">
			<div class="detail">
				<h1></h1>
				<canvas id="bigcanvas"></canvas>
				<div class="bigcircleback"></div>	
	 		</div>
	 		<div class="countdown">
				<p></p>
			</div>
			<div class="schedule">
				<p><i class="departure"></i>departure</p>
				<p><i class="arrival"></i>arrival</p>
			</div>
			<div class="stops">
				<ol>
					<!--
<li>466 townsend</li>
					<li>180 nm</li>
					<li>620 sutter</li>
					<li>860 sutter</li>
					<li>120 polk</li>
					<li>601 brannan</li>
-->
				</ol>
			</div>
		
	</div>
	</div>
</body>
</html>

