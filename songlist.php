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
$mostrecentshow = 7;





// make sure we can really connect. If not, show error message.
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

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
      
      
      // now we need to go get other details
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
      // SELECT count(id) FROM `enthusiasticpanther_songperformances` where songid = 3 
      
      /*
	SELECT  (
    SELECT count(id) FROM `enthusiasticpanther_songperformances` where songid = 3 
    ) AS songperformances,
    (
    SELECT COUNT(*)
    FROM   enthusiasticpanther_songperformances
    ) AS totalperformances2,
    (
    SELECT COUNT(*)
    FROM   enthusiasticpanther_songperformances
    ) AS totalperformances3
    */
    
      
      
	  echo "</tr>";
	  
	  /*
	  echo "<tr><td>&nbsp;</td><td colspan=4>";
	  // now we need to go get other details
	  $subquery = "SELECT 
				    date, showid, songid, performances.quality
					FROM
					    enthusiasticpanther_songperformances performances
					INNER JOIN enthusiasticpanther_shows shows
					    ON shows.id = performances.showid
					WHERE songid=$id";
	  if ($subresult = mysqli_query($link, $subquery))
	  {
		  while ($subobj = mysqli_fetch_object($subresult))
		  {
			$id = $subobj->id;
			$showid = $subobj->showid;
			$quality = $subobj->quality;
			$date = $subobj->date;

		  	// echo "Show #$showid: $quality/100";
			echo "$date: $quality/100";
						  				
			// let's figure out the gap
			$gap = ($showid - $previousPerformance);
			if ($gap > 1)
			{
				$gap--; // if you play a show on the 3rd and 5th, that's a 1 show gap
				echo " ($gap show gap)";	
			}
			echo "<br />";
			
		  	$previousPerformance = $showid;
		  }
		  if ($previousPerformance == 7)
		  {
			  // 		 
		  }else{
			  $gap = $mostrecentshow-$previousPerformance;
			  echo "$gap song gap and counting...";
		  }
	  }
	  */
	  echo "</td></tr>";
	  
	  
	
	}

	echo "</table>";
		
}


	
	
?>