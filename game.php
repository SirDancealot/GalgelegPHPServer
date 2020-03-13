<?php
session_start();
$url = "dist.saluton.dk:20129";

function logout() {
	session_destroy();
	header("Location: /index.php");
	exit;
}

if (isset($_POST["logout"]))
	logout();

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

function resetGame($usr) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_URL, $GLOBALS["url"]."/game");
	curl_setopt($curl, CURLOPT_POSTFIELDS, "name=".$usr);
	curl_setopt($curl, CURLOPT_TIMEOUT, 5);
	$response = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	if ($code == 403)
		logout();
	curl_close($curl);
}


function getData($usr) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPGET, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $GLOBALS["url"]."/game/".$usr);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        $response = json_decode(curl_exec($curl), true);
	$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	if ($code == 403)
		logout();
        curl_close($curl);
	return $response;
}

function guess($usr, $letter) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, $GLOBALS["url"]."/game");
        curl_setopt($curl, CURLOPT_POSTFIELDS, "name=".$usr."&guess=".$letter);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
	$response = curl_exec($curl);
	$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	if ($code == 403)
		logout();
        curl_close($curl);
	//return $response;
}

if (!isset($_SESSION["usr"])) {
	logout();
}
if (!checkLogin($_SESSION["usr"])) {
	logout();
}

if (isset($_POST["gameChoise"]))
	if ($_POST["gameChoise"]=="Nyt spil")
		resetGame($_SESSION["usr"]);

if (isset($_POST["guess"]))
	guess($_SESSION["usr"], $_POST["guess"]);
?>
<form method="post">
<input type="submit" name="logout" value="log ud">
<input type="submit" name="gameChoise" value="Nyt spil">
</form>
<?php
$json = getData($_SESSION["usr"]);
$visibleWord = $json["visibleWord"];
$lives = 7-$json["lives"];
$used = $json["usedLetters"];
$gameOver = ($json["isGameOver"]=="true"?true:false);
$realWord = $json["invisibleWord"];

if ($gameOver) {
?>
	<?php if ($lives==0) { ?>
	<h2>Du har desværre tabt</h2>
	<h4>Du nåede at finde frem til <?php echo $visibleWord ?></h4>
	<h4>Det rigtige ord var: <?php echo $realWord ?></h4>
	<p>Du gættede på bogstaverne:</p>
	<?php 
	foreach ($used as $key => $value) {
		echo $value." ";
	}
	echo "<br>";
	?>
	<?php } else { ?>

	<h2>Du Vandt!!!</h2>
	<h4>Ordet var: <?php echo $visibleWord ?></h4>
	<h4>Du havde <?php echo $lives ?> liv tilbage</h4>
	<p>og brugte bogstaverne:<br>
	<?php 
	foreach ($used as $key => $value) {
		echo $value." ";
	}
	echo "<br>";
	?>
	</p>


	<?php } ?>
<?php
} else {
?>
	<h2>Det synlige ord er: <?php echo $visibleWord ?></h2>
	<p>Du har brugt følgende bogstaver:<br>
	<?php 
	foreach ($used as $key => $value) {
		echo $value." ";
	}
	echo "<br>";
	?>
	Og du har <?php echo $lives ?> liv tilbage</p>

	<form method="post">
	<input type="text" id="guess" name="guess" maxlength="1" pattern="[a-z]">
	<input type="submit" value="Gæt">
	</form>
<?php
}
?>
