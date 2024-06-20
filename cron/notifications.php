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

include $_SERVER['DOCUMENT_ROOT'].'/ShtrafnayaBot.v2/config.php';
include $_SERVER['DOCUMENT_ROOT'].'/ShtrafnayaBot.v2/functions.php';
include $_SERVER['DOCUMENT_ROOT'].'/ShtrafnayaBot.v2/bot.php';
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

createLog(TIME_NOW, 'bot', ADMIN_ID, 'notification', 'check');

function sendNotifications(Nutgram $bot) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $inactivity_result = mysqli_query($dbCon, "SELECT * FROM user WHERE lastVisit < NOW() - INTERVAL 1.5 DAY AND deleted = 'no' AND banned = 'no'");
    $unapprooved_result = mysqli_query($dbCon, "SELECT puff.userTo, user.language FROM puff JOIN user ON puff.userTo = user.userId WHERE prescribed_at < NOW() - INTERVAL 6 HOUR AND status='pending'");
    $num_inac = processResults($dbCon, $bot, $inactivity_result, 'inactivity');
    $num_unap = processResults($dbCon, $bot, $unapprooved_result, 'unapprooved');
    mysqli_close($dbCon);

    $msg = "🤖 Bot sent notifications to:\n\n   ".$num_inac." inactive users \n   ".$num_unap." unapprooved";
    $bot->sendMessage($msg, chat_id: ADMIN_ID);
}

function processResults($dbCon, Nutgram $bot, $result, $messageType) {
    $counter = 0;
    while ($user = mysqli_fetch_assoc($result)) {
        $userId = $messageType == 'unapprooved' ? $user['userTo'] : $user['userId'];
        $logQuery = "SELECT * FROM log WHERE entityId = {$userId} AND context = 'notification' AND message='{$messageType}'";
        $logResult = mysqli_query($dbCon, $logQuery);
        if (mysqli_num_rows($logResult) == 0) {
            $language = isset($user['language']) ? $user['language'] : 'en';
            try {
                $bot->sendMessage(msg($messageType, $language), chat_id: $userId);
                createLog(TIME_NOW, 'bot', $userId, 'notification', $messageType);
                $counter++;
                sleep(1);
            } catch (\Exception $e) {
                if ($e->getCode() == '403') {
                    UserBlockedBot($userId);
                    sleep(1);
                }
            }
            
        }
    }
    return $counter;
}
try {
    sendNotifications($bot);
} catch (Exception $e) {
    error_log("Error sending notifications: ".$e);
}


?>
