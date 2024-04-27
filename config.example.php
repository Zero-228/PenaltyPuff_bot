<?php 

$now = new DateTime('now', new DateTimeZone('Europe/Madrid'));
define("TIME_NOW", $now->format('Y-m-d H:i:s'));
define("_IMG", $_SERVER['DOCUMENT_ROOT']."/img/");

define("BOT_TOKEN", "");
define("BOT_USERNAME", "");
define("WEBHOOK_URL", "");
define("WEB_APP", "");

define("DB_HOST", "");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_NAME", "");
$dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$dbCon) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "DB connected successfully!";
?>