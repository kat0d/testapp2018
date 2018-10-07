Коротокое описание решения для коллеги не программиста:
Таблицы в базе данных связаны:
Cash->-Users->-Cash
Gifts->-Users->-Gifts
Gifts->-Users->-gift2post
Всего 4 таблицы.

Cash - остаток денежных стредств сайта
Gifts - остаток и типы призов
Users - информация по пользователю(логин, кол-во баллов и денег на сайте, адресс, дата регистрации)
gift2post - информация о почтовых отправлениях призов пользователям

Аутентификация только по логину(без ввода пароля).
Когда пользователь нажимает кнопку "получить случайный приз" происходит проверка в базе данных на наличие доступных ресурсов(денег, призов). Призы, количество которых = 0, не отображаются.
Разлогиниться можно только закрыв браузер.
Конвертация денег в баллы работает 100денег = 200 баллов.
Отправка денег на банковский счет и отправка призов почтой реализована частично.
Информация в хедере без перезагрузки страницы не обновляется. Но в БД все изменения происходят, ну или почти все.



Комментарии по коду:
php7+, без использования фреймворков.
- в задании об аутентификации, кроме как "...после аутентификации, пользователь...", ничего не сказано, поэтому ее нет, поэтому логиниться можно без пароля, циклом проверяются логины в "index.php", если такого нет, создается новый пользователь.
- из ООП здесь только класс БД "db.php", там же меняются и данные к подключению к БД. Хотя имеется класс UserController, но он пустой, все в "db.php".
- файлы с классами находятся в папке "controllers", и подгружаются файлом "loader.php", нужно просто добавлять обьекты
- почти вся логика по работе с призами в файле "ajax-requests.php", который реагирует на POST запросы из файла "test.js" со скриптами, POST запросом передаются данные только для проверки на isset(). под GET ничего на сайте нет.
- "money2bank.php" и "gift2user.php" служат скорее в декоративных целях
- о безопасности говорить не приходится, в "ajax-requests.php" фильтровать попросту нечего, кроме $_SERVER['PHP_AUTH_USER']
- инфа о пользователе в хедере(<header></header>), через AJAX не обновляется


по БД:
- столбец "gifts"(предательский)) в таблице Users предназначен для временного хранения данных "имя=количество" о призе, пока пользователь думает что с этим призом делать (отправить/отказаться и т.п.), после того как пользователь принял решение(нажал/не нажал на одну из кнопок), у пользователя это поле очищается в NULL, и в замисимости от решения пользователя, переносится в другую таблицу или возвращается в таблицу Gifts(не путать со столбцом!)/Cash. Если пользователь закрыл браузер, во время принятия решения о действии с призом, данные остаются в таблице "Users", и очищаются/возвращаются, только после того, как пользователь повторно залогинится или перезагрузит страницу
- Таблица "gifts2post" хранит данные по призам, которые нужно отправить почтой, через сайт, данные в нее только добавляются(т.е. не обновляются/изменяются и т.д.), столбец "address" берется не из полей пользователя а просто прописан в запросе, вот так:
$db->query("INSERT INTO gifts2post (status, login, user_id, gifts, address) VALUES ('not send', '$user_login', '$user_id', '$gift2send', 'На Марс')");


Вообще код довольно кривой, но рабочий(по-моему), а времени оптимизировать уже нет. как то так. Спасибо за внимание)



Код, чтобы создать БД:


-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 13 2018 г., 13:38
-- Версия сервера: 5.7.20
-- Версия PHP: 7.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `testapp`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Cash`
--

CREATE TABLE `Cash` (
  `id` int(5) UNSIGNED NOT NULL,
  `Number` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Cash`
--

INSERT INTO `Cash` (`id`, `Number`) VALUES
(1, 717);

-- --------------------------------------------------------

--
-- Структура таблицы `Gifts`
--

CREATE TABLE `Gifts` (
  `id` int(6) UNSIGNED NOT NULL,
  `Gift` varchar(100) NOT NULL,
  `Number` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Gifts`
--

INSERT INTO `Gifts` (`id`, `Gift`, `Number`) VALUES
(1, 'Prize-1', 147),
(2, 'Prize-2', 0),
(3, 'Prize-3', 95);

-- --------------------------------------------------------

--
-- Структура таблицы `gifts2post`
--

CREATE TABLE `gifts2post` (
  `id` int(6) UNSIGNED NOT NULL,
  `status` varchar(15) DEFAULT NULL,
  `login` varchar(30) NOT NULL,
  `user_id` int(15) DEFAULT '0',
  `gifts` varchar(500) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `req_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `send_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `gifts2post`
--

INSERT INTO `gifts2post` (`id`, `status`, `login`, `user_id`, `gifts`, `address`, `req_date`, `send_date`) VALUES
(1, 'not send', 'user1', 6, 'Gifts=1', 'На Марс', '2018-09-12 04:23:48', NULL),
(2, 'send', 'user1', 6, 'Gifts=2', 'На Луну', '2018-09-12 05:23:48', '2018-09-15 05:23:48'),
(11, 'not send', 'user1', 6, 'Gifts=1', 'На Марс', '2018-09-13 08:35:31', NULL),
(12, 'not send', 'user1', 6, 'Gifts=3', 'На Марс', '2018-09-13 08:36:05', NULL),
(13, 'not send', 'user1', 6, 'Gifts=1', 'На Марс', '2018-09-13 08:37:02', NULL),
(14, 'not send', 'user1', 6, 'Gifts=1', 'На Марс', '2018-09-13 08:37:44', NULL),
(15, 'not send', 'user1', 6, 'Gifts=3', 'На Марс', '2018-09-13 08:38:55', NULL),
(16, 'not send', 'user1', 6, 'Gifts=1', 'На Марс', '2018-09-13 08:40:45', NULL),
(17, 'not send', 'user1', 6, 'Gifts=1', 'На Марс', '2018-09-13 08:41:48', NULL),
(21, 'not send', 'user1', 6, 'Gifts=1', 'На Марс', '2018-09-13 09:23:26', NULL),
(22, 'not send', 'user2', 7, 'Gifts=3', 'На Марс', '2018-09-13 10:03:09', NULL),
(23, 'not send', 'user2', 7, 'Gifts=3', 'На Марс', '2018-09-13 10:03:14', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `Users`
--

CREATE TABLE `Users` (
  `id` int(6) UNSIGNED NOT NULL,
  `login` varchar(30) NOT NULL,
  `points` int(50) DEFAULT '0',
  `cash` int(50) DEFAULT '0',
  `gifts` varchar(500) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `Users`
--

INSERT INTO `Users` (`id`, `login`, `points`, `cash`, `gifts`, `address`, `reg_date`) VALUES
(1, 'asdas', 0, 0, NULL, NULL, '2018-09-12 04:23:48'),
(3, 'usr', 0, 0, NULL, NULL, '2018-09-12 04:29:37'),
(4, 'yui', 0, 0, NULL, NULL, '2018-09-12 05:06:54'),
(5, 'werwr', 0, 0, NULL, NULL, '2018-09-12 05:07:01'),
(6, 'user1', 2164, 375, NULL, NULL, '2018-09-12 05:07:16'),
(7, 'user2', 284, 0, 'Cash=67', NULL, '2018-09-12 05:07:26'),
(8, 'user3', 0, 0, NULL, NULL, '2018-09-12 05:07:30'),
(9, 'user4', 0, 0, NULL, NULL, '2018-09-12 05:07:35'),
(10, 'user5', 0, 0, NULL, NULL, '2018-09-12 05:07:38'),
(11, 'user6', 0, 0, NULL, NULL, '2018-09-12 05:07:42'),
(12, 'user7', 0, 0, NULL, NULL, '2018-09-12 05:07:46'),
(13, 'user8', 0, 0, NULL, NULL, '2018-09-12 05:07:50'),
(14, 'aaa', 0, 0, NULL, NULL, '2018-09-12 05:23:08'),
(15, 'asd', 0, 0, NULL, NULL, '2018-09-12 05:23:53'),
(16, 'dfg', 0, 0, NULL, NULL, '2018-09-12 05:26:16'),
(17, 'e4', 0, 0, NULL, NULL, '2018-09-12 05:27:16'),
(18, 'sdfs', 0, 0, NULL, NULL, '2018-09-12 05:29:34'),
(19, 'sdf', 0, 0, NULL, NULL, '2018-09-12 14:51:11'),
(20, 'fy', 0, 0, NULL, NULL, '2018-09-12 16:12:17'),
(21, 'tyu', 0, 0, NULL, NULL, '2018-09-12 16:13:45'),
(22, 'rrr', 0, 0, NULL, NULL, '2018-09-13 05:00:17'),
(23, 's', 0, 0, NULL, NULL, '2018-09-13 09:48:38');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Cash`
--
ALTER TABLE `Cash`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Gifts`
--
ALTER TABLE `Gifts`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `gifts2post`
--
ALTER TABLE `gifts2post`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Cash`
--
ALTER TABLE `Cash`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `Gifts`
--
ALTER TABLE `Gifts`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `gifts2post`
--
ALTER TABLE `gifts2post`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



