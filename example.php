<?php

require("CasparServerConnector.php"); // require the file containing the class on every page that needs it

$connector = new CasparServerConnector("127.0.0.1", 5250); // all communication to the server will now be done through this object
$message = "";
$response = FALSE;

if (isset($_POST['button']))
{
	if ($_POST['button'] == "play")
	{
		$response = $connector->makeRequest('LOADBG '.intval($_POST['channel'], 10).'-'.intval($_POST['layer'], 10).' "'.CasparServerConnector::escapeString($_POST['filename']).'" AUTO');
		// CasparServerConnector::escapeString means call the escapeString function on the class. It is a static function.
		// the string needs escaping to make sure that any characters that may appear in the file name which are also AMCP control characters are escaped.
		// it's not really a great example. If you were setting data with UPDATE then you would definately need to put the xml string through this first.
	}
	else if ($_POST['button'] == "getInfo")
	{
		$response = $connector->makeRequest('INFO '.intval($_POST['channel'], 10).'-'.intval($_POST['layer'], 10));
	}
	
	if ($response['status'] >= 200 && $response['status'] <= 202)
	{
		$message = "The command was executed successfully!";
	}
	else
	{	
		$message = "Something went wrong. The server returned the status code: ".$response['status'];
	}
}
$connector->closeSocket();
?>
<html>
<head>
</head>
<body>
<?php
	if ($message != "")
	{ ?>
	<div><strong><?php echo($message);?></strong></div>
	<?php } ?>
	<form action="example.php" method="post">
		<label>File name (no extension): <input type="text" name="filename" value="AMB"></label>
		<label>Channel: <input type="number" name="channel" value="1" min="1"></label>
		<label>Layer: <input type="number" name="layer" value="1" min="1"></label>
		<button type="submit" name="button" value="play">Load and Play</button>
		<button type="submit" name="button" value="getInfo">Get Info</button>
	</form>
<?php
if ($response !== FALSE)
{ ?>
	<p>The following is the response form the server:</p>
	<pre>
Status code: <?php echo($response['status']."\n\n");?>
Data: <?php echo($response['response'] !== FALSE ? "\n".htmlentities($response['response']) : "No data was returned.");?>
<?php } ?>
	</pre>
</body>
</html>