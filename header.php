<?php

header('Content-Type: text/html; charset=utf-8');

// connect to the server	
$link = mysqli_connect($host, $username, $password, $db);
// make sure we can really connect. If not, show error message.
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

function timeround($seconds) {
  $t = round($seconds);
  return sprintf('%01d:%02d', ($t/60%60), $t%60);
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />	
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
