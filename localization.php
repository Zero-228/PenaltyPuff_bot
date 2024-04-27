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
		'canceled' => '❌ Action canceled ',
		'status_frends' => '🙋‍♂️ Frends',
		'status_acceptedPuffs' => '✅ Accepted puffs',
		'status_prescribedPuffs' => '✏️ Prescribed puffs',
		'status_registered' => '📅 Registered',
		'no_friends' => "It seems like you don't have connected friends yet ☹️\n\nInvite them with a button below to prescribe \nthem penalty puffs properly 😄",
		'invite_friend' => '💃 Invite 💃',
		'new_friends' => '🎉 Congratilations! You’ve made a new friend! Hurry up and prescribe him a penalty puff! 🌳',
		'already_friends' => '🔔 This contact is already in your friends list. Check it up using the button below! 🙋‍♂️',
		'updated_friends' => '🔔 We updated your friendship status with this user. Prescribe him a penalty puff for that. 🌳',
		'invite_using_btn_below' => "\n\nInvite friends using \na button below to \nprescribe them penalty \npuffs properly 🌳",
		'friends_quant' => '🙋‍♂️ Number of friends: ',
		'prescribe_puff' => '🌳 Prescribe puff 💨',
		'warn' => '⚠️ Warn',
		'remove_friend' => 'Remove 🚫',
		'inline_invite_friend' => '🙋‍♂️ Invite a friend',
		'unfriend' => '🚫 Friend is done. Friend is no more.',
		'prescribe_success' => "🌳 You prescribed penalty puff to your friend 👍",
		'prescribe_self' => "⚠️ You can't prescribe penalty puff to yourself",
		'prescribe_delay' => "⚠️ You need to wait at least 3 minutes \nto prescribe new penalty puff to this friend",
		'friend'=>'Friend',
		'prescribed_puff' => " prescribed you a penalty puff. \nApprove it quicker 🌳",
		'puff_decline' => '🚫 Decline',
		'puff_approve' => 'Approve ✅',
		'choose_friend' => "Choose a friend to whom you \nwant to prescribe penalty puff",
		'declined_puff' => " declined your penalty puff 🚫",
		'puff_declined' => "🚫 You declined penalty puff. Your friend will be notified!",
		'approved_puff' => " approved your penalty puff ✅",
		'puff_approved' => "✅ You approved penalty puff 👍",
		'puff_approve_delay' => "⚠️ Not so fast! Smoke it first, then approve.",
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
		'canceled' => '❌ Действие отменено ',
		'status_frends' => '🙋‍♂️ Друзья',
		'status_acceptedPuffs' => '✅ Приянл штрафных',
		'status_prescribedPuffs' => '✏️ Выписал штрафных',
		'status_registered' => '📅 Зарегистрирован',
		'no_friends' => "Кажется что у тебя еще нет друзей ☹️\n\nПригласи их с помощью кнопки ниже чтобы \nвыписывать им штрафные как положено 😄",
		'invite_friend' => '💃 Пригласить 💃',
		'new_friends' => '🎉 Поздравляем! Ты завёл нового друга! Поторопись и выпиши ему штрафную! 🌳',
		'already_friends' => '🔔 этот контакт уже находится в твоём списке друзей. Проверь его с помощью кнопки ниже 🙋‍♂️',
		'updated_friends' => '🔔 Мы обновили статус вашей дружбы с этим пользователем. Выпиши ему за это штрафную! 🌳',
		'invite_using_btn_below' => "\n\nПригласи друзей с помощью \nкнопки ниже чтобы \nвыписывать им штрафные \nкак положено 🌳",
		'friends_quant' => '🙋‍♂️ Количество друзей: ',
		'prescribe_puff' => '🌳 Выписать штрафную 💨',
		'warn' => '⚠️ Жалоба',
		'remove_friend' => 'Удалить 🚫',
		'inline_invite_friend' => '🙋‍♂️ Пригласить друга',
		'unfriend' => '🚫 Ты удалил этого друга.',
		'prescribe_success' => "🌳 Ты выписал штрафную банку своему другу 👍",
		'prescribe_self' => "⚠️ Ты неможешь выписывать штрафную сам себе",
		'prescribe_delay' => "⚠️ Тебе нужно подождать минимум 3 минуты \nчтобы выписать новую штрафную этому другу",
		'friend'=>'Друг',
		'prescribed_puff' => " выписал тебе штрафную. \nПодтверди её скорей 🌳",
		'puff_decline' => '🚫 Отказаться',
		'puff_approve' => 'Подтвердить ✅',
		'choose_friend' => "Выбери друга котоорому ты \nхочешь выписать штрафную",
		'declined_puff' => " відмовився від твоєї штрафної 🚫",
		'puff_declined' => "🚫 Ти відмовився від штрафної. Твій друг про це дізнається!",
		'approved_puff' => " підтвердив твою штрафну ✅",
		'puff_approved' => "✅ Ти підтвердив штрафну 👍",
		'puff_approve_delay' => "⚠️ Не так быстро! Сначала скури, потом подтверждай.",
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
		'canceled' => '❌ Подiя вiдмiнена ',
		'status_frends' => '🙋‍♂️ Друзi',
		'status_acceptedPuffs' => '✅ Прийняв штрафних',
		'status_prescribedPuffs' => '✏️ Виписав штрафних',
		'status_registered' => '📅 Зареєстрован',
		'no_friends' => "Здається щто в тебе немає друзiв ☹️\n\nЗапроси їх за допомогою книпки нижче \nщоб виписувати їм штрафнi як належить 😄",
		'invite_friend' => '💃 Запросити 💃',
		'new_friends' => '🎉 Вітаємо! Ви зробили нового друга! Поспішайте і випишите йому штрафну! 🌳',
		'already_friends' => '🔔 Цей контакт вже є у вашому списку друзів. Перевірте це за допомогою кнопки нижче! 🙋‍♂️',
		'updated_friends' => '🔔 Ми оновили ваш статус дружби з цим користувачем. Випишiть йому за це штрафну! 🌳',
		'invite_using_btn_below' => "\n\nЗапроси друзiв за \nдопомогою кнопки нижче \nщоб впипсувати їм штрафнi \nяк належить 🌳",
		'friends_quant' => '🙋‍♂️ Кiлькiсть друзiв: ',
		'prescribe_puff' => '🌳 Виписати штрафну 💨',
		'warn' => '⚠️ Скарга',
		'remove_friend' => 'Видалити 🚫',
		'inline_invite_friend' => '🙋‍♂️ Запросити друга',
		'unfriend' => '🚫 Ти видалив цього друга.',
		'prescribe_success' => "🌳 Ти виписав штрафну банку свойому другу 👍",
		'prescribe_self' => "⚠️ Ти не можешь виписати штрафну самому собi",
		'prescribe_delay' => "⚠️ Тобі потрібно зачекати що найменше 3 хвилини \nщоб виписати нову штрафну цому другу",
		'friend'=>'Друг',
		'prescribed_puff' => " виписав тобі штрафну. \nПідтверди її скоріше! 🌳",
		'puff_decline' => '🚫 Відмовитися',
		'puff_approve' => 'Підтвердити ✅',
		'choose_friend' => "Обери друга якому ти \nбажаєш виписати штрафну",
		'declined_puff' => " відмовився від твоєї штрафної 🚫",
		'puff_declined' => "🚫 Ти відмовився від штрафної. Твій друг про це дізнається!",
		'approved_puff' => " підтвердив твою штрафну ✅",
		'puff_approved' => "✅ Ти підтвердив штрафну 👍",
		'puff_approve_delay' => "⚠️ Не так швидко! Спочатку скури, потім підтверджуй.",
	),
);

function msg($message_key, $user_language) {
	global $languages;
	$res = isset($languages[$user_language][$message_key]) ? $languages[$user_language][$message_key] : "Unknown key";
	return $res;
}

?>