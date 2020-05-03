<?php
// try to connect to the server.
include("config.php");
include("header.php");
?>

<html>
<head>	
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<?php

// this query is going to load everything
$query = "SELECT * FROM enthusiasticpanther_songs";

// if we get a result, let's do stuff:
if ($result = mysqli_query($link, $query)) {
	
	echo "<table>";
	echo "<tr>";
	echo "<td><b>ID</b></td>";
	echo "<td><b>Song</b></td>";
	echo "<td><b>Duration</b></td>";
	echo "<td><b>Performances</b></td>";
	echo "<td><b>Gap</b></td>";
	echo "<td><b>Popularity</b></td>";
	echo "</tr>";
            	
	while ($obj = mysqli_fetch_object($result)) {
		
		$name = $obj->name;   
		$id = $obj->id;
		$standard_duration = $obj->standard_duration;
		
		echo "<tr>";
		echo "<td>$id</td>";
		echo "<td>$name</td>";
		$time = timeround($standard_duration);
		echo "<td>$time</td>";
            
		// now we need to go get other details per song
		$subquery = "SELECT  (
		SELECT count(id) FROM `enthusiasticpanther_songperformances` where songid = $id 
		) AS totalperformances,
		(
		SELECT avg(quality) FROM `enthusiasticpanther_songperformances` where songid = $id
		) AS averagequality,
		(
		SELECT max(id) FROM `enthusiasticpanther_shows`
		) AS latestconcert,
		(
		SELECT max(showid) FROM `enthusiasticpanther_songperformances` where songid = $id
		) AS latestperformance
		";
		
    
		if ($subresult = mysqli_query($link, $subquery))
		{
		  	while ($subobj = mysqli_fetch_object($subresult))
		  	{
			  $totalperformances = $subobj->totalperformances;
			  $averagequality = (int) $subobj->averagequality;
			  $latestconcert = $subobj->latestconcert;
			  $latestperformance = $subobj->latestperformance;			  
			  $gap = $latestconcert-$latestperformance;	  
		  	}	 
		}
     
      echo "<td>$totalperformances</td>";
      echo "<td>$gap</td>";
      
      if ($averagequality < 40)
      {
	  	$qualitycolour = "red";
	  }elseif ($averagequality < 50){
		$qualitycolour = "orange";  	
      }elseif ($averagequality > 65){
		$qualitycolour = "green";			    
      }else{
	  	$qualitycolour = "black";
      }      
      echo "<td style='color: $qualitycolour'> $averagequality</td>";
	  echo "</tr>";	 
	}
	echo "</table>";		
}


	
	
?>