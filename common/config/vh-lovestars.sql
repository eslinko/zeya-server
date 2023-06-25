-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: realmart.mysql.ukraine.com.ua:3306
-- Время создания: Июн 25 2023 г., 10:32
-- Версия сервера: 5.7.42-46-log
-- Версия PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vh-lovestars`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Events`
--

CREATE TABLE `Events` (
  `id` int(11) NOT NULL,
  `facebook_url` text,
  `name` text,
  `description` longtext,
  `description_langs` text,
  `raw_facebook_date` text,
  `start_timestamp` text,
  `end_timestamp` text,
  `raw_facebook_place_image` text,
  `place` text,
  `address` text,
  `facebook_category` text,
  `ticket_url` text,
  `organizer_id` int(11) DEFAULT NULL,
  `organizer_facebook_title` text,
  `status` enum('added','opened','in_processing','analysing','processed') DEFAULT 'added'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `HashTag`
--

CREATE TABLE `HashTag` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Languages`
--

CREATE TABLE `Languages` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `code` text,
  `status` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Languages`
--

INSERT INTO `Languages` (`id`, `title`, `code`, `status`) VALUES
(1, 'Русский', 'ru', 'active'),
(11, 'English', 'en', 'active'),
(18, 'Estonian - eesti', 'et', 'active');

-- --------------------------------------------------------

--
-- Структура таблицы `Lovestar`
--

CREATE TABLE `Lovestar` (
  `id` int(11) NOT NULL,
  `issuingAction` int(11) NOT NULL,
  `currentOwner` int(11) NOT NULL,
  `birthTimestamp` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m140622_111540_create_image_table', 1526123301),
('m140622_111545_add_name_to_image_table', 1526123303);

-- --------------------------------------------------------

--
-- Структура таблицы `Partner`
--

CREATE TABLE `Partner` (
  `id` int(11) NOT NULL,
  `legalName` text NOT NULL,
  `description` text,
  `billingVATNumber` text NOT NULL,
  `billingDetails` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `PartnerBillingInvoice`
--

CREATE TABLE `PartnerBillingInvoice` (
  `id` int(11) NOT NULL,
  `partnerId` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `billingStartDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `billingEndDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ruleActionsList` text NOT NULL,
  `totalLovecoisIssued` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `PartnerRule`
--

CREATE TABLE `PartnerRule` (
  `id` int(11) NOT NULL,
  `partnerId` int(11) NOT NULL,
  `title` text NOT NULL,
  `triggerName` text NOT NULL,
  `emissionCalculationBaseValue` int(11) NOT NULL,
  `emissionCalculationPercentage` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `PartnerRuleAction`
--

CREATE TABLE `PartnerRuleAction` (
  `id` int(11) NOT NULL,
  `ruleId` int(11) NOT NULL,
  `timestamp` text NOT NULL,
  `ruleTitle` text NOT NULL,
  `emissionCalculationBaseValue` int(11) NOT NULL,
  `emissionCalculationPercentage` decimal(10,0) NOT NULL DEFAULT '1',
  `triggerName` text NOT NULL,
  `emittedLovestars` int(11) NOT NULL,
  `emittedLovestarsUser` int(11) NOT NULL,
  `approvalQRCode` text NOT NULL,
  `approvalStatus` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Teacher`
--

CREATE TABLE `Teacher` (
  `id` int(11) NOT NULL,
  `publicAlias` text NOT NULL,
  `title` text NOT NULL,
  `description` text,
  `hashtags` text,
  `status` enum('active','archive','','') NOT NULL DEFAULT 'active'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `TeacherOutcome`
--

CREATE TABLE `TeacherOutcome` (
  `id` int(11) NOT NULL,
  `teacherId` int(11) NOT NULL,
  `type` enum('1','2') NOT NULL,
  `title` text NOT NULL,
  `description` text,
  `hashtags` text,
  `valueInLovestarsFrom` int(11) NOT NULL,
  `valueInLovestarsTo` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `TeachingTransaction`
--

CREATE TABLE `TeachingTransaction` (
  `id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `userGivingLovestars` int(11) NOT NULL,
  `teacherGivingValue` int(11) NOT NULL,
  `lovestars` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `TelegramChatsLastMessage`
--

CREATE TABLE `TelegramChatsLastMessage` (
  `id` int(11) NOT NULL,
  `chat_id` text NOT NULL,
  `last_message` text NOT NULL,
  `active_teacher_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `User`
--

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `publicAlias` text COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `temp_email` text COLLATE utf8_unicode_ci,
  `telegram` text COLLATE utf8_unicode_ci,
  `currentLovestarsCounter` text COLLATE utf8_unicode_ci,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `verifiedUser` tinyint(1) NOT NULL DEFAULT '0',
  `verificationCode` text COLLATE utf8_unicode_ci,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `role` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT 'user',
  `language` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `User`
--

INSERT INTO `User` (`id`, `username`, `full_name`, `publicAlias`, `email`, `temp_email`, `telegram`, `currentLovestarsCounter`, `auth_key`, `password_hash`, `password_reset_token`, `status`, `verifiedUser`, `verificationCode`, `created_at`, `updated_at`, `role`, `language`) VALUES
(1, 'admin', '', '', NULL, NULL, NULL, NULL, '6Dr5NuCEFJtMICtRH4mWY0Ut7RE2t48E', '$2y$13$MqD1V6a9zE3/FSZzqcf9x.0QGFDPUDYkiYa9Nv5O6/R2YE0dKdSGe', NULL, 10, 1, '', 1524209419, 1674819430, 'admin', 'en');

-- --------------------------------------------------------

--
-- Структура таблицы `User2Partner`
--

CREATE TABLE `User2Partner` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `partnerId` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `User2Teacher`
--

CREATE TABLE `User2Teacher` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `teacherId` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Events`
--
ALTER TABLE `Events`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `HashTag`
--
ALTER TABLE `HashTag`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Languages`
--
ALTER TABLE `Languages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Lovestar`
--
ALTER TABLE `Lovestar`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `Partner`
--
ALTER TABLE `Partner`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `PartnerBillingInvoice`
--
ALTER TABLE `PartnerBillingInvoice`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `PartnerRule`
--
ALTER TABLE `PartnerRule`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `PartnerRuleAction`
--
ALTER TABLE `PartnerRuleAction`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Teacher`
--
ALTER TABLE `Teacher`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `TeacherOutcome`
--
ALTER TABLE `TeacherOutcome`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `TelegramChatsLastMessage`
--
ALTER TABLE `TelegramChatsLastMessage`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Индексы таблицы `User2Partner`
--
ALTER TABLE `User2Partner`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `User2Teacher`
--
ALTER TABLE `User2Teacher`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Events`
--
ALTER TABLE `Events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `HashTag`
--
ALTER TABLE `HashTag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `Languages`
--
ALTER TABLE `Languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `Lovestar`
--
ALTER TABLE `Lovestar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `Partner`
--
ALTER TABLE `Partner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `PartnerBillingInvoice`
--
ALTER TABLE `PartnerBillingInvoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `PartnerRule`
--
ALTER TABLE `PartnerRule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `PartnerRuleAction`
--
ALTER TABLE `PartnerRuleAction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `Teacher`
--
ALTER TABLE `Teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `TeacherOutcome`
--
ALTER TABLE `TeacherOutcome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `TelegramChatsLastMessage`
--
ALTER TABLE `TelegramChatsLastMessage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `User`
--
ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT для таблицы `User2Partner`
--
ALTER TABLE `User2Partner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `User2Teacher`
--
ALTER TABLE `User2Teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
