<?php 

require_once '../bot.php';
require_once '../config.php';
require_once '../functions.php';

createLog(TIME_NOW, 'bot', ADMIN_ID, 'notification', 'check');

function sendNotifications(Nutgram $bot) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT * FROM user WHERE lastVisit < NOW() - INTERVAL 1.5 DAY AND deleted = 'no' AND banned = 'no'
              UNION
              SELECT puff.userTo, user.language FROM puff JOIN user ON puff.userTo = user.userId WHERE prescribed_at < NOW() - INTERVAL 6 HOUR AND status='pending'";
    $result = mysqli_query($dbCon, $query);
    processResults($dbCon, $bot, $result);
    mysqli_close($dbCon);
}

function processResults($dbCon, Nutgram $bot, $result) {
    while ($user = mysqli_fetch_assoc($result)) {
        $userId = $user['userId'];
        $logQuery = "SELECT * FROM log WHERE entityId = {$userId} AND context = 'notification'";
        $logResult = mysqli_query($dbCon, $logQuery);
        if (mysqli_num_rows($logResult) == 0) {
            $language = isset($user['language']) ? $user['language'] : 'en';
            $bot->sendMessage(msg('notification', $language), chat_id: $userId);
            createLog(TIME_NOW, 'bot', $userId, 'notification', 'notification');
            sleep(1);
        }
    }
}

sendNotifications($bot);

?>
