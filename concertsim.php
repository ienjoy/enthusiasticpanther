	<?php

// try to connect to the server.
include("config.php");
include("header.php");

$link = mysqli_connect("localhost", "$username", "$password", "$db");

// make sure we can really connect. If not, show error message.
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// 
if ($_REQUEST["debug"] == "true")
{
	echo "<p style='color: red'>Nothing submitted to the database, just testing...</p>";
}

// the song should be between 10-14
$concertLength = rand(11,15);

// get the most recent concert ID and then add one to it
$query_recentConcert = "SELECT showid FROM enthusiasticpanther_songperformances ORDER BY id DESC LIMIT 0, 1";
if ($result_recentConcert = mysqli_query($link, $query_recentConcert))
{
	while ($obj_recentConcert = mysqli_fetch_object($result_recentConcert))
	{
	  $lastShowID = $obj_recentConcert->showid;
	  $nextShowID = $lastShowID+1;
	}
}

// get the most recent concert date
$query_nextDate = "SELECT date FROM `enthusiasticpanther_shows` where id = $nextShowID";
if ($result_nextDate = mysqli_query($link, $query_nextDate))
{
	while ($obj_nextDate = mysqli_fetch_object($result_nextDate))
	{
	  $nextDate = $obj_nextDate->date;
	}
}

// figure out what day it is. anything to update today?
$currentDatestamp = date('Y-m-d');

// debugging
// $currentDatestamp = "2020-12-11";
// $nextDate = "2020-12-01";

echo "<h2>Show #$nextShowID</h2>";
echo "<p>Number of songs: $concertLength<br />";
echo "$currentDatestamp: Today<br />";
echo "$nextDate: Next show</p>";


// then we want to determine if that date is logged in our cities list.
if ($nextDate <= $currentDatestamp || $_REQUEST["debug"] == "true")
{	
	// make a random playlist
	$query = "SELECT * FROM enthusiasticpanther_songs where weighting != 0 ORDER BY RAND()/weighting limit $concertLength";
	
	// if we get a result, let's do stuff:
	if ($result = mysqli_query($link, $query)) {		
		$showRating = 0;
		$showIterator = 0;
				
		while ($obj = mysqli_fetch_object($result)) {		  
			$name = $obj->name;   
			$id = $obj->id; 
			$songid = $obj->songid;		  
			echo "<b>$name:</b> ";		  
		  
			$subquery = "SELECT  (
			SELECT AVG(quality) FROM enthusiasticpanther_songperformances where songid=$id
			) AS quality,    
			(
			SELECT max(id) FROM `enthusiasticpanther_shows` where `DATE` <= CURDATE()
			) AS latestconcert,
			(
			SELECT max(showid) FROM `enthusiasticpanther_songperformances` where songid = $id
			) AS latestperformance
			";

			if ($subresult = mysqli_query($link, $subquery))
			{
			  while ($subobj = mysqli_fetch_object($subresult))
			  {
				  $historicalQuality = (int) $subobj->quality;
				  
				  if ($historicalQuality < 1)
				  {
					  $historicalQuality = 50;
					  $gap = 0;
					  $gapBonus = 1;
				  }else{
					$latestconcert = $subobj->latestconcert;
					$latestperformance = $subobj->latestperformance;				
					$gap = $latestconcert-$latestperformance;
								
					$gapBonus = 1+($gap/$latestconcert); 			 						  
				  }			   
			  }
			}
		  
		  // $quality = bellWeightedRnd(1,100); // this is fully random. I want to base it on previous numbers
	
		  // we need to make a range
		  $historicalQuality_lowerRange = $historicalQuality-25;
		  $historicalQuality_higherRange = $historicalQuality+25;
	
	
		  
		  // now make sure it's not above 100 or below 1
		  if ($historicalQuality_higherRange > 100)
			  $historicalQuality_higherRange = 100;
	
		  if ($historicalQuality_lowerRange < 1)
			  $historicalQuality_lowerRange = 1;
			  
		  // but new songs have no range yet. So you should set them to 1,00
		  if (!isset($historicalQuality))
		  {
			  $historicalQuality_lowerRange = 1;
			  $historicalQuality_higherRange = 100;  
		  }	
			  
		  	
		  // $quality = bellWeightedRnd(1,100); // this is fully random. I want to base it on previous numbers
		  $quality = bellWeightedRnd($historicalQuality_lowerRange, $historicalQuality_higherRange);	
		// echo ")<br />";
		
		// let's multiply quality and the gapbonus now:
		$quality_afterBonus = (int) ($quality*($gapBonus));
		if ($quality_afterBonus > 100)
		{
			$quality_afterBonus = 100;
		}
		
		/* echo "Range: $historicalQuality_lowerRange ---- ($quality_afterBonus) ---- $historicalQuality_higherRange<br /><br />"; */
		echo "$quality_afterBonus <br/>";

		
	
		
		// let's debug this.
		if ($_REQUEST["debug"] != "true")
		{
			$query = "INSERT INTO enthusiasticpanther_songperformances  (`id`, `showid`, `songid`, `quality`) VALUES (NULL, '$nextShowID', '$id', '$quality_afterBonus');";
			echo "<br />$query<br /><hr />";	
		}
		
		
		
		
		// echo "<br />quality after: from $historicalQuality -> $quality -> $quality_afterBonus ($gap gap = $gapBonus)<br />";
		
		
		// this is the line that actually makes it happen! Woo!	
		if ($_REQUEST["debug"] != "true")
		{
			mysqli_query($link, $query) or die(mysql_error());
		}
	
	
	
		/*
		echo "<br />";
		echo "<br />";
		*/
		
		// now we're going to get a quality rating for the show itself
		
		/*
		echo "Show rating: $showRating<br />";
		echo "Quality: $quality<br />";
		echo "Iterator: $showIterator<br />";
		*/
		
		$showRating = $showRating + $quality;
	
		// echo "Show rating: $showRating<br />";
		// echo "<br />";
	
		$showIterator++;
		
		}
	
		$finalShowScore = $showRating/$showIterator;
	
		echo "<br />";
		echo "Final show score: ".$finalShowScore;
		
		// INSERT INTO enthusiasticpanther_shows (`id`, `date`, `quality`) VALUES (NULL, '1', '2020-01-14', '50')
		// but really this should be an update
		
	
	}
	
	
	// INSERT INTO `enthusiasticpather_performances` (`id`, `showid`, `songid`, `quality`) VALUES (NULL, '1', '3', '50');
	
	 
	
	// echo $query;
	
	
	
}else{
	
	echo "nothing to be done.";
	echo date('Y-m-d H:m:s');
	
	if (date_default_timezone_get()) {
		echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
	}
	
}



	
	
function random_0_1()
{
    //  returns random number using mt_rand() with a flat distribution from 0 to 1 inclusive
    //
    return (float) mt_rand() / (float) mt_getrandmax() ;
}

function random_PN()
{
    //  returns random number using mt_rand() with a flat distribution from -1 to 1 inclusive
    //
    return (2.0 * random_0_1()) - 1.0 ;
}


function gauss()
{
    static $useExists = false ;
    static $useValue ;

    if ($useExists) {
        //  Use value from a previous call to this function
        //
        $useExists = false ;
        return $useValue ;
    } else {
        //  Polar form of the Box-Muller transformation
        //
        $w = 2.0 ;
        while (($w >= 1.0) || ($w == 0.0)) {
            $x = random_PN() ;
            $y = random_PN() ;
            $w = ($x * $x) + ($y * $y) ;
        }
        $w = sqrt((-2.0 * log($w)) / $w) ;

        //  Set value for next call to this function
        //
        $useValue = $y * $w ;
        $useExists = true ;

        return $x * $w ;
    }
}

function gauss_ms( $mean,
                   $stddev )
{
    //  Adjust our gaussian random to fit the mean and standard deviation
    //  The division by 4 is an arbitrary value to help fit the distribution
    //      within our required range, and gives a best fit for $stddev = 1.0
    //
    return gauss() * ($stddev/4) + $mean;
}

function gaussianWeightedRnd( $LowValue,
                                 $maxRand,
                                 $mean=0.0,
                                 $stddev=2.0 )
{
    //  Adjust a gaussian random value to fit within our specified range
    //      by 'trimming' the extreme values as the distribution curve
    //      approaches +/- infinity
    $rand_val = $LowValue + $maxRand ;
    while (($rand_val < $LowValue) || ($rand_val >= ($LowValue + $maxRand))) {
        $rand_val = floor(gauss_ms($mean,$stddev) * $maxRand) + $LowValue ;
        $rand_val = ($rand_val + $maxRand) / 2 ;
    }

    return $rand_val ;
}

function bellWeightedRnd( $LowValue,
                             $maxRand )
{
    return (int) gaussianWeightedRnd( $LowValue, $maxRand, 0.0, 1.0 ) ;
}


for($i = 0 ; $i < 1000 ; $i++)
{
	// echo bellWeightedRnd(1,100);
	// echo "<br />";
}

	
	
?>