<?php 

require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';
require_once 'functions.php';
require 'localization.php';

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\Support\DeepLink;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

$bot = new Nutgram(BOT_TOKEN);
$bot->setRunningMode(Webhook::class);
$bot->setWebhook(WEBHOOK_URL);

$bot->onCommand('start {referral}', function(Nutgram $bot, $referral = null) {
    if ($referral) {
        $checkUser = checkUser($bot->userId());
        if ($checkUser == 'no_such_user') {
            $user_info = get_object_vars($bot->user());
            createUser($user_info);
            createLog(TIME_NOW, 'user', $bot->userId(), 'registering', '/start');
            $lang = lang($bot->userId());
            $keyboard = constructMenuButtons($lang);
            if ($referral
            ) {
                $newFriend = makeFriend($referral, $bot->userId(), TIME_NOW);
                if (str_contains($newFriend, "new friends")) {
                    $bot->sendMessage(msg('welcome', $lang)."\n\n".msg('new_friends', $lang), reply_markup: $keyboard);
                    createLog(TIME_NOW, 'user', $bot->userId(), 'friendship', $newFriend);
                } elseif ($newFriend=="already friends") {
                    $bot->sendMessage(msg('welcome', $lang)."\n\n".msg('already_friends', $lang), reply_markup: $keyboard);
                } elseif (str_contains($newFriend, "updated")) {
                    createLog(TIME_NOW, 'user', $bot->userId(), 'friendship', $newFriend);
                    $bot->sendMessage(msg('welcome', $lang)."\n\n".msg('updated_friends', $lang), reply_markup: $keyboard);
                } else {
                    $bot->sendMessage('Some strange shit');
                }
            } else {
                $bot->sendMessage(msg('welcome', $lang), reply_markup: $keyboard);
            }
        } elseif ($checkUser == 'one_user') {
            createLog(TIME_NOW, 'user', $bot->userId(), 'command', '/start');
            if ($referral) {
                $newFriend = makeFriend($referral, $bot->userId(), TIME_NOW);
                if (str_contains($newFriend, "new friends")) {
                    $bot->sendMessage(msg('new_friends', lang($bot->userId())));
                    createLog(TIME_NOW, 'user', $bot->userId(), 'friendship', $newFriend);
                } elseif ($newFriend=="already friends") {
                    $bot->sendMessage(msg('already_friends', lang($bot->userId())));
                } elseif (str_contains($newFriend, "updated")) {
                    createLog(TIME_NOW, 'user', $bot->userId(), 'friendship', $newFriend);
                    $bot->sendMessage(msg('updated_friends', lang($bot->userId())));
                } else {
                    $bot->sendMessage('Some strange shit');
                }
            } else {
                $bot->sendMessage(msg('welcome_back', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
            }
        } else {
            $bot->sendMessage('WTF are you?');
        }        
    }
});

$bot->onCommand('start', function(Nutgram $bot) {
    $checkUser = checkUser($bot->userId());
    if ($checkUser == 'no_such_user') {
        $user_info = get_object_vars($bot->user());
        $creating = createUser($user_info);
        if ($creating) {
            $bot->sendMessage(msg('welcome', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
            createLog(TIME_NOW, 'user', $bot->userId(), 'registering', '/start');
        }
    } elseif ($checkUser == 'one_user') {
        createLog(TIME_NOW, 'user', $bot->userId(), 'command', '/start');
        $bot->sendMessage(msg('welcome_back', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
    } else {
        $bot->sendMessage('WTF are you?');
    }
});

$bot->onCallbackQueryData('callback_change_lang', function (Nutgram $bot) {
    $changeLangInlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('language', 'en'), null, null, 'callback_change_lang_to en'))->addRow(InlineKeyboardButton::make(msg('language', 'uk'), null, null, 'callback_change_lang_to uk'))->addRow(InlineKeyboardButton::make(msg('language', 'ru'), null, null, 'callback_change_lang_to ru'))->addRow(InlineKeyboardButton::make(msg('cancel', lang($bot->userId())), null,null, 'callback_cancel'));
    $bot->sendMessage(msg('choose_language', lang($bot->userId())), reply_markup: $changeLangInlineKeyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_change_lang_to {param}', function (Nutgram $bot, $param) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'change_lang_to '.$param);
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    changeLanguage($bot->userId(), $param);
    $bot->sendMessage(msg('language_changed', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_view_friend_info {friendId}', function (Nutgram $bot, $friendId) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'view_friend_info '.$friendId);
    $lang = lang($bot->userId());
    $inlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('prescribe_puff', $lang), null,null, 'callback_prescribe '.$friendId))->addRow(InlineKeyboardButton::make(msg('warn', $lang), null,null, 'callback_warn '.$friendId),InlineKeyboardButton::make(msg('remove_friend', $lang), null,null, 'callback_remove_friend '.$friendId))->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage(constructStatus($friendId, $lang), reply_markup:$inlineKeyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_prescribe {friendId}', function (Nutgram $bot, $friendId) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'prescribe '.$friendId);
    $username = getUsername($bot->userId());
    $prescribe = prescribePuff($bot->userId(), $friendId);
    if (str_contains($prescribe, "success")) {
        $trimmedPrescribe = substr($prescribe, strpos($prescribe, "success") + strlen("success"));
        $puffId = (int)$trimmedPrescribe;
        $friendLang = lang($friendId);
        $inlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('puff_decline', $friendLang), null,null, 'callback_puff_decline '.$puffId.' '.$bot->userId()),InlineKeyboardButton::make(msg('puff_approve', $friendLang), null,null, 'callback_puff_approve '.$puffId.' '.$bot->userId()))->addRow(InlineKeyboardButton::make(msg('cancel', $friendLang), null,null, 'callback_cancel'));
        $bot->sendMessage($username.msg('prescribed_puff', $friendLang), chat_id: $friendId, reply_markup: $inlineKeyboard);
        $bot->deleteMessage($bot->userId(),$bot->messageId());
        $bot->sendMessage(msg('prescribe_success', lang($bot->userId())));
    } elseif ($prescribe == "self") {
        $bot->deleteMessage($bot->userId(),$bot->messageId());
        $bot->sendMessage(msg('prescribe_self', lang($bot->userId())));
    } elseif ($prescribe == "delay") {
        $bot->deleteMessage($bot->userId(),$bot->messageId());
        $bot->sendMessage(msg('prescribe_delay', lang($bot->userId())));
    }
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_warn {friendId}', function (Nutgram $bot, $friendId) {
    $lang = lang($bot->userId());
    $warnKeyboard = InlineKeyboardMarkup::make()
    ->addRow(InlineKeyboardButton::make(msg('warn_btn_falseApr', $lang), null,null, 'callback_warn_user '.$friendId.' falseApr'))
    ->addRow(InlineKeyboardButton::make(msg('warn_btn_spam', $lang), null,null, 'callback_warn_user '.$friendId.' spam'))
    ->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage(msg('warn_user', $lang), reply_markup: $warnKeyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_warn_user {friendId} {reason}', function (Nutgram $bot, $friendId, $reason) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'warn '.$friendId);
    $check = warnFriend($bot->userId(), $friendId, $reason);
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    if ($check) {
        $bot->sendMessage(msg('warn_sent', lang($bot->userId())));
    } else {
        error_log("Error in warning user ".$friendId);
    }
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_remove_friend {friendId}', function (Nutgram $bot, $friendId) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'remove_friend '.$friendId);
    removeFriend($bot->userId(), $friendId);
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage(msg('unfriend', lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_puff_decline {puffId} {friendId}', function (Nutgram $bot, $puffId, $friendId) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'puff_decline '.$puffId);
    $username = getUsername($bot->userId());
    updatePuff($puffId, 'decline');
    $bot->sendMessage($username.msg('declined_puff', lang($friendId)), chat_id: $friendId);
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage(msg('puff_declined', lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_puff_approve {puffId} {friendId}', function (Nutgram $bot, $puffId, $friendId) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'puff_approve '.$puffId);
    $status = updatePuff($puffId, 'approve');
    $username = getUsername($bot->userId());
    if ($status == "delay") {
        $bot->sendMessage(msg('puff_approve_delay', lang($bot->userId())));
    } else {
        $bot->sendMessage($username.msg('approved_puff', lang($friendId)), chat_id: $friendId);
        $bot->deleteMessage($bot->userId(),$bot->messageId());
        $bot->sendMessage(msg('puff_approved', lang($bot->userId())));
    }
        $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_cancel', function (Nutgram $bot) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'cancel');
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage(msg('canceled', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onMessage(function (Nutgram $bot) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'message', $bot->message()->text);
    $text = $bot->message()->text;
    $lang = lang($bot->userId());
    if (str_contains($text, msg('approve', $lang))) {
        $bot->sendMessage(msg('WIP', $lang));
    }
    elseif (str_contains($text, msg('prescribe', $lang))) {
        $prescribePuffKeyboard = prescribePuffFriend($bot->userId());
        $bot->sendMessage(msg('choose_friend', $lang), reply_markup: $prescribePuffKeyboard);
    }
    elseif (str_contains($text, msg('frends', $lang))) {
        $friends = findFriends($bot->userId());
        $inlineKeyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make(msg('invite_friend', lang($bot->userId())), null, null, null, 'friend'))->addRow(InlineKeyboardButton::make(msg('cancel', lang($bot->userId())), null,null, 'callback_cancel'));
        if ($friends==0) {
            $bot->sendMessage(msg('no_friends', $lang), reply_markup: $inlineKeyboard);
        } else {
            $friend_keyboard = showFriends($bot->userId());
            $msg = msg('friends_quant', lang($bot->userId())).$friends.msg('invite_using_btn_below', lang($bot->userId()));
            $bot->sendMessage($msg, reply_markup: $friend_keyboard);
        }
    }
    elseif (str_contains($text, msg('status', $lang))) {
        $inlineKeyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make(msg('change_language', lang($bot->userId())), null, null, 'callback_change_lang'));
        $bot->sendMessage(constructStatus($bot->userId()), reply_markup: $inlineKeyboard);
    }
    elseif (str_contains($text, msg('info', $lang))) {
        $bot->sendMessage(msg('WIP', $lang));
    } 
    else {
        $bot->sendMessage("You send: ".$text);
    }
});

$bot->onInlineQueryText("friend", function (Nutgram $bot){
    createLog(TIME_NOW, 'user', $bot->userId(), 'InlineQuery', 'friend');
    $deeplink = new DeepLink();
    $deep_link = $deeplink->start('@shtrafnaya_bot', $bot->userId());
    $uniqueId = "make_friend_".TIME_NOW;
    $response = [
        [
            'type'=>'article',
            'id'=>$uniqueId,
            'title'=>msg('inline_invite_friend', lang($bot->userId())),
            'input_message_content'=> [
                'message_text' => $deep_link,
            ],
        ],
    ];
    $bot->answerInlineQuery($response);
});

$bot->run();

?>
