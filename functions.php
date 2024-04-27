<?php 

use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;

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
    $row = mysqli_query($dbCon, "SELECT userId FROM user WHERE userId='$userId'");
    $numRow = mysqli_num_rows($row);
    if ($numRow == 0) { return 'no_such_user'; } 
    elseif ($numRow == 1) { return 'one_user'; } 
    else { return false; error_log("ERROR! TWIN USER IN DB!");}
    mysqli_close($dbCon);
}

function createUser($user){
    $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $username = "";
    $timeNow = TIME_NOW;
    if ($user['username']!='') { $username = $user['username']; } 
    else { $username = $user['first_name']." ".$user['last_name']; }
    mysqli_query($dbCon, "INSERT INTO user (userId, firstName, lastName, username, language, lastVisit, registeredAt) VALUES ('" . $user['id'] . "', '" . $user['first_name'] . "', '" . $user['last_name'] . "', '" . $username . "', '" . $user['language_code'] . "', '" . $timeNow . "', '" . $timeNow . "')");
    mysqli_close($dbCon);
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

    $status = "=========================\n   📜 User: ".$username."\n=========================\n ".msg('status_frends', $lang).": ".$friends."\n\n ".msg('status_acceptedPuffs', $lang).": 0/0\n\n ".msg('status_prescribedPuffs', $lang).": 0\n_____________________________\n ".msg('status_registered', $lang).": ".$registered."\n=========================";    

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

    if ($existing_row) {
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
}function showFriends($userId) {
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

    $keyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('invite_friend', lang($userId)), null, null, null, 'friend'));

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
    $query = mysqli_query($dbCon, "UPDATE friend_request SET status='unfriend' AND modified_at='$timeNow' WHERE (user_from='$userId' AND user_to='$friendId') OR (user_to='$userId' AND user_from='$friendId')");
    mysqli_close($dbCon);
    return true;
}

function prescribePuff($userId, $friendId) {
    $timeNow = TIME_NOW;
    if ($userId!=$friendId) {
        $dbCon = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
        $checkPuffs = mysqli_query($dbCon, "SELECT MAX(prescribed_at) AS latest_prescribed_at FROM puff WHERE userFrom = '$userId' AND userTo = '$friendId'");
        
        if ($checkPuffs && mysqli_num_rows($checkPuffs) > 0) {
            $row = mysqli_fetch_assoc($checkPuffs); 
            $latestPrescribedAt = strtotime($row['latest_prescribed_at']);
            
            if ($timeNow - $latestPrescribedAt < 180) {
                return "delay";
            } else {
                $query = mysqli_query($dbCon, "INSERT INTO puff (userFrom, userTo, status, modified_at, prescribed_at) VALUES ('$userId', '$friendId', 'pending', '$timeNow', '$timeNow')");
                return "success";
            }
        } else {
            $query = mysqli_query($dbCon, "INSERT INTO puff (userFrom, userTo, status, modified_at, prescribed_at) VALUES ('$userId', '$friendId', 'pending', '$timeNow', '$timeNow')");
            return "success";
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