<style>

BODY
{
	font-family: helvetica;
	margin: 24px;
	font-size: 14px;
}	
	
.playlistTable
{
	width: 244px;
	border-collapse: collapse;
}	

A
{
	color: black;
	text-decoration: none;
}


TD
{
	border-bottom: 1px solid #ccc;
}

.row
{
font-size: 14px;
line-height: 24px;
width: 100%;
}

.quality
{
	text-align: right;
}

.red
{
	color: red;
}

.blue
{
	color: blue;
}

.total
{
	background-color: #efefef;
	font-size: 14px;
	line-height: 24px;
}

#header
{
	text-transform: uppercase;
	font-weight: bold;
	margin-bottom: 30px;
	font-size: 18px;
}
	
</style>

<?php
	
	
// try to connect to the server.
include("config.php");
$link = mysqli_connect("localhost", "$username", "$password", "$db");

	
$showid = $_REQUEST['showid'];


function timeround($seconds) {
  $t = round($seconds);
  return sprintf('%01d:%02d', ($t/60%60), $t%60);
}


// make sure we can really connect. If not, show error message.
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// everything about the show

$showquery = "SELECT * FROM `enthusiasticpanther_shows` where id='$showid'";
    
    // if we get a result, let's do stuff:
if ($showresult = mysqli_query($link, $showquery)) {
	
	
	
	while ($showobj = mysqli_fetch_object($showresult)) {
		
			$date = $showobj->date; 
			$location = $showobj->location; 
						
			echo "<div id='header'>
				$location<br />
				$date
				</div>";

			
		}
}		


// this query is going to load everything about the songlist
$query = "
SELECT name, showid, songid, quality
FROM
enthusiasticpanther_songs songs
INNER JOIN enthusiasticpanther_songperformances performances
WHERE performances.songid = songs.id
AND
showid = '$showid'
ORDER BY performances.id
";



	$showid = $_REQUEST['showid'];
	
	$prev = $showid - 1;
	$next = $showid + 1;
	
	echo "<br /><br /><a href='show.php?showid=$prev'>prev</a> | <a href='show.php?showid=$next'>next</a><br /><br />";



// if we get a result, let's do stuff:
if ($result = mysqli_query($link, $query)) {
	

	
	while ($obj = mysqli_fetch_object($result)) {
      
      $name = $obj->name;   
      $showid = $obj->showid;
      $quality = $obj->quality;  
      $songid = $obj->songid;     
      
      
    // need to compare to the previous score quality
	$subquery = "SELECT * FROM `enthusiasticpanther_songperformances` WHERE songid='$songid' and showid < $showid ORDER BY id DESC LIMIT 1";
	if ($subresult = mysqli_query($link, $subquery)) {
			while ($subobj = mysqli_fetch_object($subresult)) {
				 $historicalquality = $subobj->quality; // what should the song be?
				 if ($historicalquality <= $quality) // is this worse?
				 {
					 $color = "blue";
				 }else{
					 $color = "red";
				 }
				
			}
		}
      
      echo "<table class='playlistTable'>" ;
      
      echo "<tr class='row'>";  
      echo "<td><a href='song.php?songid=$songid'>$name</a></td>"; 	
      
	  // echo "$historicalquality - $quality<br />";
					 
	  echo "<td class='quality $color'>$quality</td>";
	  echo "</tr>";
	}


		
}


$avgquery = "SELECT avg(quality) as avgquality FROM `enthusiasticpanther_songperformances` where showid='$showid'";
    
    // if we get a result, let's do stuff:
if ($avgresult = mysqli_query($link, $avgquery)) {
	
	
	
	while ($avgobj = mysqli_fetch_object($avgresult)) {
		
			$average = (int) $avgobj->avgquality; 
						
			echo "<tr class='total'><td>Concert rating</td><td class='quality'>$average</td></tr>";

			
		}
}

	echo "</table>\r\r";

	
	
?>