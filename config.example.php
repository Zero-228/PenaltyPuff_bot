<?php 
/**
 * PenaltyPuff Chatbot
 * 
 * Licensed under the Simple Commercial License.
 * 
 * Copyright (c) 2024 Nikita Shkilov nikshkilov@yahoo.com
 * 
 * All rights reserved.
 * 
 * This file is part of PenaltyPuff bot. The use of this file is governed by the
 * terms of the Simple Commercial License, which can be found in the LICENSE file
 * in the root directory of this project.
 */

$now = new DateTime('now', new DateTimeZone('Europe/Madrid'));
define("TIME_NOW", $now->format('Y-m-d H:i:s'));
define("_IMG", $_SERVER['DOCUMENT_ROOT']."/img/");

define("BOT_TOKEN", "");
define("BOT_USERNAME", "");
define("WEBHOOK_URL", "");
define("WEB_APP", "");
define("ADMIN_ID", "");
define("BOT_ID", "");

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