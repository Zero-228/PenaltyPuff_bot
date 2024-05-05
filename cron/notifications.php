<?php 

include '/home4/zerosite/public_html/ShtrafnayaBot.v2/config.php';
include '/home4/zerosite/public_html/ShtrafnayaBot.v2/functions.php';
include '/home4/zerosite/public_html/ShtrafnayaBot.v2/bot.php';
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

createLog(TIME_NOW, 'bot', ADMIN_ID, 'notification', 'check');

function sendNotifications(Nutgram $bot) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $inactivity_result = mysqli_query($dbCon, "SELECT * FROM user WHERE lastVisit < NOW() - INTERVAL 1.5 DAY AND deleted = 'no' AND banned = 'no'");
    $unapprooved_result = mysqli_query($dbCon, "SELECT puff.userTo, user.language FROM puff JOIN user ON puff.userTo = user.userId WHERE prescribed_at < NOW() - INTERVAL 6 HOUR AND status='pending'");
    processResults($dbCon, $bot, $inactivity_result, 'inactivity');
    processResults($dbCon, $bot, $unapprooved_result, 'unapprooved');
    mysqli_close($dbCon);
}

function processResults($dbCon, Nutgram $bot, $result, $messageType) {
    while ($user = mysqli_fetch_assoc($result)) {
        $userId = $messageType == 'unapprooved' ? $user['userTo'] : $user['userId'];
        $logQuery = "SELECT * FROM log WHERE entityId = {$userId} AND context = 'notification' AND message='{$messageType}'";
        $logResult = mysqli_query($dbCon, $logQuery);
        if (mysqli_num_rows($logResult) == 0) {
            $language = isset($user['language']) ? $user['language'] : 'en';
            $bot->sendMessage(msg($messageType, $language), chat_id: $userId);
            createLog(TIME_NOW, 'bot', $userId, 'notification', $messageType);
            sleep(1);
        }
    }
}
try {
    sendNotifications($bot);
} catch (Exception $e) {
    error_log("Error sending notifications: ".$e);
}


?>
