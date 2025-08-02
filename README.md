## Trustpilot Parser Laravel 11 | PHP 8.2 | MySql 5.7 | Composer version 2.7.4 | node -v22.14.0


Описание

Проект предназначен для парсинга отзывов с Trustpilot и сохранения информации в базу данных. Также поддерживается экспорт в CSV и загрузка аватаров пользователей.

## Установка

## 1. Установка зависимостей в консоли проекта
   composer install, <br>
   npm install, <br>
   npm run build <br>
   php artisan migrate <br>
   php artisan key:generate <br>
   php artisan storage:link <br>
   Дамп базы прилагается, но он не нужен потому как миграция установит все таблицы

## База данных называется: trustpilot-parce
добавьте доступы в поля в .env файле <br>
DB_HOST= <br>
DB_PORT=3306 <br>
DB_DATABASE=trustpilot-parce <br>
DB_USERNAME= <br>
DB_PASSWORD= <br>

## Настройте URL 
в .env файле <br>
сейчас он: <br>
APP_URL=https://trustpilot-parce.loc


## 2 Для того чтоб загрузить фаил с ссылками, нужно зарегестрироваться на сайте
после регистрации, вы попадете в админ часть где в меню вы можете перейти
по ссылке - Download Link и загрузить ваш файл, по умолчанию фаил уже загружен с такими ссылками:
<br>
https://www.trustpilot.com/review/wg.casino <br>
https://www.trustpilot.com/review/payments.astropay.com <br>
https://www.trustpilot.com/review/blockbets.casino <br>
https://www.trustpilot.com/review/bitspin365.com <br>
https://www.trustpilot.com/review/wazbee.casino <br>

## 3 Для Экспорта в CSV нужно кликнуть на меню - Dashboard и нажать на кнопку экспортировать
после чего появится кнопка для скачивания файла
## Примечание:
скачивайте csv файл после парсинга сайта

## Для того что спарсить сайт,пропишите в консоли проекта
## php artisan queue:work - для активации очереди <br>
## php artisan app:parse-trustpilot-reviews - для запуска парсера <br>
после окончания загрузки, будет указано в консоли - Все загруженно в базу! 
<br> в админ части можете скачать CSV как описано выше
<br> все кртинки будут загружаться в каталог storage/app/public/image <br>
а все данные будут загружены в таблицу reviews
все логи пишутся в фаил storage/logs/laravel.log
## В корне проекта в архиве Data.rar находятся db, image и reviews.csv уже с спарсеными данными




