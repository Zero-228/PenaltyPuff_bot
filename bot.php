<?php 

require_once __DIR__ . '/vendor/autoload.php';
require_once 'config.php';
require_once 'functions.php';
require 'localization.php';

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

$bot = new Nutgram(BOT_TOKEN);
$bot->setRunningMode(Webhook::class);
$bot->setWebhook(WEBHOOK_URL);

$bot->onCommand('start {referral}', function(Nutgram $bot, $referral = null) {
    if (checkUser($bot->userId()) == 'no_such_user') {
        $user_info = get_object_vars($bot->user());
        createUser($user_info);
        createLog(TIME_NOW, 'user', $bot->userId(), 'registering', '/start');
        $bot->sendMessage(msg('welcome', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
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
        }
    } elseif (checkUser($bot->userId()) == 'one_user') {
        createLog(TIME_NOW, 'user', $bot->userId(), 'command', '/start');
        $bot->sendMessage(msg('welcome_back', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
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
        }
    } else {
        $bot->sendMessage('WTF are you?');
    }
});

$bot->onCallbackQueryData('callback_change_lang', function (Nutgram $bot) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'change language');
    $changeLangInlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('language', 'en'), null, null, 'callback_change_lang_to en'))->addRow(InlineKeyboardButton::make(msg('language', 'uk'), null, null, 'callback_change_lang_to uk'))->addRow(InlineKeyboardButton::make(msg('language', 'ru'), null, null, 'callback_change_lang_to ru'))->addRow(InlineKeyboardButton::make(msg('cancel', lang($bot->userId())), null,null, 'callback_cancel'));
    $bot->sendMessage(msg('choose_language', lang($bot->userId())), reply_markup: $changeLangInlineKeyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_change_lang_to {param}', function (Nutgram $bot, $param) {
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    changeLanguage($bot->userId(), $param);
    $bot->sendMessage(msg('language_changed', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_invite_friend', function (Nutgram $bot) {
    $bot->sendMessage(msg('WIP', lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_view_friend_info {friendId}', function (Nutgram $bot, $friendId) {
    $lang = lang($bot->userId());
    $inlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('prescribe_puff', $lang), null,null, 'callback_prescribe '.$friendId))->addRow(InlineKeyboardButton::make(msg('warn', $lang), null,null, 'callback_warn '.$friendId),InlineKeyboardButton::make(msg('remove_friend', $lang), null,null, 'callback_remove_friend '.$friendId))->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage(constructStatus($friendId, $lang), reply_markup:$inlineKeyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_prescribe {friendId}', function (Nutgram $bot, $friendId) {
    $bot->sendMessage(msg('WIP', lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_warn {friendId}', function (Nutgram $bot, $friendId) {
    $bot->sendMessage(msg('WIP', lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_remove_friend {friendId}', function (Nutgram $bot, $friendId) {
    $bot->sendMessage(msg('WIP', lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_cancel', function (Nutgram $bot) {
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
        $bot->sendMessage(msg('WIP', $lang));
    }
    elseif (str_contains($text, msg('frends', $lang))) {
        $friends = findFriends($bot->userId());
        $deep_link = "https://t.me/".BOT_USERNAME."?start=".$bot->userId();
        $inlineKeyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make(msg('invite_friend', lang($bot->userId())), null, null, null, $deep_link))->addRow(InlineKeyboardButton::make(msg('cancel', lang($bot->userId())), null,null, 'callback_cancel'));
        if ($friends==0) {
            $bot->sendMessage(msg('no_friends', $lang), reply_markup: $inlineKeyboard);
        } else {
            $friend_keyboard = showFriends($bot->userId());
            $bot->sendMessage("You have ".$friends." friends", reply_markup: $friend_keyboard);
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

$bot->run();

?>
