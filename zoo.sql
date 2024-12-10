-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Дек 10 2024 г., 11:43
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
-- База данных: `zoo`
--

-- --------------------------------------------------------

--
-- Структура таблицы `admin`
--

CREATE TABLE `admin` (
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `name`, `surname`, `date_of_birth`, `password`) VALUES
(1, 'admin1', 'Grace', 'Anderson', '2001-12-15', '12345678q'),
(2, 'admin2', 'Henry', 'Brown', '1999-05-05', '12345678w'),
(3, 'admin3', 'Chanel', 'Cisneros', '1998-01-15', '12345678e'),
(4, 'admin4', 'Maizie', 'Fuller', '1989-04-10', '12345678r'),
(5, 'admin5', 'Naima', 'Leonard', '2001-07-22', '12345678t'),
(6, 'admin6', 'Greta', 'Marsh', '1998-09-12', '12345678y'),
(7, 'admin7', 'Sydney', 'Tucker', '1991-12-01', '12345678u'),
(8, 'admin8', 'Stasyan', 'Miller', '1999-07-07', '12345678i'),
(9, 'admin9', 'Stacy', 'Wilson', '2003-04-08', '12345678o'),
(10, 'admin10', 'Frank', 'Escobar', '2002-07-21', '12345678p');

-- --------------------------------------------------------

--
-- Структура таблицы `animal`
--

CREATE TABLE `animal` (
  `animal_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `type` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `animal`
--

INSERT INTO `animal` (`animal_id`, `name`, `type`, `date_of_birth`) VALUES
(1, 'Lion', 'Mammal', '2011-05-14'),
(2, 'Tiger', 'Mammal', '2012-07-22'),
(3, 'Elephant', 'Mammal', '2004-03-19'),
(4, 'Giraffe', 'Mammal', '2012-10-12'),
(5, 'Penguin', 'Bird', '2016-01-20'),
(6, 'Parrot', 'Bird', '2014-08-15'),
(7, 'Crocodile', 'Reptile', '2009-09-09'),
(8, 'Snake', 'Reptile', '2019-02-28'),
(9, 'Zebra', 'Mammal', '2014-04-10'),
(10, 'Kangaroo', 'Mammal', '2017-11-07');

-- --------------------------------------------------------

--
-- Структура таблицы `aviary`
--

CREATE TABLE `aviary` (
  `aviary_id` bigint(20) UNSIGNED NOT NULL,
  `size` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `number` int(11) NOT NULL,
  `animal_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `aviary`
--

INSERT INTO `aviary` (`aviary_id`, `size`, `location`, `number`, `animal_id`, `admin_id`, `ticket_id`) VALUES
(1, 210, 'zone 1', 1, 1, 1, 1),
(2, 200, 'zone 2', 2, 2, 2, 2),
(3, 220, 'zone 3', 3, 3, 3, 3),
(4, 160, 'zone 4', 4, 4, 4, 4),
(5, 120, 'zone 5', 5, 5, 5, 5),
(6, 120, 'zone 6', 6, 6, 6, 6),
(7, 180, 'zone 7', 7, 7, 7, 7),
(8, 100, 'zone 8', 8, 8, 8, 8),
(9, 160, 'zone 9', 9, 9, 9, 9),
(10, 150, 'zone 10', 10, 10, 10, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `info`
--

CREATE TABLE `info` (
  `info_id` bigint(20) UNSIGNED NOT NULL,
  `visitor_id` bigint(20) UNSIGNED NOT NULL,
  `information_about_animal_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `info`
--

INSERT INTO `info` (`info_id`, `visitor_id`, `information_about_animal_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 5),
(6, 6, 6),
(7, 7, 7),
(8, 8, 8),
(9, 9, 9),
(10, 10, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `information_about_animal`
--

CREATE TABLE `information_about_animal` (
  `information_about_animal_id` bigint(20) UNSIGNED NOT NULL,
  `animal_id` bigint(20) UNSIGNED NOT NULL,
  `information` text NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `information_about_animal`
--

INSERT INTO `information_about_animal` (`information_about_animal_id`, `animal_id`, `information`, `admin_id`) VALUES
(1, 1, 'Lions are social animals, living in groups called prides.', 1),
(2, 2, 'Tigers are solitary animals, mostly found in forests.', 2),
(3, 3, 'Elephants are the largest land animals on Earth.', 3),
(4, 4, 'Giraffes are known for their long necks and legs.', 4),
(5, 5, 'Penguins are flightless birds that live in cold climates.', 5),
(6, 6, 'Parrots are highly intelligent and colorful birds.', 6),
(7, 7, 'Crocodiles are semi-aquatic reptiles, known for their powerful jaws.', 7),
(8, 8, 'Snakes are legless reptiles with elongated bodies.', 8),
(9, 9, 'Zebras are known for their distinctive black-and-white striped coats.', 9),
(10, 10, 'Kangaroos are marsupials known for their strong hind legs and jumping ability.', 10);

-- --------------------------------------------------------

--
-- Структура таблицы `manager`
--

CREATE TABLE `manager` (
  `manager_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `manager`
--

INSERT INTO `manager` (`manager_id`, `username`, `name`, `surname`, `date_of_birth`, `password`) VALUES
(0, '', 'Temp Manager', '', '0000-00-00', NULL),
(1, 'manager1', 'Elouise', 'Ware', '2000-07-14', '12345678q'),
(2, 'manager2', 'Osman', 'Dixon', '1998-03-22', '12345678w'),
(3, 'manager3', 'Tori', 'Warner', '1989-12-09', '12345678e'),
(4, 'manager4', 'Cynthia', 'Hodges', '2001-05-17', '12345678r'),
(5, 'manager5', 'Stacy', 'Escobar', '1997-08-29', '12345678t'),
(6, 'manager6', 'Chris', 'Gibbons', '1999-11-02', '12345678y'),
(7, 'manager7', 'Anisa', 'Nixon', '2004-01-19', '12345678u'),
(8, 'manager8', 'Kateryna', 'Rojas', '1996-06-11', '12345678i'),
(9, 'manager9', 'Honey', 'Shelton', '1995-09-05', '12345678o'),
(10, 'manager10', 'Connor', 'White', '1989-10-23', '12345678p');

-- --------------------------------------------------------

--
-- Структура таблицы `ticket`
--

CREATE TABLE `ticket` (
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(20) NOT NULL,
  `price` int(11) NOT NULL,
  `visitor_id` bigint(20) UNSIGNED NOT NULL,
  `manager_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `ticket`
--

INSERT INTO `ticket` (`ticket_id`, `type`, `price`, `visitor_id`, `manager_id`, `admin_id`) VALUES
(1, 'Child', 75, 1, 1, 1),
(2, 'Adult', 150, 2, 2, 2),
(3, 'Senior', 95, 3, 3, 3),
(4, 'Adult', 150, 4, 4, 4),
(5, 'Adult', 150, 5, 5, 5),
(6, 'Child', 75, 6, 6, 6),
(7, 'Adult', 150, 7, 7, 7),
(8, 'Adult', 150, 8, 8, 8),
(9, 'Senior', 95, 9, 9, 9),
(10, 'Child', 75, 10, 10, 10);

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_order`
--

CREATE TABLE `ticket_order` (
  `order_id` int(11) NOT NULL,
  `ticket_type` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `status` enum('pending','confirmed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `visitor`
--

CREATE TABLE `visitor` (
  `visitor_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `surname` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `visitor`
--

INSERT INTO `visitor` (`visitor_id`, `username`, `name`, `surname`, `date_of_birth`, `password`) VALUES
(0, '', 'Temp Visitor', '', '0000-00-00', ''),
(1, 'scotomik', 'Michael', 'Scott', '2016-02-14', '12345678q'),
(2, 'lheight', 'Dwight', 'Scott', '1989-04-20', '12345678w'),
(3, 'jimhell', 'Jim', 'Halpert', '1962-06-28', '12345678e'),
(4, 'bepam', 'Pam', 'Beesly', '1992-11-05', '12345678r'),
(5, 'gosling', 'Ryan', 'Howard', '1994-03-10', '12345678t'),
(6, 'bernady', 'Andy', 'Bernard', '2014-12-17', '12345678y'),
(7, 'stasyao', 'Stanley', 'Hudson', '1979-07-03', '12345678u'),
(8, 'morris', 'Philip', 'Vance', '1985-09-09', '12345678i'),
(9, 'maros', 'Oscar', 'Martinez', '1964-05-15', '12345678o'),
(10, 'gelin', 'Angela', 'Martin', '2011-10-25', '12345678p'),
(11, 'qweasd', 'qwe', 'ads', '2024-12-05', '123');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_id` (`admin_id`);

--
-- Индексы таблицы `animal`
--
ALTER TABLE `animal`
  ADD UNIQUE KEY `animal_id` (`animal_id`);

--
-- Индексы таблицы `aviary`
--
ALTER TABLE `aviary`
  ADD UNIQUE KEY `aviary_id` (`aviary_id`),
  ADD KEY `animal_id` (`animal_id`,`admin_id`,`ticket_id`),
  ADD KEY `animal_id_2` (`animal_id`,`admin_id`,`ticket_id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Индексы таблицы `info`
--
ALTER TABLE `info`
  ADD UNIQUE KEY `info_id` (`info_id`),
  ADD KEY `visitor_id` (`visitor_id`,`information_about_animal_id`),
  ADD KEY `information_about_animal_id` (`information_about_animal_id`);

--
-- Индексы таблицы `information_about_animal`
--
ALTER TABLE `information_about_animal`
  ADD UNIQUE KEY `information_about_animal_id` (`information_about_animal_id`),
  ADD KEY `animal_id` (`animal_id`,`admin_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Индексы таблицы `manager`
--
ALTER TABLE `manager`
  ADD UNIQUE KEY `manager_id` (`manager_id`);

--
-- Индексы таблицы `ticket`
--
ALTER TABLE `ticket`
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD UNIQUE KEY `ticket_id_3` (`ticket_id`),
  ADD KEY `visitor_id` (`visitor_id`,`manager_id`,`admin_id`),
  ADD KEY `ticket_id_2` (`ticket_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Индексы таблицы `ticket_order`
--
ALTER TABLE `ticket_order`
  ADD PRIMARY KEY (`order_id`);

--
-- Индексы таблицы `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`visitor_id`),
  ADD UNIQUE KEY `visitor_id` (`visitor_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `animal`
--
ALTER TABLE `animal`
  MODIFY `animal_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `aviary`
--
ALTER TABLE `aviary`
  MODIFY `aviary_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `info`
--
ALTER TABLE `info`
  MODIFY `info_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `information_about_animal`
--
ALTER TABLE `information_about_animal`
  MODIFY `information_about_animal_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `manager`
--
ALTER TABLE `manager`
  MODIFY `manager_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000;

--
-- AUTO_INCREMENT для таблицы `ticket`
--
ALTER TABLE `ticket`
  MODIFY `ticket_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `ticket_order`
--
ALTER TABLE `ticket_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `visitor`
--
ALTER TABLE `visitor`
  MODIFY `visitor_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `aviary`
--
ALTER TABLE `aviary`
  ADD CONSTRAINT `aviary_ibfk_1` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aviary_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `aviary_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `info`
--
ALTER TABLE `info`
  ADD CONSTRAINT `info_ibfk_1` FOREIGN KEY (`information_about_animal_id`) REFERENCES `information_about_animal` (`information_about_animal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `info_ibfk_2` FOREIGN KEY (`info_id`) REFERENCES `visitor` (`visitor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `information_about_animal`
--
ALTER TABLE `information_about_animal`
  ADD CONSTRAINT `information_about_animal_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `information_about_animal_ibfk_2` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`animal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`manager_id`) REFERENCES `manager` (`manager_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_3` FOREIGN KEY (`visitor_id`) REFERENCES `visitor` (`visitor_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
