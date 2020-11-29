<?php
// try to connect to the server.
include("config.php");
include("header.php");
?>

<style>
h3
{
	font-size: 20px;
	font-weight: lighter;
}

TABLE
{
	padding: 0;
	border: 0;
	font-size: 20px;
	font-weight: lighter;
}

TD
{
	vertical-align: top;
}

TD A:hover
{
	color: blue;
}
</style>


<h2>The name of this make-believe band is ENTHUSIASTIC PANTHER</h2>

<h3>Hello! Enthusiastic Panther is a make-believe band with make-believe songs that plays make-believe concerts around the world.</h3>

<h3>The next concert takes place in [Location] in [time]. Join us!</h3>


<table width="100%">
	<tr>
		<td width="50%" align="center">
			<h2>Concert chronology</h2>
			<p>
				
				<?php
				
				// this query is going to load everything
				$query = "SELECT * FROM `enthusiasticpanther_shows` where `DATE` <= CURDATE()+1 order by id desc";
				
				// if we get a result, let's do stuff:
				if ($result = mysqli_query($link, $query)) {
					
					echo "<table width='100%'>";
				
								
					while ($obj = mysqli_fetch_object($result)) {
						  
						$id = $obj->id;
						$date = $obj->date; 
						$location = $obj->location;
						
						$standard_duration = $obj->standard_duration;
						
						echo "<tr>";
						echo "<td><a href='show.php?showid=$id'>$location</a></td>";
						
							
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
							  $agescore_percent = round((float)$agescore * 100 ) . '';
							  $showavg_percent = round((float)$showavg * 1 ) . '';
							  
							  
							  if ($showavg_percent < 50)
							  {
								  $qualitycolour = "red";
							  }elseif ($showavg_percent < 58){
								  $qualitycolour = "orange";  	
							  }elseif ($showavg_percent > 63){
								  $qualitycolour = "green";			    
							  }else{
								  $qualitycolour = "black";
							  }      
							  echo "<td style='color: $qualitycolour' align='right'> $showavg_percent</td>";
							  
							  // echo "<td align='right'>$showavg_percent</td>";
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
				
			</p>
		</td>
		<td width="50%" align="center"> 
			<h2>Songbook</h2>
			<p>
				
				
				<?php
				
				// this query is going to load everything
				$query = "SELECT * FROM enthusiasticpanther_songs order by id";
				
				// if we get a result, let's do stuff:
				if ($result = mysqli_query($link, $query)) {
					
					echo "<table width='100%'>";
								
					while ($obj = mysqli_fetch_object($result)) {
						
						$name = $obj->name;   
						$id = $obj->id;
						$standard_duration = $obj->standard_duration;
						
						echo "<tr>";						
						echo "<td><a href='song.php?songid=$id'>$name</a></td>";
													
						// now we need to go get other details per song
						$subquery = "SELECT  (
						SELECT count(id) FROM `enthusiasticpanther_songperformances` where songid = $id 
						) AS totalperformances,
						(
						SELECT avg(quality) FROM `enthusiasticpanther_songperformances` where songid = $id
						) AS averagequality,
						(
						SELECT max(id) FROM `enthusiasticpanther_shows` where `DATE` <= CURDATE()+1
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
					echo "<td style='color: $qualitycolour' align='right'> $averagequality</td>";
					echo "</tr>";	 
					}
				echo "</table>";		
				}
				
				
					
					
				?>
				
				
							
			</p>
		</td>
	</tr>
</table>
	
	



<!--
<p>showlist.php -- all the shows in a row</p>


<h2>Showlist</h2>

<p>All the shows in a row.</p>

<h2>Show</h2>

<p>Show specific page.</p>

<h2>Songlist</h2>

<p>All songs in a row.</p>

<h2>Song</h2>

<p>Song specific page.</p>

<h2>Concertsim</h2>

<p>The thing that makes the magic work.</p>
-->