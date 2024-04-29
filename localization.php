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
		'welcome' => "Hello! I'm Penalty Puff, your companion \nin the fun and fair issuance of penalty \npuffs. Trust me with this challenging task, \nand together we'll make the process more\n engaging and friendly! Shall we begin? 💨✍️",
		'welcome_back' => "Welcome back! Glad to see you again \nin Penalty Puff. Ready to continue our journey? \n\nIf you have any questions or need assistance, \nfeel free to reach out! 💨✍️",
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
		'no_puffs' => "It seems like prescribed you penalty puff yet ☹️\n\nAsk a friend to prescribe you one, оr prescribe \nhim a penalty puff to keep a friend on his toes 🙋‍♂️",
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
		'warn_user' => "🛂 Choose the warn reason",
		'warn_btn_falseApr' => '⚠️ False approvement',
		'warn_btn_spam' => '📢 Spam',
		'warn_sent' => "✅ Warn sent to administration. \nThank you for your help 😄",
		'information' => "📢 This bot was especially designed for prescribing \npenalty puffs properly. \n\n🌳 Penalty puffs can be prescribed for \nany type of misconduct, or to \nshow your friends your care \n\n",
		'fact1' => "❔ Cool Fact:\nPenalty puff was invented by ancient Ukrs \nsomewhere in the forests of Kievan Rus",
		'fact2' => "❔ Cool Fact:\nSomeone says that smoking natural \nthings prolongs life",
		'fact3' => "❔ Cool Fact:\nCannabis has been around for 28.6 million years \nand you still haven’t smoked your penalty puff",
		'support' => '🛟 Support',
		'donate' => '💵 For treatment',
		'donation' => "💵 Thank you for supporting this bot! \nIt really matters for us 😄",
		'show_wallet' => '👛 Show TON wallet number',
	),
	'ru' => array(
		'welcome' => "Привет! Я - Penalty Puff, твой помощник \nв веселом и справедливом выписывании \nштрафных. Доверь мне эту нелегкую задачу, \nи вместе мы сделаем процесс более интересным \nи дружелюбным! Давай начнем? 💨✍️",
		'welcome_back' => "С возвращением! Рад видеть тебя снова \nв Penalty Puff. Готов продолжить наше \nприключение? \n\nЕсли у тебя есть какие-то вопросы или нужна \nпомощь, не стесняйся обращаться! 💨✍️",
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
		'no_puffs' => "Кажется что тебе еще не выписали штрафных ☹️\n\nПопроси друга выписать одну, или выпиши ему \nштрафную чтоб держать друга в тонусе 🙋‍♂️",
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
		'declined_puff' => " отказался от твоей штрафной 🚫",
		'puff_declined' => "🚫 Ты отказался от штрафной. Твой друг про это узнает!",
		'approved_puff' => " подтвердил твою штрафную ✅",
		'puff_approved' => "✅ Ты подтвердил штрафную 👍",
		'puff_approve_delay' => "⚠️ Не так быстро! Сначала скури, потом подтверждай.",
		'warn_user' => "🛂 Выбери причину жалобы",
		'warn_btn_falseApr' => '⚠️ Ложное подтвержение',
		'warn_btn_spam' => '📢 Спам',
		'warn_sent' => "✅ Жалоба отправлена администрации. \nСпасибо за вашу помощь 😄",
		'information' => "📢 Этот бот специально спроектирован для \nвыписывания штрафных как положено. \n\n🌳 Штрафные банки могут выписываться за \nлюбой проступок, или чтобы показать \nсвою заботу своим друзьям \n\n",
		'fact1' => "❔ Интересный Факт:\nШтрафные банки были изобретены древними \nУкрами где-то в лесах Киевской Руси",
		'fact2' => "❔ Интересный Факт:\nХодят слухи что курение натуральных \nвеществ продлевает жизнь",
		'fact3' => "❔ Интересный Факт:\nКаннабис появился около 28.6 миллионов лет назад \nа ты всё еще не выкурил свою штрафную",
		'support' => '🛟 Поддержка',
		'donate' => '💵 На лечение',
		'donation' => "💵 Спасибо за поддержку этого бота! \nЭто действительно важно для нас 😄",
		'show_wallet' => '👛 Показать номер TON кошелька',
	),
	'uk' => array(
		'welcome' => "Привіт! Я - Penalty Puff, твій помічник \nу веселому та справедливому виписуванні \nштрафних. Довірся мені з цією нелегкою \nзадачею, і разом ми зробимо процес більш \nцікавим та дружелюбним! Давай почнемо? 💨✍️",
		'welcome_back' => "Ласкаво просимо назад! Рад бачити вас знову \nв Penalty Puff. Готові продовжити нашу подорож? \n\nЯкщо у вас є які-небудь питання або потрібна \nдопомога, не соромтеся звертатися! 💨✍️",
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
		'no_friends' => "Здається що в тебе немає друзiв ☹️\n\nЗапроси їх за допомогою книпки нижче \nщоб виписувати їм штрафнi як належить 😄",
		'no_puffs' => "Здається що тобi ще не виписали штрафних ☹️\n\nПопорохай друга щоб вiн виписав штучку, чи \nвипиши йому штрафну щоб трмати друга в тонусi 🙋‍♂️",
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
		'warn_user' => "🛂 Обери причину скарги",
		'warn_btn_falseApr' => '⚠️ Неправдиві підтвердження',
		'warn_btn_spam' => '📢 Спам',
		'warn_sent' => "✅ Скарга відправлена до адміністрації. \nДякуємо за вашу допомогу 😄",
		'information' => "📢 Цей бот був спеціально розроблений для \nналежного призначення штрафних затяжок. \n\n🌳 Штрафні можуть бути призначені \nза будь-який вид проступку, або щоб \nпоказати друзям свою турботу \n\n",
		'fact1' => "❔ Цiкавий Факт:\nШтрафнi були придумані стародавніми \nУкрами десь у лісах Київської Русі",
		'fact2' => "❔ Цiкавий Факт:\nХодять чутки, що куріння натуральних \nречовин продовжує життя",
		'fact3' => "❔ Цiкавий Факт:\nКанабіс з'явився близько 28,6 мільйонів років \nтому, а ти досі не скурив свою штрафну",
		'support' => '🛟 Підтримка',
		'donate' => '💵 На лікування',
		'donation' => "💵 Дякуємо за підтримку цього бота! \nЦе дійсно важливо для нас 😄",
		'show_wallet' => '👛 Показати номер TON гаманця',
	),
);

function msg($message_key, $user_language) {
	global $languages;
	if (!isset($languages[$user_language])) {$user_language = 'en';}
	$res = isset($languages[$user_language][$message_key]) ? $languages[$user_language][$message_key] : "Unknown key";
	return $res;
}

?>