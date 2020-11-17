<?php
// try to connect to the server.
include("config.php");
include("header.php");
?>


<?php
$showid = $_REQUEST['showid'];
    
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
FROM enthusiasticpanther_songs songs
INNER JOIN enthusiasticpanther_songperformances performances
WHERE performances.songid = songs.id
AND showid = '$showid'
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
	// $age = $obj->age; 
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

$agequery = "
SELECT avg(songid) as age
FROM enthusiasticpanther_songs songs
INNER JOIN enthusiasticpanther_songperformances performances
WHERE performances.songid = songs.id
AND showid = '$showid'
ORDER BY performances.id
";
// if we get a result, let's do stuff:
if ($ageresult = mysqli_query($link, $agequery)) {
	while ($ageobj = mysqli_fetch_object($ageresult)) {		
		$age = (int) $ageobj->age; 						
		echo "<tr class='total'><td>Song age</td><td class='quality'>$age</td></tr>";
	}
}


echo "</table>\r\r";
?>