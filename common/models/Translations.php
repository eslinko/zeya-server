<?php

namespace common\models;

class Translations {
    private static $tralsnates = [
        'ecosystemGrowthNotification' => [
            'en' => 'Good morning!
This is your personalised update on the growth of Zeya4eve-community.

During the latest 24h:

1. {newDayUsersCount} new users successfully signed-up. Now we have {countUsers} users.

2. Your connections brought {newSecondaryUsers} users. Now your secondary circles contain {secondaryUsers} users.

3. New signups brought you {newSecondaryUsersWithSharedInterests} connections candidates. Soon you will have an access to the creative expressions of {secondaryUsersWithSharedInterests} users. Among those, you will be validating your emotional resonance and get new connections!',
            'ru' => 'Доброе утро!
Это Ваш персональный отчет о росте Zeya4eve-сообщества.

За последние 24 часа:

1. Успешно зарегистрировано {newDayUsersCount} новых пользователей. Теперь у нас {countUsers} пользователей.

2. Ваши подключения привели {newSecondaryUsers} пользователей. Теперь ваши вторичные круги содержат {secondaryUsers} пользователей.

3. Новые регистрации принесли вам {newSecondaryUsersWithSharedInterests} кандидатов на подключение. Вскоре вы получите доступ к творческим проявлениям пользователей {secondaryUsersWithSharedInterests}. Среди них вы сможете подтвердить свой эмоциональный резонанс и получить новые связи!',
            'et' => 'Tere hommikust!
See on teie personaalne värskendus Zeya4eve-kogukonna kasvu kohta.

Viimase 24h jooksul:

1. {newDayUsersCount} uusi kasutajaid edukalt registreerunud. Nüüd on meil {countUsers} kasutajaid.

2. Teie ühendused tõid {newSecondaryUsers} kasutajaid. Nüüd on teie sekundaarsetes ringkondades {secondaryUsers} kasutajaid.

3. Uued liitumised tõid sulle {newSecondaryUsersWithSharedInterests} ühendused kandidaadid. Varsti on teil juurdepääs {secondaryUsersWithSharedInterests} kasutajate loomingulistele väljendustele. Nende seas kinnitate oma emotsionaalse resonantsi ja saate uusi ühendusi!'
        ],
        "🚀 %s wants to vibe with you!" => [
            'en' => "🚀 %s wants to vibe with you!",
            'ru' => "🚀 %s готов подружиться и весело провести время!"
        ],
        "🎉 %s vibed with your request!" => [
            'en' => "🎉 %s vibed with your request!",
            'ru' => "🎉 %s на одной волне с твоим запросом!"
        ],
        "🥺 %s didn't vibe with your request" => [
            'en' => "🥺 %s didn't vibe with your request",
            'ru' => "🥺 %s на разных волнах с твоим запросом"
        ],
        "🔥 Hot news! You and %s are a cosmic match!" => [
            'en' => "🔥 Hot news! You and %s are a cosmic match!",
            'ru' => "🔥 Вот это да! Ты и %s - космическое совпадение!"
        ],
        "🌟 Get a Lovestar! %s joined Zeya4Eve space using your invite code!" => [
            'en' => "🌟 Get a Lovestar! %s joined Zeya4Eve space using your invite code!",
            'ru' => "🌟 Принимай Lovestar! %s присоединился к Zeya4Eve используя твой код приглашения!"
        ],
        "🌟 Get a Lovestar! %s joined Zeya4Eve space via the invitation of your connection %s!" => [
            'en' => "🌟 Get a Lovestar! %s joined Zeya4Eve space via the invitation of your connection %s!",
            'ru' => "🌟 Принимай Lovestar! %s присоединился к Zeya4Eve через приглашение от %s из твоих связей!"
        ],
        "💌 Hey, spread the love! You have %d invite codes chillin" => [
            'en' => "💌 Hey, spread the love! You have %d invite codes chillin",
            'ru' => "💌 Привет, распространи любовь! У тебя %d инвайт-кодов готовы к действию"
        ],
        "⏳ Quick! Your creative vibe fades in %d hours. Keep the fire alive and share your new expression!" => [
            'en' => "⏳ Quick! Your creative vibe fades in %d hours. Keep the fire alive and share your new expression!",
            'ru' => "⏳ Поспеши! Твоё творческое выражение заканчивается через %d часов. Поддержи огонь и поделись новым творением!"
        ],
        "Accept" => [
            'en' => "ACCEPT",
            'ru' => "ПРИНЯТЬ"
        ],
        "Decline" => [
            'en' => "DECLINE",
            'ru' => "ОТКЛОНИТЬ"
        ],
        "Show_match_info" => [
            'en' => "SHOW MATCH INFO",
            'ru' => "ПОКАЗАТЬ СОВПАДЕНИЕ"
        ],
        "Update_ce" => [
            'en' => "UPDATE EXPRESSION",
            'ru' => "ОБНОВИТЬ ТВОРЕНИЕ"
        ],
        "This invitation was already sent by you and still pending." => [
            'en' => "You've already sent this invitation, and it's pending.",
            'ru' => "Ты уже отправил(а) это приглашение, и оно в режиме ожидания."
        ],
        "This invitation was already sent by you and accepted." => [
            'en' => "You've already sent this invitation, and it's accepted.",
            'ru' => "Ты уже отправил(а) это приглашение, и оно принято."
        ],
        "This invitation was already sent by you and rejected." => [
            'en' => "You've already sent this invitation, and it's rejected. You may contact the person and ask them to find your rejected invitation and re-accept it.",
            'ru' => "Ты уже отправил(а) это приглашение, и оно отклонено. Ты можешь связаться с этим человеком и попросить его найти твое приглашение, чтобы его принять."
        ],
        "This invitation was already sent to you and accepted." => [
            'en' => "You've already received this invitation, and it's accepted.",
            'ru' => "Ты уже получил(а) это приглашение, и оно принято."
        ],
        "Connection request has been sent." => [
            'en' => "Connection request has been sent.",
            'ru' => "Запрос добавления в связи отправлен."
        ],
        "Btn_My invitation codes" => [
            'en' => "My Invitation Codes",
            'ru' => "Мои коды приглашений"
        ],
    ];

    public static function s($text, $lang = 'en'){
        return !empty(self::$tralsnates[$text]) && !empty(self::$tralsnates[$text][$lang]) ? self::$tralsnates[$text][$lang] : $text;
    }
}