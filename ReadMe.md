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

