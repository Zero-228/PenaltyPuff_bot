<?php 

/*
require 'localization.php';
*/

/*
$inlineKeyboard = InlineKeyboardMarkup::make()
        ->addRow(InlineKeyboardButton::make(msg('change_language', lang($bot->userId())), null, null, 'callback_change_lang'));
$bot->sendMessage(msg('welcome', lang($bot->userId())), reply_markup: $inlineKeyboard);
*/

/*
$bot->onCallbackQueryData('callback_change_lang', function (Nutgram $bot) {
    createLog(TIME_NOW, 'user', $bot->userId(), 'callback', 'change language');
    $changeLangInlineKeyboard = InlineKeyboardMarkup::make()->addRow(InlineKeyboardButton::make(msg('language', 'en'), null, null, 'callback_change_lang_to en'))->addRow(InlineKeyboardButton::make(msg('language', 'uk'), null, null, 'callback_change_lang_to uk'))->addRow(InlineKeyboardButton::make(msg('language', 'ru'), null, null, 'callback_change_lang_to ru'));
    $bot->sendMessage(msg('choose_language', lang($bot->userId())), reply_markup: $changeLangInlineKeyboard);
    $bot->answerCallbackQuery();
});
*/

/*
$bot->onCallbackQueryData('callback_change_lang_to {param}', function (Nutgram $bot, $param) {
    changeLanguage($bot->userId(), $param);
    $bot->sendMessage(msg('language_changed', lang($bot->userId())));
    $bot->answerCallbackQuery();
});
*/

$languages = array(
	'en' => array(
		'welcome' => 'Greetengs',
		'welcome_back' => 'Welcome back to main menu',
		'change_language' => '🌐 Change language',
		'choose_language' => 'Choose language',
		'language_changed' => 'Language changed',
		'language' => '🇬🇧 English',
		'approve' => '| Approve ✅ |',
		'prescribe' => '| Prescribe ✏️ |',
		'frends' => '| Frends 🙋‍♂️ |',
		'status' => '| Status 📜 |',
		'info' => '| Info 📢 |',
		'WIP' => "Development of this feature still in progress.\nThank you for your patience. 🧑‍💻",
		'cancel' => 'Cancel ❌',
		'canceled' => 'Action canceled ❌',
		'status_frends' => '🙋‍♂️ Frends',
		'status_acceptedPuffs' => '✅ Accepted puffs',
		'status_prescribedPuffs' => '✏️ Prescribed puffs',
		'status_registered' => '📅 Registered',
		'no_friends' => "It seems like you don't have connected friends yet(\n\nInvite them with a button below to prescribe \nthem penalty puffs properly)",
		'invite_friend' => '💃 Invite 💃',
	),
	'ru' => array(
		'welcome' => 'Добро пожаловать',
		'welcome_back' => 'С возвращением в главное меню',
		'change_language' => '🌐 Сменить язык',
		'choose_language' => 'Выберите язык',
		'language_changed' => 'Язык изменён',
		'language' => '🇷🇺 Русский',
		'approve' => "| Подтвердить ✅ |",
		'prescribe' => '| Выписать ✏️ |',
		'frends' => '| Друзья 🙋‍♂️ |',
		'status' => '| Статус 📜 |',
		'info' => '| Инфо 📢 |',
		'WIP' => "Данная функция находится в процессе разработки.\nСпасибо за ваше терпение. 🧑‍💻",
		'cancel' => 'Отменить ❌',
		'canceled' => 'Действие отменено ❌',
		'status_frends' => '🙋‍♂️ Друзья',
		'status_acceptedPuffs' => '✅ Приянл штрафных',
		'status_prescribedPuffs' => '✏️ Выписал штрафных',
		'status_registered' => '📅 Зарегистрирован',
		'no_friends' => "Кажется что у тебя еще нет друзей(\n\nПригласи их с помощью кнопки ниже чтобы \nвыписывать им штрафные как положено)",
		'invite_friend' => '💃 Пригласить 💃',
	),
	'uk' => array(
		'welcome' => 'Ласкаво просимо',
		'welcome_back' => 'З поверненням до головного меню',
		'change_language' => '🌐 Змiнити мову',
		'choose_language' => 'Оберiть мову',
		'language_changed' => 'Мову змiнено',
		'language' => '🇺🇦 Українська',
		'approve' => '| Пiдтвердити ✅ |',
		'prescribe' => '| Виписати ✏️ |',
		'frends' => '| Друзi 🙋‍♂️ |',
		'status' => '| Статус 📜 |',
		'info' => '| Інфо 📢 |',
		'WIP' => "Ця функція знаходиться в процессi розробки.\nДякую за ваше терпіння. 🧑‍💻",
		'cancel' => 'Вiдмiнити ❌',
		'canceled' => 'Подiя вiдмiнена ❌',
		'status_frends' => '🙋‍♂️ Друзi',
		'status_acceptedPuffs' => '✅ Прийняв штрафних',
		'status_prescribedPuffs' => '✏️ Виписав штрафних',
		'status_registered' => '📅 Зареєстрован',
		'no_friends' => "Здається щто в тебе немає друзiв(\n\nЗапроси їх за допомогою книпки нижче \nщоб впипсувати їм штрафнi як належить)",
		'invite_friend' => '💃 Запросити 💃',
	),
);

function msg($message_key, $user_language) {
	global $languages;
	$res = isset($languages[$user_language][$message_key]) ? $languages[$user_language][$message_key] : "Unknown key";
	return $res;
}

?>