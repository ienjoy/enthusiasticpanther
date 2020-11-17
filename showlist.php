<?php
// try to connect to the server.
include("config.php");
include("header.php");
?>


<?php

// this query is going to load everything
$query = "SELECT * FROM `enthusiasticpanther_shows`";

// if we get a result, let's do stuff:
if ($result = mysqli_query($link, $query)) {
	
	echo "<table>";
	echo "<tr>";
	echo "<td><b>ID</b></td>";
	echo "<td><b>Date</b></td>";
	echo "<td><b>Location</b></td>";
	echo "<td><b>Fresh</b></td>";
	echo "<td><b>Score</b></td>";
	echo "</tr>";
            	
	while ($obj = mysqli_fetch_object($result)) {
		  
		$id = $obj->id;
		$date = $obj->date; 
		$location = $obj->location;
		
		$standard_duration = $obj->standard_duration;
		
		echo "<tr>";
		echo "<td>$id</td>";
		echo "<td>$date</td>";
		echo "<td>$location</td>";
		
            
		// now we need to go get other details per show
		$subquery = "SELECT  (
		SELECT DISTINCT avg(songs.id)
		FROM enthusiasticpanther_songs songs
		INNER JOIN enthusiasticpanther_songperformances performances
		WHERE performances.songid = songs.id
		AND showid = $id
		ORDER BY performances.id
		) AS avg,
		(
		SELECT max(songid)
		FROM enthusiasticpanther_songs songs
		INNER JOIN enthusiasticpanther_songperformances performances
		WHERE performances.songid = songs.id
		AND showid <= $id  
		ORDER BY `performances`.`songid`  DESC
		) AS latestsong,
		(
		SELECT max(songid)
		FROM enthusiasticpanther_songs songs
		INNER JOIN enthusiasticpanther_songperformances performances
		WHERE performances.songid = songs.id
		AND showid < $id
		ORDER BY `performances`.`songid`  DESC
		) AS fuck,
		(
		SELECT avg(quality) FROM `enthusiasticpanther_songperformances` WHERE showid = $id
		) AS showavg,
		(
		SELECT max(showid) FROM `enthusiasticpanther_songperformances` where songid = $id
		) AS latestperformance
		";
	    
		
		// first figure out the average age of the song
		// concert 1: 2
		// concert 2: 2.5
		
		// next figure out the oldest song played up until that show
		// concert 10: 5.8 -> 13
		// 13-5.8 = 6.2		

		// concert 1: 2 -> 3
		// 3-2 = 1
	    
	    
	    
		if ($subresult = mysqli_query($link, $subquery))
		{
		  	while ($subobj = mysqli_fetch_object($subresult))
		  	{
			  $totalperformances = $subobj->totalperformances;
			  
			  $avg = $subobj->avg;
			  $showavg = $subobj->showavg;
			  
			  $latestsong = $subobj->latestsong;
			  
			  $agescore = ($avg/$latestsong);			  
			  $agescore_percent = round((float)$agescore * 100 ) . '%';
			  $showavg_percent = round((float)$showavg * 1 ) . '%';
			  
			  
			  echo "<td>$agescore_percent</td>";
			  echo "<td>$showavg_percent</td>";
			  // echo "<td>$mostRecentSong_beforeThisShow | $avg | $latestsong | $agescore | $agescore2</td>";
			  
			  
			  $averagequality = (int) $subobj->averagequality;
			  // echo $averagequality;
			  
			  $latestconcert = $subobj->latestconcert;
			  $latestperformance = $subobj->latestperformance;			  
			  $gap = $latestconcert-$latestperformance;
		  	}	 
		}
     
	}
echo "</table>";		
}


	
	
?>