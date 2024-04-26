-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 26 2024 г., 02:17
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
-- База данных: `shtrafnayabot_rem`
--

-- --------------------------------------------------------

--
-- Структура таблицы `friend_request`
--

CREATE TABLE `friend_request` (
  `user_from` int(11) NOT NULL,
  `user_to` int(11) NOT NULL,
  `status` varchar(8) NOT NULL COMMENT '(friends/denied/unfriend/..)',
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `friend_request`:
--   `user_to`
--       `user` -> `userId`
--   `user_from`
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
  `entityId` int(11) NOT NULL,
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
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `userId` int(11) NOT NULL,
  `firstName` varchar(30) NOT NULL,
  `lastName` varchar(30) NOT NULL,
  `username` varchar(60) NOT NULL,
  `language` varchar(2) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `lastVisit` timestamp NOT NULL DEFAULT current_timestamp(),
  `registeredAt` datetime NOT NULL,
  `role` varchar(7) NOT NULL DEFAULT 'user' COMMENT '(user/moder/admin)',
  `deleted` varchar(3) NOT NULL DEFAULT 'no',
  `banned` varchar(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `user`:
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
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `log`
--
ALTER TABLE `log`
  MODIFY `logId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `friend_request`
--
ALTER TABLE `friend_request`
  ADD CONSTRAINT `friend_request_ibfk_1` FOREIGN KEY (`user_to`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friend_request_ibfk_2` FOREIGN KEY (`user_from`) REFERENCES `user` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`entityId`) REFERENCES `user` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
