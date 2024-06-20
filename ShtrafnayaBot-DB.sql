-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 20 2024 г., 16:03
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `penaltypuff_bot`
--

-- --------------------------------------------------------

--
-- Структура таблицы `friend_request`
--

CREATE TABLE `friend_request` (
  `user_from` bigint(20) NOT NULL,
  `user_to` bigint(20) NOT NULL,
  `status` varchar(8) NOT NULL COMMENT '(friends/denied/unfriend/)',
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `friend_request`:
--   `user_from`
--       `user` -> `userId`
--   `user_to`
--       `user` -> `userId`
--

-- --------------------------------------------------------

--
-- Структура таблицы `log`
--

CREATE TABLE `log` (
  `logId` int(11) NOT NULL,
  `createdAt` datetime NOT NULL,
  `entity` varchar(15) NOT NULL COMMENT '(user/bot/admin/..)',
  `entityId` bigint(20) NOT NULL,
  `context` varchar(15) NOT NULL COMMENT '(callback/comand/..)',
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `log`:
--   `entityId`
--       `user` -> `userId`
--

-- --------------------------------------------------------

--
-- Структура таблицы `puff`
--

CREATE TABLE `puff` (
  `puffId` int(11) NOT NULL,
  `userFrom` bigint(20) DEFAULT NULL,
  `userTo` bigint(20) DEFAULT NULL,
  `status` varchar(10) NOT NULL COMMENT '(pending/approved/denied/...)',
  `comment` text DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `prescribed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `puff`:
--   `userFrom`
--       `user` -> `userId`
--   `userTo`
--       `user` -> `userId`
--

-- --------------------------------------------------------

--
-- Структура таблицы `support`
--

CREATE TABLE `support` (
  `id` int(11) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `message` text NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'pending' COMMENT '(pending/answered/canceled)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `support`:
--   `userId`
--       `user` -> `userId`
--

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `userId` bigint(20) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `lastName` varchar(30) NOT NULL,
  `username` varchar(60) NOT NULL,
  `language` varchar(2) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `lastVisit` timestamp NOT NULL DEFAULT current_timestamp(),
  `registeredAt` datetime NOT NULL,
  `role` varchar(7) NOT NULL DEFAULT 'user' COMMENT '(user/moder/admin)',
  `referral` varchar(15) NOT NULL,
  `karma` int(11) NOT NULL DEFAULT 0,
  `deleted` varchar(3) NOT NULL DEFAULT 'no',
  `banned` varchar(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `user`:
--

-- --------------------------------------------------------

--
-- Структура таблицы `warn`
--

CREATE TABLE `warn` (
  `id` int(11) NOT NULL,
  `userFrom` bigint(20) DEFAULT NULL,
  `userTo` bigint(20) NOT NULL,
  `reason` varchar(50) NOT NULL,
  `warndAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `warn`:
--   `userFrom`
--       `user` -> `userId`
--   `userTo`
--       `user` -> `userId`
--

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
  MODIFY `logId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `puff`
--
ALTER TABLE `puff`
  MODIFY `puffId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `support`
--
ALTER TABLE `support`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `warn`
--
ALTER TABLE `warn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `support_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`) ON UPDATE CASCADE;

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
