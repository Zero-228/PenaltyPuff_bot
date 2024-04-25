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

$bot->onCommand('start', function(Nutgram $bot) {
    if (checkUser($bot->userId()) == 'no_such_user') {
        $user_info = get_object_vars($bot->user());
        createUser($user_info);
        createLog(TIME_NOW, 'user', $bot->userId(), 'registering', '/start');
        // $inlineKeyboard = InlineKeyboardMarkup::make()
        // ->addRow(InlineKeyboardButton::make(msg('change_language', lang($bot->userId())), null, null, 'callback_change_lang'));
        $bot->sendMessage(msg('welcome', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
    } elseif (checkUser($bot->userId()) == 'one_user') {
        createLog(TIME_NOW, 'user', $bot->userId(), 'command', '/start');
        // $inlineKeyboard = InlineKeyboardMarkup::make()
        // ->addRow(InlineKeyboardButton::make(msg('change_language', lang($bot->userId())), null, null, 'callback_change_lang'));
        $bot->sendMessage(msg('welcome_back', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
    } else {
        $bot->sendMessage('WTF are you?');
    }
});

$bot->onCallbackQueryData('callback_change_lang', function (Nutgram $bot) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'change language');
    $changeLangInlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('language', 'en'), null, null, 'callback_change_lang_to en'))->addRow(InlineKeyboardButton::make(msg('language', 'uk'), null, null, 'callback_change_lang_to uk'))->addRow(InlineKeyboardButton::make(msg('language', 'ru'), null, null, 'callback_change_lang_to ru'));
    $bot->sendMessage(msg('choose_language', lang($bot->userId())), reply_markup: $changeLangInlineKeyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_change_lang_to {param}', function (Nutgram $bot, $param) {
    changeLanguage($bot->userId(), $param);
    $bot->sendMessage(msg('language_changed', lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onMessage(function (Nutgram $bot) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'message', $bot->message()->text);
    $msg = "You send: ".$bot->message()->text;
    $bot->sendMessage($msg);
});

$bot->run();

?>
