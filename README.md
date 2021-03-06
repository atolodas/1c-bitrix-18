<<<<<<< HEAD
# Описание платежного модуля ИнвойсБокс для Bitrix

Платёжный модуль Битрикс для интеграции платёжной системы «ИнвойсБокс» и CMS Битрикс. Реализована поддержка платёжного API. Протестировано на системе Битрикс 18 в кодировке cp1251 и utf8.

Модуль позволяет организовать оплату товаров в магазине через систему «ИнвойсБокс». Система «ИнвойсБокс» позволяет Интернет-магазину принимать оплату через популярные инструменты -
банковские карты, системы Интернет-банка, электронные деньги, терминалы, кассы банков и счета мобильных телефонов. Оплата приходит на расчётный счёт компании или индивидуального предпринимателя.
При работе с «ИнвойсБокс» Интернет-магазину не требуется покупать и обслуживать онлайн-кассу в соответствии с ФЗ-54, чеки для плательщиков «ИнвойсБокс» формирует за Интернет-магазин.

# Установка и настройка модуля ИнвойсБокс на Bitrix 16/17

## Установка модуля

Распакуйте архив. Скопируйте папку <strong>invoicebox.payment</strong> и всё её содержимое на ваш сервер в папку <strong> \local\modules\ </strong>, если директории \local\modules\ не существует, то ее необходимо создать

## Настройка модуля

1. Зайти в административную часть интернет магазина. Перейдите в раздел <strong>«MarketPlace» -> «Установленные решения»</strong>;
2. Найдите модуль <strong>«ИнвойсБокс»</strong> (InvoiceBox, invoicebox.payment) и установите его;
3. Перейдите в раздел <strong>«Магазин» -> «Платёжные системы»</strong> и нажмите на кнопку «Добавить платёжную систему»
   - Выберите "Обработчик": <strong>«ИнвойсБокс»</strong> (invoicebox);
   - Если требуется измените "Заголовок" и "Название";
   - Выберите логотип платёжной системы (<a href="https://www.invoicebox.ru/downloads/white_200x100.png">скачать</a>);
   - Укажите кодировку "UTF-8".

<img src="https://raw.githubusercontent.com/InvoiceBox/1c-bitrix-16/master/docimg/invocebox1.png">


4. Перейти на нужную вкладку ("Физические лица" или "Юридические лица" ) и заполните необходимую информацию:
идентификатор магазина (ID магазина), региональный код магазина и API ключ. Если необходимо укажите:
 статус после которого можно будет оплатить заказ (если вам требуется отложенный платеж) и статус заказа после оплаты. 	 Указанные сведения предоставляются
при заключении договора с системой «ИнвойсБокс», а также доступны в личном кабинете магазина на сайте системы;
    
<img src="https://raw.githubusercontent.com/InvoiceBox/1c-bitrix-16/master/docimg/invocebox2.png">

5. Дополнительные настройки.

- Тестовый режим - включите его для проведения тестовых платежей, при включении этого режима, вы пройдете все шаги в платежном терминале Invoicebox, но деньги с вашей карты списаны не будут.
- Адрес эл. почты - если вы не хотите, чтоб пользователи вводили е-меил повторно при настройке укажите "Адрес эл. почты", выбредите "Пользователь" во втором выпадающем списке "Электронный адрес".

## Примечания для упаковки модуля для разных версий

Данная версия предназначена для публикации в маркетплейсе Битрикс. Все файлы закодированы в кодировке 1251. Данная версия так же установится в ручную на версию Битрикс 1251.

Для создания пакета ручной установки для Битрикса UTF-8 необходимо все файлы перекодировать UTF-8 без BOM.

### Настройка панели ИнвойсБокс:

1. Для настройки панели управления ИнвойсБокс пройдите по url - https://login.invoicebox.ru/ ;
2. Авторизуйтесь и пройдите в раздел "Мои магазины". "Начало работы" -> "Настройки" -> "Мои магазины";
3. Пройдите по вкладку "Уведомления по протоколу" -> выберите "Тип уведомления" "Оплата/HTTP/Post (HTTP POST запрос с данными оплаты в переменных)"
4. В поле "URL уведомления" укажите:

   <домен_сайта>/local/components/invoicebox/index.php

5. Сохраните изменения.
