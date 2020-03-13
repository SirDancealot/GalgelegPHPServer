<?php
session_start();
$url = "dist.saluton.dk:20129";

function login($usr, $pass) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, "name=".$usr."&password=".$pass );
	curl_setopt($curl, CURLOPT_URL, $GLOBALS["url"]."/login");
	curl_setopt($curl, CURLOPT_TIMEOUT, 5);
	$response = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	curl_close($curl);
	return ($code==200?true:false);
}

function getData($usr) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HTTPGET, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_URL, $GLOBALS["url"]."/game/".$_SESSION["usr"]);
	curl_setopt($curl, CURLOPT_TIMEOUT, 5);
	$response = json_decode(curl_exec($curl), true);
	curl_close($curl);
	return $response;
}

function checkLogin($usr) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_HTTPGET, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_URL, $GLOBALS["url"]."/login/".$usr);
	curl_setopt($curl, CURLOPT_TIMEOUT, 5);
	$response = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	curl_close($curl);
	return ($code==200?true:false);
}

$loggedIn = false;

if (isset($_SESSION["usr"])) {
	if (checkLogin($_SESSION["usr"]))
		$loggedIn = true;
}

if (isset($_POST["usr"]) && !$loggedIn ) {
	$loggedIn = login($_POST["usr"], $_POST["pass"]);
	if ($loggedIn)
		$_SESSION["usr"] = $_POST["usr"];
}

if ( $loggedIn ) {
	$data = getData($_SESSION["usr"]);
?>
	<h1>Du er nu logget ind!</h1>
	<form method="post" action="/game.php">
	<input type="submit" name="gameChoise" value="Nyt spil"></input>
	<input type="submit" name="gameChoise" value="Fortsæt spil"></input>
	</form>
<?php
} else {
?>
	<h1>Velkommen til Galgelegs spillet!</h1>
	<h2>Log ind for at fortsætte</h2>
	<form method="post">
	<label for="usr">Username:</label><br>
	<input type="text" id="usr" name="usr"></input><br>
	<label for="pass">Password:</label><br>
	<input type="password" id="pass" name="pass"></input><br>
	<input type="submit" value="Submit"></input>
	</form>
<?php
}
?>
