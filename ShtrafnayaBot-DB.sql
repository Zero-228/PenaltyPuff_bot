-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Июн 01 2024 г., 17:58
-- Версия сервера: 8.0.37
-- Версия PHP: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `zerosite_penaltyPuff`
--

-- --------------------------------------------------------

--
-- Структура таблицы `friend_request`
--

CREATE TABLE `friend_request` (
  `user_from` bigint NOT NULL,
  `user_to` bigint NOT NULL,
  `status` varchar(8) COLLATE utf8mb4_general_ci NOT NULL COMMENT '(friends/denied/unfriend/)',
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `log`
--

CREATE TABLE `log` (
  `logId` int NOT NULL,
  `createdAt` datetime NOT NULL,
  `entity` varchar(15) COLLATE utf8mb4_general_ci NOT NULL COMMENT '(user/bot/admin/..)',
  `entityId` bigint NOT NULL,
  `context` varchar(15) COLLATE utf8mb4_general_ci NOT NULL COMMENT '(callback/comand/..)',
  `message` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `puff`
--

CREATE TABLE `puff` (
  `puffId` int NOT NULL,
  `userFrom` bigint DEFAULT NULL,
  `userTo` bigint DEFAULT NULL,
  `status` varchar(10) COLLATE utf8mb4_general_ci NOT NULL COMMENT '(pending/approved/denied/...)',
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `prescribed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `support`
--

CREATE TABLE `support` (
  `id` int NOT NULL,
  `userId` bigint NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT '(pending/answered/canceled)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `userId` bigint NOT NULL,
  `firstName` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `lastName` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `language` varchar(2) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lastVisit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registeredAt` datetime NOT NULL,
  `role` varchar(7) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user' COMMENT '(user/moder/admin)',
  `referral` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `karma` int NOT NULL DEFAULT '0',
  `deleted` varchar(3) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'no',
  `banned` varchar(3) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `warn`
--

CREATE TABLE `warn` (
  `id` int NOT NULL,
  `userFrom` bigint DEFAULT NULL,
  `userTo` bigint NOT NULL,
  `reason` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `warndAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `friend_request`
--
ALTER TABLE `friend_request`
  ADD PRIMARY KEY (`user_from`,`user_to`),
  ADD KEY `user_to` (`user_to`);

--
-- Индексы таблицы `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`logId`),
  ADD KEY `entityId` (`entityId`);

--
-- Индексы таблицы `puff`
--
ALTER TABLE `puff`
  ADD PRIMARY KEY (`puffId`),
  ADD KEY `puff_ibfk_2` (`userTo`),
  ADD KEY `puff_ibfk_1` (`userFrom`);

--
-- Индексы таблицы `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_ibfk_1` (`userId`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`);

--
-- Индексы таблицы `warn`
--
ALTER TABLE `warn`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warn_ibfk_1` (`userFrom`),
  ADD KEY `warn_ibfk_2` (`userTo`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `log`
--
ALTER TABLE `log`
  MODIFY `logId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `puff`
--
ALTER TABLE `puff`
  MODIFY `puffId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `support`
--
ALTER TABLE `support`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `warn`
--
ALTER TABLE `warn`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `friend_request`
--
ALTER TABLE `friend_request`
  ADD CONSTRAINT `friend_request_ibfk_1` FOREIGN KEY (`user_from`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friend_request_ibfk_2` FOREIGN KEY (`user_to`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`entityId`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `puff`
--
ALTER TABLE `puff`
  ADD CONSTRAINT `puff_ibfk_1` FOREIGN KEY (`userFrom`) REFERENCES `user` (`userId`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `puff_ibfk_2` FOREIGN KEY (`userTo`) REFERENCES `user` (`userId`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `support`
--
ALTER TABLE `support`
  ADD CONSTRAINT `support_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `warn`
--
ALTER TABLE `warn`
  ADD CONSTRAINT `warn_ibfk_1` FOREIGN KEY (`userFrom`) REFERENCES `user` (`userId`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `warn_ibfk_2` FOREIGN KEY (`userTo`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
