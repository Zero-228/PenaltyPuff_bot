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
        if ($user['username']!='') { $username = $user['username']; } 
        else { $username = $user['first_name']." ".$user['last_name']; }
        mysqli_query($dbCon, "INSERT INTO user (userId, firstName, lastName, username, language, lastVisit, registeredAt) VALUES ('" . $user['id'] . "', '" . $user['first_name'] . "', '" . $user['last_name'] . "', '" . $username . "', '" . $user['language_code'] . "', '" . $timeNow . "', '" . $timeNow . "')");
        mysqli_close($dbCon);
        return true;
    } else {
        return false;
    }
    
}

function createLog($timestamp, $entity, $entityId, $context, $message) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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

    $status = "=========================\n   📜 User: ".$username."\n=========================\n ".msg('status_frends', $lang).": ".$friends."\n\n ".msg('status_acceptedPuffs', $lang).": ".$puffs."\n\n ".msg('status_prescribedPuffs', $lang).": ".$num_rows_prescribed_puff."\n_____________________________\n ".msg('status_registered', $lang).": ".$registered."\n=========================";    

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


function makeFriend($user_from, $user_to, $timeNow) {
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $check_query = mysqli_query($dbCon, "SELECT * FROM friend_request WHERE (user_from='$user_to' AND user_to='$user_from' AND status='friends') OR (user_to='$user_to' AND user_from='$user_from' AND status='friends')");
    $existing_row = mysqli_fetch_assoc($check_query);

    if ($existing_row || $user_from==$user_to) {
        return "already friends";
    } else {
        $check_query = mysqli_query($dbCon, "SELECT * FROM friend_request WHERE (user_from='$user_from' AND user_to='$user_to') OR (user_to='$user_from' AND user_from='$user_to')");
        $existing_row = mysqli_fetch_assoc($check_query);

        if ($existing_row) {
            $update_query = mysqli_query($dbCon, "UPDATE friend_request SET status='friends' AND modified_at='$timeNow' WHERE (user_from='$user_from' AND user_to='$user_to') OR (user_to='$user_from' AND user_from='$user_to')");
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
    $deeplink = new DeepLink();
    $deep_link = $deeplink->start(BOT_USERNAME, $userId);
    $share_link = "https://t.me/share/url?url=".$deep_link;
    $keyboard = InlineKeyboardMarkup::make()
    ->addRow(InlineKeyboardButton::make(msg('invite_friend', lang($userId)), $share_link));

    foreach ($friends_info as $row) {
        $msg = $row['first_name']."  ( ".$row['username']." )";
        $keyboard->addRow(InlineKeyboardButton::make($msg, null,null, 'callback_view_friend_info '.$row['id']));
    }

    $keyboard->addRow(InlineKeyboardButton::make(msg('cancel', lang($userId)), null,null, 'callback_cancel'));

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
    $timeNow = TIME_NOW;
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

function prescribePuffFriend($userId) {
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

    $keyboard = InlineKeyboardMarkup::make();
    foreach ($friends_info as $row) {
        $msg = $row['first_name']."  ( ".$row['username']." )";
        $keyboard->addRow(InlineKeyboardButton::make($msg, null,null, 'callback_prescribe '.$row['id']));
    }
    $keyboard->addRow(InlineKeyboardButton::make(msg('cancel', lang($userId)), null,null, 'callback_cancel'));

    return $keyboard;
}

function updatePuff($puffId, $status) {
    $timeNow = TIME_NOW;
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
    $username = mysqli_query($dbCon, "SELECT username FROM user WHERE userId='$userId'");
    if ($username) {
        $username = mysqli_fetch_assoc($username);
        return $username['username'];
    } else {
        return msg("friend", $bot->userId());
    }
    mysqli_close($dbCon);
}


?>