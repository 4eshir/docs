-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 30 2022 г., 12:40
-- Версия сервера: 10.3.22-MariaDB
-- Версия PHP: 7.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `u0457336_index_new`
--

-- --------------------------------------------------------

--
-- Структура таблицы `material_object_subobject`
--

CREATE TABLE `material_object_subobject` (
  `id` int(11) NOT NULL,
  `material_object_id` int(11) NOT NULL,
  `subobject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `material_object_subobject`
--
ALTER TABLE `material_object_subobject`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_object_id` (`material_object_id`),
  ADD KEY `subobject_id` (`subobject_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `material_object_subobject`
--
ALTER TABLE `material_object_subobject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `material_object_subobject`
--
ALTER TABLE `material_object_subobject`
  ADD CONSTRAINT `material_object_subobject_ibfk_1` FOREIGN KEY (`material_object_id`) REFERENCES `material_object` (`id`),
  ADD CONSTRAINT `material_object_subobject_ibfk_2` FOREIGN KEY (`subobject_id`) REFERENCES `subobject` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
