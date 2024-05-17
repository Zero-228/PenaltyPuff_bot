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
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

$bot = new Nutgram(BOT_TOKEN);
$bot->setRunningMode(Webhook::class);
$bot->setWebhook(WEBHOOK_URL);
$cache = new FilesystemAdapter();

$bot->onCommand('start {referral}', function(Nutgram $bot, $referral = null) {
    if ($referral) {
        $checkUser = checkUser($bot->userId());
        if ($checkUser == 'no_such_user') {
            $user_info = get_object_vars($bot->user());
            createUser($user_info);
            $lang = lang($bot->userId());
            $role = checkRole($bot->userId());
            createLog(TIME_NOW, $role, $bot->userId(), 'registering', '/start');
            $keyboard = constructMenuButtons($lang);
            if ($referral) {
                if (ctype_digit($referral)) {
                    die();
                } else {
                    $friendId = getUserFromRef($referral);
                    $newFriend = makeFriend($referral, $bot->userId(), TIME_NOW);
                    if (str_contains($newFriend, "new friends")) {
                        $msg = "🙋‍♂️ ".getUsername($bot->userId()).msg("accepted_friendship", lang($referral));
                        $bot->sendMessage(msg('welcome', $lang)."\n\n".msg('new_friends', $lang), reply_markup: $keyboard);
                        createLog(TIME_NOW, 'user', $bot->userId(), 'friendship', $newFriend);
                        sleep(2);
                        $bot->sendMessage($msg, chat_id: $friendId);
                    } elseif ($newFriend=="already friends") {
                        $bot->sendMessage(msg('welcome', $lang)."\n\n".msg('already_friends', $lang), reply_markup: $keyboard);
                    } elseif (str_contains($newFriend, "updated")) {
                        createLog(TIME_NOW, 'user', $bot->userId(), 'friendship', $newFriend);
                        $bot->sendMessage(msg('welcome', $lang)."\n\n".msg('updated_friends', $lang), reply_markup: $keyboard);
                        sleep(2);
                        $msg = "🙋‍♂️ ".getUsername($bot->userId()).msg("accepted_friendship", lang($referral));
                        $bot->sendMessage($msg, chat_id: $friendId);
                    } else {
                        $bot->sendMessage('Some strange shit');
                    }
                }
            } else {
                $bot->sendMessage(msg('welcome', $lang), reply_markup: $keyboard);
            }
        } elseif ($checkUser == 'one_user') {
            $lang = lang($bot->userId());
            $role = checkRole($bot->userId());
            createLog(TIME_NOW, $role, $bot->userId(), 'command', '/start');
            $keyboard = constructMenuButtons($lang);
            if (checkUserStatus($bot->userId() == 'deleted')) {
                userActivatedBot($bot->userId());
            }
            if ($referral) {
                if (ctype_digit($referral)) {
                    die();
                } else {
                    $newFriend = makeFriend($referral, $bot->userId(), TIME_NOW);
                    if (str_contains($newFriend, "new friends")) {
                        $msg = "🙋‍♂️ ".getUsername($bot->userId()).msg("accepted_friendship", lang($referral));
                        $bot->sendMessage(msg('new_friends', $lang), reply_markup: $keyboard);
                        createLog(TIME_NOW, 'user', $bot->userId(), 'friendship', $newFriend);
                        sleep(2);
                        $bot->sendMessage($msg, chat_id: $referral);
                    } elseif ($newFriend=="already friends") {
                        $bot->sendMessage(msg('already_friends', $lang), reply_markup: $keyboard);
                    } elseif (str_contains($newFriend, "updated")) {
                        createLog(TIME_NOW, 'user', $bot->userId(), 'friendship', $newFriend);
                        $bot->sendMessage(msg('updated_friends', $lang), reply_markup: $keyboard);
                        sleep(2);
                        $msg = "🙋‍♂️ ".getUsername($bot->userId()).msg("accepted_friendship", lang($referral));
                        $bot->sendMessage($msg, chat_id: $friendId);
                    } else {
                        $bot->sendMessage('Some strange shit');
                    }
                }
            } else {
                $bot->sendMessage(msg('welcome_back', $lang), reply_markup: constructMenuButtons($lang));
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
            $lang = lang($bot->userId());
            $role = checkRole($bot->userId());
            $bot->sendMessage(msg('welcome', $lang), reply_markup: constructMenuButtons($lang));
            createLog(TIME_NOW, $role, $bot->userId(), 'registering', '/start');
        }
    } elseif ($checkUser == 'one_user') {
        sleep(1);
        if (checkUserStatus($bot->userId() == 'deleted')) {
            userActivatedBot($bot->userId());
            $username = getUsername($bot->userId());
            $friends = showFriends($bot->userId());
            foreach ($friends as $friend) {
                $friendLang = lang($friend['id']);
                $inlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('prescribe_puff', $friendLang), null,null, 'callback_prescribe '.$bot->userId()))->addRow(InlineKeyboardButton::make(msg('cancel', $friendLang), null,null, 'callback_cancel'));
                $bot->sendMessage('🎉'.$username.msg('activated_bot', $friendLang), reply_markup: $inlineKeyboard, chat_id: $friend['id']);
                sleep(1);
            }
        }
        $lang = lang($bot->userId());
        $role = checkRole($bot->userId());
        createLog(TIME_NOW, $role, $bot->userId(), 'command', '/start');
        $bot->sendMessage(msg('welcome_back', $lang), reply_markup: constructMenuButtons($lang));
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
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'change_lang_to '.$param);
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    changeLanguage($bot->userId(), $param);
    $bot->sendMessage(msg('language_changed', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_view_friend_info {friendId}', function (Nutgram $bot, $friendId) {
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'view_friend_info '.$friendId);
    $lang = lang($bot->userId());
    $inlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('prescribe_puff', $lang), null,null, 'callback_prescribe '.$friendId))->addRow(InlineKeyboardButton::make(msg('warn', $lang), null,null, 'callback_warn '.$friendId),InlineKeyboardButton::make(msg('remove_friend', $lang), null,null, 'callback_remove_friend '.$friendId))->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage(constructStatus($friendId, $lang), reply_markup:$inlineKeyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_prescribe {friendId}', function (Nutgram $bot, $friendId) {
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'prescribe '.$friendId);
    $username = getUsername($bot->userId());
    $prescribe = prescribePuff($bot->userId(), $friendId);
    if (str_contains($prescribe, "success")) {
        $trimmedPrescribe = substr($prescribe, strpos($prescribe, "success") + strlen("success"));
        $puffId = (int)$trimmedPrescribe;
        $friendLang = lang($friendId);
        $inlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('puff_decline', $friendLang), null,null, 'callback_puff_decline '.$puffId.' '.$bot->userId()),InlineKeyboardButton::make(msg('puff_approve', $friendLang), null,null, 'callback_puff_approve '.$puffId.' '.$bot->userId()))->addRow(InlineKeyboardButton::make(msg('cancel', $friendLang), null,null, 'callback_cancel'));
        $checkStatus = checkUserStatus($friendId);
        if ($checkStatus == 'deleted') {
            $bot->deleteMessage($bot->userId(),$bot->messageId());
            $bot->sendMessage(msg('prescribe_deleted_user', lang($bot->userId())));
        } elseif ($checkStatus == 'active') {
            try {
                $bot->sendMessage($username.msg('prescribed_puff', $friendLang), chat_id: $friendId, reply_markup: $inlineKeyboard);
                $bot->deleteMessage($bot->userId(),$bot->messageId());
                $bot->sendMessage(msg('prescribe_success', lang($bot->userId())));
            } catch (\Exception $e) {
                if ($e->getCode() == '403') {
                    sleep(1);
                    UserBlockedBot($friendId);
                    $bot->deleteMessage($bot->userId(),$bot->messageId());
                    $bot->sendMessage(msg('prescribe_deleted_user', lang($bot->userId())));
                }
            }
        }
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
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'warn '.$friendId);
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
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'remove_friend '.$friendId);
    removeFriend($bot->userId(), $friendId);
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage(msg('unfriend', lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_puff_decline {puffId} {friendId}', function (Nutgram $bot, $puffId, $friendId) {
    $role = checkRole($bot->userId());
    if (checkUserStatus($bot->userId() == 'deleted')) {
        userActivatedBot($bot->userId());
    }
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'puff_decline '.$puffId);
    $username = getUsername($bot->userId());
    updatePuff($puffId, 'decline');
    $checkStatus = checkUserStatus($friendId);
    if ($checkStatus == 'deleted') {
        $bot->deleteMessage($bot->userId(),$bot->messageId());
        $bot->sendMessage(msg('puff_declined_deleted_user', lang($bot->userId())));
    } elseif ($checkStatus == 'active') {
        try {
            $bot->sendMessage($username.msg('declined_puff', lang($friendId)), chat_id: $friendId);
            $bot->deleteMessage($bot->userId(),$bot->messageId());
            $bot->sendMessage(msg('puff_declined', lang($bot->userId())));
        } catch (\Exception $e) {
            if ($e->getCode() == '403') {
                sleep(1);
                UserBlockedBot($friendId);
                $bot->deleteMessage($bot->userId(),$bot->messageId());
                $bot->sendMessage(msg('puff_declined_deleted_user', lang($bot->userId())));
            }
        }
    }
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_puff_approve {puffId} {friendId}', function (Nutgram $bot, $puffId, $friendId) {
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'puff_approve '.$puffId);
    if (checkUserStatus($bot->userId() == 'deleted')) {
        userActivatedBot($bot->userId());
    }
    $status = updatePuff($puffId, 'approve');
    $username = getUsername($bot->userId());
    if ($status == "delay") {
        $bot->sendMessage(msg('puff_approve_delay', lang($bot->userId())));
    } else {
        $checkStatus = checkUserStatus($friendId);
        if ($checkStatus == 'deleted') {
            $bot->deleteMessage($bot->userId(),$bot->messageId());
            $bot->sendMessage(msg('puff_approved_deleted_user', lang($bot->userId())));
        } elseif ($checkStatus == 'active') {
            try {
                $bot->sendMessage($username.msg('approved_puff', lang($friendId)), chat_id: $friendId);
                $bot->deleteMessage($bot->userId(),$bot->messageId());
                $bot->sendMessage(msg('puff_approved', lang($bot->userId())));
            } catch (\Exception $e) {
                if ($e->getCode() == '403') {
                    sleep(1);
                    UserBlockedBot($friendId);
                    $bot->deleteMessage($bot->userId(),$bot->messageId());
                    $bot->sendMessage(msg('puff_approved_deleted_user', lang($bot->userId())));
                }
            }
        }
    }
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_support', function (Nutgram $bot) {
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'support '.$bot->messageId());
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $keyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('cancel', lang($bot->userId())), null,null, 'callback_cancel'));
    $bot->sendMessage(msg('support_msg', lang($bot->userId())), reply_markup: $keyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_donate', function (Nutgram $bot) {
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'donate');
    $lang = lang($bot->userId());
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $selectDonationKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('show_wallet', $lang), null, null, 'callback_show_wallet'))->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
    $bot->sendMessage(msg('donation', $lang), reply_markup:$selectDonationKeyboard);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_show_wallet', function (Nutgram $bot) {
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'show_wallet');
    $bot->deleteMessage($bot->userId(),$bot->messageId());
    $bot->sendMessage("UQDtWWRLIE9a8gFZp7NnSlkNMYAIE1N7q7H8kcoS4kLGUiOP");
    sleep(1);
    $bot->sendMessage("User: ".$bot->userId()." saw your wallet. Check it up!", chat_id: ADMIN_ID);
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('callback_cancel', function (Nutgram $bot) {
    $role = checkRole($bot->userId());
    if (checkUserStatus($bot->userId() == 'deleted')) {
        userActivatedBot($bot->userId());
    }
    createLog(TIME_NOW, $role, $bot->userId(), 'callback', 'cancel');
    try {
        $bot->deleteMessage($bot->userId(),$bot->messageId());
    } catch (Exception $e) {
        error_log($e);
    }
    $bot->sendMessage(msg('canceled', lang($bot->userId())), reply_markup: constructMenuButtons(lang($bot->userId())));
    $bot->answerCallbackQuery();
});

$bot->onCallbackQueryData('/friend_page_{matches}/', function (Nutgram $bot, $matches) use ($cache) {
    $userId = $bot->userId();
    $page = isset($matches[1]) ? $matches[1] : 0;
    $lang = lang($bot->userId());

    // Сохраняем значение page в кэше
    $pageItem = $cache->getItem("page.$userId");
    $pageItem->set($page);
    $cache->save($pageItem);

    // Получаем список друзей прямо здесь, а не из кэша
    $friends = showFriends($userId);

    // Если друзей нет, то возвращаем пустую клавиатуру
    if (!$friends) {
        $referral = getReferralCode($bot->userId());
        if (!$referral) {
            $referral = $bot->userId();
        }
        $deeplink = new DeepLink();
        $deep_link = $deeplink->start(BOT_USERNAME, $referral);
        $share_link = "https://t.me/share/url?url=".$deep_link;
        $inlineKeyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make(msg('invite_friend', $lang), $share_link))->addRow(InlineKeyboardButton::make(msg('cancel', $lang)), null,null, 'callback_cancel');
        $bot->sendMessage(msg('no_friends', $lang), reply_markup: $inlineKeyboard);
        return;
    }

    $friendsQuant = findFriends($bot->userId());

    // Обновляем сообщение с новой страницей друзей
    $keyboard = getFriendKeyboard($friends, $page, $userId);
    $keyboard->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
    $msg = msg('friends_quant', $lang).$friendsQuant.msg('invite_using_btn_below', $lang);
    $bot->editMessageText(text: $msg,chat_id: $bot->chat()->id, message_id: $bot->message()->message_id, reply_markup: $keyboard);
});

$bot->onCallbackQueryData('/prescribe_page_{matches}/', function (Nutgram $bot, $matches) use ($cache) {
    $userId = $bot->userId();
    $page = isset($matches[1]) ? $matches[1] : 0;
    $lang = lang($bot->userId());
    $pageItem = $cache->getItem("p_page.$userId");
    $pageItem->set($page);
    $cache->save($pageItem);
    $friends = showFriends($userId);

    // Если друзей нет, то возвращаем пустую клавиатуру
    if (!$friends) {
        $inlineKeyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make(msg('cancel', $lang)), null,null, 'callback_cancel');
        $bot->sendMessage(msg('no_friends', $lang), reply_markup: $inlineKeyboard);
        return;
    }

    $keyboard = prescribePuffFriend2($userId, $page);
    $keyboard->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
    $msg = msg('choose_friend', $lang);
    $bot->editMessageText(text: $msg,chat_id: $bot->chat()->id, message_id: $bot->message()->message_id, reply_markup: $keyboard);
});



$bot->onMessage(function (Nutgram $bot) use ($cache){
    $role = checkRole($bot->userId());
    createLog(TIME_NOW, $role, $bot->userId(), 'message', $bot->message()->text);
    $text = $bot->message()->text;
    $lang = lang($bot->userId());
    if (str_contains($text, msg('approve', $lang))) {
        $check = checkPuff($bot->userId());
        if ($check) {
            foreach ($check as $puff) {
                $inlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('puff_decline', $lang), null,null, 'callback_puff_decline '.$puff['puffId'].' '.$puff['userFrom']),InlineKeyboardButton::make(msg('puff_approve', $lang), null,null, 'callback_puff_approve '.$puff['puffId'].' '.$puff['userFrom']))->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
                $username = getUsername($puff['userFrom']);
                $msg = $username.msg('prescribed_puff', $lang)."\n\n( ".$puff['prescribed_at'].' )';
                $bot->sendMessage($msg, chat_id: $puff['userTo'], reply_markup: $inlineKeyboard);
                sleep(1);
            }
        } else {
            $keyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('cancel', lang($bot->userId())), null,null, 'callback_cancel'));
            $bot->sendMessage(msg('no_puffs', $lang), reply_markup: $keyboard);
        }
    }
    elseif (str_contains($text, msg('prescribe', $lang))) {
        $prescribePuffKeyboard = prescribePuffFriend2($bot->userId());
        $prescribePuffKeyboard->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
        $bot->sendMessage(msg('choose_friend', $lang), reply_markup: $prescribePuffKeyboard);
    }
    elseif (str_contains($text, msg('frends', $lang))) {
        $friends = findFriends($bot->userId()); 
        if ($friends == 0) {
            $referral = getReferralCode($bot->userId());
            if (!$referral) {
                $referral = createRefCode($bot->userId());
            }
            $deeplink = new DeepLink();
            $deep_link = $deeplink->start(BOT_USERNAME, $referral);
            $share_link = "https://t.me/share/url?url=".$deep_link;
            $inlineKeyboard = InlineKeyboardMarkup::make()
            ->addRow(InlineKeyboardButton::make(msg('invite_friend', $lang), $share_link))->addRow(InlineKeyboardButton::make(msg('cancel', lang($bot->userId())), null,null, 'callback_cancel'));
            $bot->sendMessage(msg('no_friends', $lang), reply_markup: $inlineKeyboard);
        } else {
            $friends_info = showFriends($bot->userId());
            $keyboard = getFriendKeyboard($friends_info, 0, $bot->userId());
            $keyboard->addRow(InlineKeyboardButton::make(msg('cancel', $lang), null,null, 'callback_cancel'));
            $msg = msg('friends_quant', $lang).$friends.msg('invite_using_btn_below', $lang);
            $bot->sendMessage($msg, reply_markup: $keyboard);
        }
    }
    elseif (str_contains($text, msg('status', $lang))) {
        $inlineKeyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make(msg('change_language', lang($bot->userId())), null, null, 'callback_change_lang'));
        $bot->sendMessage(constructStatus($bot->userId()), reply_markup: $inlineKeyboard);
    }
    elseif (str_contains($text, msg('info', $lang))) {
        $rand = rand(1, 8);
        $msg = msg('information', lang($bot->userId())).msg('fact'.$rand, lang($bot->userId()));
        $keyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make(msg('support', lang($bot->userId())), null,null, 'callback_support'))
        ->addRow(InlineKeyboardButton::make(msg('donate', lang($bot->userId())), null,null, 'callback_donate'))
        ->addRow(InlineKeyboardButton::make(msg('cancel', lang($bot->userId())), null,null, 'callback_cancel'));
        $bot->sendMessage($msg, reply_markup: $keyboard);
    } 
    elseif ($role != 'user') {
        if (str_contains($text, '.msg')) {
            $bot->deleteMessage($bot->userId(),$bot->messageId());
            $parts = explode(' ', $text);
            array_shift($parts);
            $msg_lang = array_shift($parts);
            $message = implode(' ', $parts);
            $users = selectUsersWithLang($bot->userId(), $msg_lang);
            foreach ($users as $user) {
                $bot->sendMessage(text: "❗️✉️  ".$message, chat_id: $user['userId']);
                sleep(1);
            }
            $bot->sendMessage("Message \"".$message."\" sent to all users with language: ".$msg_lang);
            createLog(TIME_NOW, 'admin', $bot->userId(), 'msg_all', $bot->message()->text);
        } 
        if (str_contains($text, '.stat')) {
            $bot->deleteMessage($bot->userId(),$bot->messageId());
            $bot->sendMessage(showBotStat());
        }
        if (str_contains($text, '.help')) {
            $bot->deleteMessage($bot->userId(),$bot->messageId());
            $bot->sendMessage(msg('WIP', lang($bot->userId())));
        }
        if (str_contains($text, '.sup')) {
            $bot->deleteMessage($bot->userId(),$bot->messageId());
            $bot->sendMessage(msg('WIP', lang($bot->userId())));
        }
        if (str_contains($text, '.warn')) {
            $bot->deleteMessage($bot->userId(),$bot->messageId());
            $bot->sendMessage(msg('WIP', lang($bot->userId())));
        }
        //$bot->sendMessage(msg('no_perm', $lang));
    } 
    else {
        $checkIfSupport = checkIfSupport($bot->userId(), $bot->messageId());
        if ($checkIfSupport) {
            createSupport($bot->userId(), $text);
            $bot->sendMessage(msg('support_delivered', lang($bot->userId())));
        } else {
            $bot->sendMessage(msg('unknown', lang($bot->userId())));
            //$bot->sendMessage("You send: ".$text);
        }
    }
});

$bot->run();

?>
