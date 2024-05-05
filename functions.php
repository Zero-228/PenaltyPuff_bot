<?php 

use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Support\DeepLink;

function debug($things, $decode=false, $mysql=false, $clear=false) {

    $directory_path = $_SERVER['DOCUMENT_ROOT'] . '/temp';
    $file_path = $directory_path . '/debug.txt';
    if (!file_exists($directory_path)) {
        mkdir($directory_path, 0777, true);
    }
    $file = fopen($file_path, 'a+');

    if ($clear) {
        file_put_contents($file_path, '');
    }

    if ($decode) {
        $data = json_decode($things, true);
        $message = '[' . TIME_NOW . '] ' . print_r($data, true);
    } else {
        $message = '[' . TIME_NOW . '] ' . $things;
    }

    fwrite($file, $message . PHP_EOL);
    fclose($file);
}

function checkUser($userId){
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $row = mysqli_query($dbCon, "SELECT role FROM user WHERE userId='$userId'");
    $numRow = mysqli_num_rows($row);
    if ($numRow == 0) { return 'no_such_user'; } 
    elseif ($numRow == 1) { return 'one_user'; } 
    else { return false; error_log("ERROR! TWIN USER IN DB!");}
    mysqli_close($dbCon);
}

function createUser($user){
    $checkUser = checkUser($user['id']);
    if ($checkUser=="no_such_user") {
        $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $username = "";
        $timeNow = TIME_NOW;
        while (true) {
            $referral = uniqid();
            $query = mysqli_query($dbCon, "SELECT * FROM user WHERE referral='$referral'");
            if (mysqli_num_rows($query) == 0) {
                break;
            }
        }
        if ($user['username']!='') { $username = $user['username']; } 
        else { $username = $user['first_name']." ".$user['last_name']; }
        mysqli_query($dbCon, "INSERT INTO user (userId, firstName, lastName, username, language, lastVisit, registeredAt, referral) VALUES ('" . $user['id'] . "', '" . $user['first_name'] . "', '" . $user['last_name'] . "', '" . $username . "', '" . $user['language_code'] . "', '" . $timeNow . "', '" . $timeNow . "', '" . $referral . "')");
        mysqli_close($dbCon);
        return true;
    } else {
        return false;
    }
}

function createLog($timestamp, $entity, $entityId, $context, $message) {
    $timeNow = TIME_NOW;
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if($entity=='user'){
        try {
            $query = mysqli_query($dbCon, "UPDATE user SET lastVisit='$timeNow' WHERE userId='$entityId'");
        } catch (Exception $e) {
            error_log("Error updating user's last visit time. User: ".$entityId." | Error: ".mysqli_error($dbCon));
        }
    }
    $createLog = mysqli_query($dbCon, "INSERT INTO log (createdAt, entity, entityId, context, message) VALUES ('$timestamp', '$entity','$entityId','$context','$message')");
    if (!$createLog) {
        error_log("error with create log in DB");
    }
    mysqli_close($dbCon);
}

function lang($userId) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $languageResult = mysqli_query($dbCon, "SELECT language FROM user WHERE userId='$userId'");
    $row = mysqli_fetch_assoc($languageResult);
    $language = isset($row['language']) ? $row['language'] : "Unknown";
    mysqli_free_result($languageResult);
    mysqli_close($dbCon);
    
    return $language;
}

function changeLanguage($userId, $newLang) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = mysqli_query($dbCon, "UPDATE user SET language='$newLang' WHERE userId='$userId'");
    mysqli_close($dbCon);
}

function constructMenuButtons($lang) {
    $keyboard = ReplyKeyboardMarkup::make(resize_keyboard: true,)
    ->addRow(KeyboardButton::make(msg('approve', $lang)), KeyboardButton::make(msg('prescribe', $lang)),)
    ->addRow(KeyboardButton::make(msg('frends', $lang)))
    ->addRow(KeyboardButton::make(msg('status', $lang)), KeyboardButton::make(msg('info', $lang)),);

    return $keyboard;
}

function constructStatus($userId, $language = null) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $user = mysqli_fetch_assoc(mysqli_query($dbCon, "SELECT * FROM user WHERE userId='$userId'"));
    if ($user['username']=='') {
        $username = $user['first_name'].' '.$user['last_name'];
    } else { $username = $user['username']; }
    $lang = $user['language'];
    if ($language) {
        $lang = $language;
    }
    $registered = substr($user['registeredAt'], 0, 10);
    $friends = findFriends($userId);
    $checkAllPuffs = mysqli_query($dbCon, "SELECT puffId FROM puff WHERE userTo='$userId'");
    $num_rows_all_puff = mysqli_num_rows($checkAllPuffs);
    $checkAcceptedPuffs = mysqli_query($dbCon, "SELECT puffId FROM puff WHERE userTo='$userId' AND status='approve'");
    $num_rows_accepted_puff = mysqli_num_rows($checkAcceptedPuffs);
    $puffs = $num_rows_accepted_puff."/".$num_rows_all_puff;
    $checkPresctibedPuffs = mysqli_query($dbCon, "SELECT puffId FROM puff WHERE userFrom='$userId'");
    $num_rows_prescribed_puff = mysqli_num_rows($checkPresctibedPuffs);

    $status = "============================\n   📜 User: ".$username."\n============================\n ".msg('status_frends', $lang).": ".$friends."\n\n ".msg('status_acceptedPuffs', $lang).": ".$puffs."\n\n ".msg('status_prescribedPuffs', $lang).": ".$num_rows_prescribed_puff."\n________________________________\n ".msg('status_registered', $lang).": ".$registered."\n============================";    

    mysqli_close($dbCon);

    return $status;
}

function findFriends($userId) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $friends = mysqli_query($dbCon, "SELECT * FROM friend_request WHERE (user_from='$userId' AND status='friends') OR (user_to='$userId' AND status='friends')");
    $num_rows = mysqli_num_rows($friends);
    if ($num_rows==0) {
        return 0;
    } else {
        return $num_rows;
    }
    mysqli_close($dbCon);
}

function getUserFromRef($referral) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $userId = mysqli_query($dbCon, "SELECT userId FROM user WHERE referral='$referral'");
    $userId = mysqli_fetch_assoc($userId);
    mysqli_close($dbCon);
    return $userId['userId'];
}

function createRefCode($userId) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    while (true) {
        $referral = uniqid();
        $query = mysqli_query($dbCon, "SELECT * FROM user WHERE referral='$referral'");
        if (mysqli_num_rows($query) == 0) {
            break;
        }
    }
    try {
        mysqli_query($dbCon, "UPDATE user SET referral='$referral' WHERE userId='$userId'");
        mysqli_close($dbCon);
        return $referral;
    } catch (Exception $e) {
        error_log("Error in generating ref code");
        return false;
    }
}

function getReferralCode($userId) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $result = mysqli_query($dbCon, "SELECT referral FROM user WHERE userId='$userId'");
    $ref = mysqli_fetch_assoc($result);
    mysqli_close($dbCon);
    $referral = $ref['referral'];
    if ($referral === NULL || $referral === '') {
        $referral = createRefCode($userId);
        if (!$referral) {
            error_log('Failed to create referral.');
            return false;
        }
    }
    return $referral;
}

function makeFriend($referral, $user_to, $timeNow) {
    $user_from = getUserFromRef($referral);
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $check_query = mysqli_query($dbCon, "SELECT * FROM friend_request WHERE (user_from='$user_to' AND user_to='$user_from' AND status='friends') OR (user_to='$user_to' AND user_from='$user_from' AND status='friends')");
    $existing_row = mysqli_fetch_assoc($check_query);

    if ($existing_row || $user_from==$user_to) {
        return "already friends";
    } else {
        $check_query = mysqli_query($dbCon, "SELECT * FROM friend_request WHERE (user_from='$user_from' AND user_to='$user_to') OR (user_to='$user_from' AND user_from='$user_to')");
        $existing_row = mysqli_fetch_assoc($check_query);

        if ($existing_row) {
            $update_query = mysqli_query($dbCon, "UPDATE friend_request SET status='friends', modified_at='$timeNow' WHERE (user_from='$user_from' AND user_to='$user_to') OR (user_to='$user_from' AND user_from='$user_to')");
            return "updated (".$user_from." and ".$user_to.")";
        } else {
            $newFriend = mysqli_query($dbCon, "INSERT INTO friend_request (user_from, user_to, status, modified_at, created_at) VALUES ('$user_from', '$user_to', 'friends', '$timeNow', '$timeNow')");
            return "new friends ".$user_from." and ".$user_to;
        }
    }
    mysqli_close($dbCon);
}

function showFriends($userId) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $friends_query = mysqli_query($dbCon, "SELECT * FROM friend_request WHERE (user_from='$userId' OR user_to='$userId') AND status='friends'");
    $friends_info = array();
    while ($friend = mysqli_fetch_assoc($friends_query)) {
        $friend_id = ($friend['user_from'] == $userId) ? $friend['user_to'] : $friend['user_from'];
        $user_query = mysqli_query($dbCon, "SELECT firstName, username FROM user WHERE userId='$friend_id'");
        $user_info = mysqli_fetch_assoc($user_query);
        $friends_info[] = array(
            'id' => $friend_id,
            'first_name' => $user_info['firstName'],
            'username' => $user_info['username']
        );
    }
    mysqli_close($dbCon);
    return $friends_info;
}

function getFriendKeyboard(array $friends, int $page, $userId): InlineKeyboardMarkup{
    $referral = getReferralCode($userId);
    if (!$referral) {
        $referral = createRefCode($userId);
    }
    $lang = lang($userId);
    $deeplink = new DeepLink();
    $deep_link = $deeplink->start(BOT_USERNAME, $referral);
    $share_link = "https://t.me/share/url?url=".$deep_link;
    $keyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('invite_friend', $lang), $share_link));
    $start = $page * 5;
    for ($i = $start; $i < $start + 5 && $i < count($friends); $i++) {
        $msg = $friends[$i]['first_name']."  ( ".$friends[$i]['username']." )";
        $keyboard->addRow(InlineKeyboardButton::make($msg, null,null, 'callback_view_friend_info '.$friends[$i]['id']));
    }
    if ($page > 0 || count($friends) > ($start + 5)) {
        $buttons = [];
        if ($page > 0) {
            $buttons[] = InlineKeyboardButton::make(msg('btn_back', $lang), callback_data:"/friend_page_ " . ($page - 1) . " /");
        }
        if (count($friends) > ($start + 5)) {
            $buttons[] = InlineKeyboardButton::make(msg('btn_forward', $lang), callback_data:"/friend_page_ " . ($page + 1) . " /");
        }
        $keyboard->addRow(...$buttons);
    }

    return $keyboard;
}

function removeFriend($userId, $friendId) {
    $timeNow = TIME_NOW;
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = mysqli_query($dbCon, "UPDATE friend_request SET status='unfriend', modified_at='$timeNow' WHERE (user_from='$userId' AND user_to='$friendId') OR (user_to='$userId' AND user_from='$friendId')");
    mysqli_close($dbCon);
    return true;
}

function warnFriend($userId, $friendId, $reason) {
    $timeNow = TIME_NOW;
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    try {
        $query = mysqli_query($dbCon, "INSERT INTO warn (userFrom, userTo, reason, warndAt) VALUES ('$userId', '$friendId', '$reason', '$timeNow')");
        return true;
    } catch (Exception $e) {
        return false;
    }
    mysqli_close($dbCon);
}

function prescribePuff($userId, $friendId) {
    $now = new DateTime('now', new DateTimeZone('Europe/Madrid'));
    $timeNow = $now->format('Y-m-d H:i:s');
    if ($userId!=$friendId) {
        $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
        $checkPuffs = mysqli_query($dbCon, "SELECT MAX(prescribed_at) AS latest_prescribed_at FROM puff WHERE userFrom = '$userId' AND userTo = '$friendId'");
        
        if ($checkPuffs && mysqli_num_rows($checkPuffs) > 0) {
            $row = mysqli_fetch_assoc($checkPuffs); 
            $latestPrescribedAt = strtotime($row['latest_prescribed_at'])+180;
            
            if ($latestPrescribedAt > strtotime($timeNow)) {
                return "delay";
            } else {
                $query = mysqli_query($dbCon, "INSERT INTO puff (userFrom, userTo, status, modified_at, prescribed_at) VALUES ('$userId', '$friendId', 'pending', '$timeNow', '$timeNow')");
                $checkPuffs2 = mysqli_query($dbCon, "SELECT puffId FROM puff WHERE userFrom = '$userId' AND userTo = '$friendId' AND prescribed_at='$timeNow'");
                $row = mysqli_fetch_assoc($checkPuffs2); 
                return "success ".$row['puffId'];
            }
        } else {
            $query = mysqli_query($dbCon, "INSERT INTO puff (userFrom, userTo, status, modified_at, prescribed_at) VALUES ('$userId', '$friendId', 'pending', '$timeNow', '$timeNow')");
            $checkPuffs2 = mysqli_query($dbCon, "SELECT puffId FROM puff WHERE userFrom = '$userId' AND userTo = '$friendId' AND prescribed_at='$timeNow'");
            $row = mysqli_fetch_assoc($checkPuffs2); 
            return "success ".$row['puffId'];
        }
        mysqli_close($dbCon);
    } else {
        return "self";
    }
}

function prescribePuffFriend2($userId, $page = 0) {
    $friends = showFriends($userId);
    $lang = lang($userId);
    $keyboard = InlineKeyboardMarkup::make();
    $start = $page * 5;
    for ($i = $start; $i < $start + 5 && $i < count($friends); $i++) {
        $msg = $friends[$i]['first_name']."  ( ".$friends[$i]['username']." )";
        $keyboard->addRow(InlineKeyboardButton::make($msg, null,null, 'callback_prescribe '.$friends[$i]['id']));
    }
    if ($page > 0 || count($friends) > ($start + 5)) {
        $buttons = [];
        if ($page > 0) {
            $buttons[] = InlineKeyboardButton::make(msg('btn_back', $lang), callback_data:"/prescribe_page_ " . ($page - 1) . " /");
        }
        if (count($friends) > ($start + 5)) {
            $buttons[] = InlineKeyboardButton::make(msg('btn_forward', $lang), callback_data:"/prescribe_page_ " . ($page + 1) . " /");
        }
        $keyboard->addRow(...$buttons);
    }

    return $keyboard;
}

function updatePuff($puffId, $status) {
    $now = new DateTime('now', new DateTimeZone('Europe/Madrid'));
    $timeNow = $now->format('Y-m-d H:i:s');
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($status=='decline') {
        $query = mysqli_query($dbCon, "UPDATE puff SET status='$status', modified_at='$timeNow' WHERE puffId='$puffId'");
    } elseif ($status=='approve') {
        $checkPuffs = mysqli_query($dbCon, "SELECT prescribed_at FROM puff WHERE puffId='$puffId'");
        if ($checkPuffs && mysqli_num_rows($checkPuffs) > 0) {
            $row = mysqli_fetch_assoc($checkPuffs); 
            $latestPrescribedAt = strtotime($row['prescribed_at'])+90;
            if ($latestPrescribedAt > strtotime($timeNow)) {
                return "delay";
            } else {
                $query = mysqli_query($dbCon, "UPDATE puff SET status='$status', modified_at='$timeNow' WHERE puffId='$puffId'");
            }
        } else {
            error_log("Error in function updatePuff()");
        }
    }
    mysqli_close($dbCon);
}

function checkPuff($userId) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = "SELECT * FROM puff WHERE userTo='$userId' AND status='pending' ORDER BY prescribed_at DESC LIMIT 5";
    $result = mysqli_query($dbCon, $query);
    if ($result) {
        $puffs = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $puffs;
    } else {
        error_log("Error in checkPuff: " . mysqli_error($dbCon));
        return false;
    }
    mysqli_close($dbCon);
}


function getUsername($userId){
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $result = mysqli_query($dbCon, "SELECT username FROM user WHERE userId='$userId'");
    if ($result && mysqli_num_rows($result) > 0) {
        $username = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        mysqli_close($dbCon);
        return $username['username'];
    } else {
        mysqli_close($dbCon);
        return msg("friend", $bot->userId());
    }
}

function checkIfSupport($userId, $message_id){
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $result = mysqli_query($dbCon, "SELECT * FROM log WHERE entityId = '$userId' AND context = 'callback' AND message LIKE '%support%' AND createdAt >= NOW() - INTERVAL 1 HOUR");
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $message_parts = explode(' ', $row['message']);
            foreach ($message_parts as $part) {
                if (is_numeric($part) && $part == ($message_id - 2)) {
                    return true;
                }
            }
        }
    }
    mysqli_close($dbCon);
    return false;
}

function createSupport($userId, $message){
    $timeNow = TIME_NOW;
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = mysqli_query($dbCon, "INSERT INTO support (userId, message, created_at) VALUES ('$userId', '$message', '$timeNow')");
    mysqli_close($dbCon);


}

function selectUsersWithLang($userId, $lang){
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $query = mysqli_query($dbCon, "SELECT userId FROM user WHERE language='$lang' AND userId != '$userId'");
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
    mysqli_close($dbCon);
    return $result;
}

function checkRole($userId) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $roleQuery = mysqli_query($dbCon, "SELECT role FROM user WHERE userId='$userId'");
    $roleNumRow = mysqli_num_rows($roleQuery);
    if ($roleNumRow == 1) {
        $role = mysqli_fetch_assoc($roleQuery);
        $role = $role['role'];
        return $role;
    } else {
        return "no user";
    }
    mysqli_close($dbCon);
}function showBotStat() {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    $query = "
        SELECT 
            (SELECT COUNT(userId) FROM user) as num_users,
            (SELECT COUNT(userId) FROM user WHERE registeredAt >= NOW() - INTERVAL 12 HOUR) as num_new_users,
            (SELECT COUNT(logId) FROM log WHERE createdAt >= NOW() - INTERVAL 1 HOUR) as num_msgs,
            (SELECT COUNT(puffId) FROM puff WHERE prescribed_at >= NOW() - INTERVAL 1 HOUR) as num_puffs,
            (SELECT COUNT(puffId) FROM puff WHERE status='approve' AND prescribed_at >= NOW() - INTERVAL 1 HOUR) as num_new_appr_puffs,
            (SELECT COUNT(puffId) FROM puff WHERE status='denied' AND prescribed_at >= NOW() - INTERVAL 1 HOUR) as num_new_den_puffs,
            (SELECT COUNT(puffId) FROM puff WHERE status='approve' AND modified_at >= NOW() - INTERVAL 1 HOUR) as num_appr_puffs,
            (SELECT COUNT(puffId) FROM puff WHERE status='denied' AND modified_at >= NOW() - INTERVAL 1 HOUR) as num_den_puffs,
            (SELECT puffId FROM puff ORDER BY puffId DESC LIMIT 1) as last_puffId
    ";

    $result = mysqli_query($dbCon, $query);
    $data = mysqli_fetch_assoc($result);

    mysqli_close($dbCon);

    $msg = "⠀             🤖  Bot statistisc:\n==============================\n\n  🧑‍💻 Users:   ".$data['num_users']."   (🆕 ".$data['num_new_users']." )\n\n  ✉️ Msgs last hour:   ".$data['num_msgs']."\n\n  🌳 Total puffs:   ".$data['last_puffId']."\n\n  💨 Puffs last hour:   ".$data['num_puffs']."   (👍 ".$data['num_new_appr_puffs']."/ ".$data['num_new_den_puffs']." 👎)\n\n  ✅Approved puffs last hour:   ".$data['num_appr_puffs']."\n\n  🚫Declided puffs last hour:   ".$data['num_den_puffs']." \n\n==============================";

    return $msg;
}


?>