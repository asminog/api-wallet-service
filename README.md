Тестовое задание
================
PHP 7.4, Mysql 5.7

Задание выполнено используя Yii2 микро фреймворк c дополнительными пакетами для разработки дебаг-панелью и генерации swagger документации.

Race condition при минусовом балансе переложен на базу данных, для этого переключает ее в sql_mode = 'TRADITIONAL'

---
Запуск в Docker
---------------
1. Запустить в корне каталога: `docker-compose up -d`
2. Зайти в консоль PHP контейнера: `docker exec -it test-php-1 bash`

---
Запуск в локальном окружении
----------------------------
* Создать базу данных в MySQL
* Открыть `src/config.php` и указать настройки подключения к ней.
* Nginx или Apache должен смотреть в директорию `public`, настройки можно взять из https://www.yiiframework.com/doc/guide/2.0/ru/start-installation#rekomenduemye-nastrojki-apache 

---
Настройка проекта
-----------------
* Установить пакеты зависимости `composer install`
* Запустить миграции `./yii migrate`
* Загрузить курсы валют `./yii currency/update`
* Добавить обновление валют в крон
* Документация по API находится в директории http://localhost/docs


