<?php

namespace common\models;

class Translations {
    private static $tralsnates = [
        'ecosystemGrowthNotification' => [
            'en' => 'Good morning!
This is your personalised update on the growth of Zeya-community.

During the latest 24h:

1. {newDayUsersCount} new users successfully signed-up. Now we have {countUsers} users.

2. Your connections brought {newSecondaryUsers} users. Now your secondary circles contain {secondaryUsers} users.

3. New signups brought you {newSecondaryUsersWithSharedInterests} connections candidates. Soon you will have access to the creative expressions of {secondaryUsersWithSharedInterests} users. Among those, you will be validating your emotional resonance and get new connections!',
            'ru' => 'Доброе утро!
Это Ваш персональный отчет о росте Zeya-сообщества.

За последние 24 часа:

1. Успешно зарегистрировано {newDayUsersCount} новых пользователей. Теперь у нас {countUsers} пользователей.

2. Ваши подключения привели {newSecondaryUsers} пользователей. Теперь ваши вторичные круги содержат {secondaryUsers} пользователей.

3. Новые регистрации принесли вам {newSecondaryUsersWithSharedInterests} кандидатов на подключение. Вскоре вы получите доступ к творческим проявлениям пользователей {secondaryUsersWithSharedInterests}. Среди них вы сможете подтвердить свой эмоциональный резонанс и получить новые связи!',
            'et' => 'Tere hommikust!
See on teie personaalne värskendus Zeya-kogukonna kasvu kohta.

Viimase 24h jooksul:

1. {newDayUsersCount} uusi kasutajaid edukalt registreerunud. Nüüd on meil {countUsers} kasutajaid.

2. Teie ühendused tõid {newSecondaryUsers} kasutajaid. Nüüd on teie sekundaarsetes ringkondades {secondaryUsers} kasutajaid.

3. Uued liitumised tõid sulle {newSecondaryUsersWithSharedInterests} ühendused kandidaadid. Varsti on teil juurdepääs {secondaryUsersWithSharedInterests} kasutajate loomingulistele väljendustele. Nende seas kinnitate oma emotsionaalse resonantsi ja saate uusi ühendusi!'
        ]
    ];

    public static function s($text, $lang = 'en'){
        return !empty(self::$tralsnates[$text]) && !empty(self::$tralsnates[$text][$lang]) ? self::$tralsnates[$text][$lang] : $text;
    }
}