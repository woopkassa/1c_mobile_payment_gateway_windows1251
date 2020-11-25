# 1c_mobile_payment_gateway_windows1251
Woopkassa mobile payment gateway for 1C CMS as a module. Encoding version windows1251.

## Требования
Для работы модуля должно быть установлено и включено PHP расширение SOAP.

## Установка
1. Создать новую папку с названием sale_payment в директории \bitrix\php_interface\include. Это можно сделать прямо в административной странице сайта, нажав на кнопку "Добавить" на странице
Контент->Структура сайта->Файлы и папки, перед этим перейти в папку \bitrix\php_interface\include.
![Alt text](.README/bitrix_1.png?raw=true)
2. Закинуть в нее архив нужного модуля соответствующей кодировки и распаковать
![Alt text](.README/bitrix_2.png?raw=true)
3. Выбрать пункт меню Магазин->Настройки->Платежные системы, нажать кнопку "Добавить платежную систему"
![Alt text](.README/bitrix_3.png?raw=true)
4. В качестве обработчика выбрать желаемый модуль
![Alt text](.README/bitrix_4.png?raw=true)
5. Добавить "Логотип платёжной системы", нажав на кнопку "Добавить файл" и выбрать логотип из папки соответствующего модуля
![Alt text](.README/bitrix_5.png?raw=true)
6. Поставить нужную кодировку UTF-8/Windows1251
![Alt text](.README/bitrix_6.png?raw=true)
7. Заполнить "Остальные свойства платежных систем" в соответствии со своими данными
![Alt text](.README/bitrix_7.png?raw=true)
````
Пример:

API URL: http://www.test.wooppay.com/api/wsdl
API Username: test_merch
API Password: A12345678a
Order prefix: mobile

````
Перейти в магазин и оплатить товар.