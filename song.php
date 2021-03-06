<?php
// try to connect to the server.
include("config.php");
include("header.php");
?>

	
<?php
// what song was passed in?
$songid = $_REQUEST['songid'];
$songquery = "SELECT * FROM `enthusiasticpanther_songs` where id='$songid'";

// if we get a result, let's do stuff:
if ($songresult = mysqli_query($link, $songquery)) {
	while ($songobj = mysqli_fetch_object($songresult)) {		
			$songname = $songobj->name;
		}
}

// everything about the show
echo "<div id='header'>$songname</div>";
				
// this query is going to load everything about the songlist
$query = "
SELECT DISTINCT name, showid, songid, performances.quality, location
FROM enthusiasticpanther_songperformances performances 
INNER JOIN enthusiasticpanther_songs songs 
INNER JOIN enthusiasticpanther_shows shows
WHERE performances.songid = songs.id
AND songid = $songid 
AND shows.id = performances.showid 
ORDER BY performances.id
";

$songid = $_REQUEST['songid'];
$prev = $songid - 1;
$next = $songid + 1;

echo "<br /><br /><a href='song.php?songid=$prev'>prev</a> | <a href='song.php?songid=$next'>next</a><br /><br />";

// if we get a result, let's do stuff:
if ($result = mysqli_query($link, $query)) {
	while ($obj = mysqli_fetch_object($result)) {      
      $name = $obj->name;   
      $showid = $obj->showid;
      $quality = $obj->quality;  
      $songid = $obj->songid;     
      $location = $obj->location;           
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
      echo "<td><a href='show.php?showid=$showid'>$showid. $location</a></td>"; 	
	  echo "<td class='quality $color'>$quality</td>";
	  echo "</tr>";
	}
}

$avgquery = "SELECT avg(quality) as avgquality FROM `enthusiasticpanther_songperformances` where songid='$songid'";
    
// if we get a result, let's do stuff:
if ($avgresult = mysqli_query($link, $avgquery)) {
	while ($avgobj = mysqli_fetch_object($avgresult)) {		
			$average = (int) $avgobj->avgquality;						
			echo "<tr class='total'><td>Song popularity</td><td class='quality'>$average</td></tr>";
		}
}
echo "</table>\r\r";
?>