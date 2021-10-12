# Как это запустить? (я знаю, что это тупо выглядит, но потом поправлю)

1. Скачайте apache2
2. Склонируйте проект ( `git clone <url>` )
3. Поменяйте пару строк в паре конфигурационных файлов apache2:

```
$ sudo nano /etc/apache2/apache2.conf
```
Тут нужно найти что-то похожее на :
```
<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
</Directory>
```
Главное, чтоб было `/var/www`

И измените этот фрагмент вот так:
```
<Directory /media/myserver/ktelekom-test/site>
            Options Indexes FollowSymLinks
            AllowOverride None
            Require all granted
    </Directory>
```
где `/media/myserver` - это папка, куда вы склонировали проект.

Ctrl+O Enter Ctrl+X

Меняем второй конфигурационный файл:

```
$ sudo nano /etc/apache2/sites-available/000-default.conf
```
Здесь нужно поменять опцию `DocumentRoot` на `/media/myserver/ktelekom-test/site`
Где `/media/myserver` - папка, в которую вы склонировали проект.
Ctrl+O Enter Ctrl+X

После изменения файлов конфигурации необходимо перезапустить apache2:
```
$ sudo systemctl restart apache2
```

В том, что apache2 был перезапущен и работает, можно убедиться, прописав
```
$ sudo systemctl status apache2
```

Сайт можно найти на 127.0.0.1

Доступные на данный момент страницы:
* 127.0.0.1 - выведется тестовая страница, автоматически сгенерированная apache2
* 127.0.0.1/info.php - выведется информация о клиенте (какой браузер, система)
* 127.0.0.1/form.html - та самая ФОРМА ИЗ ТЕСТОВОГО ЗАДАНИЯ
* 127.0.0.1/action.php - откроется после отправки формы

# Фронт работ:
* Прикрутить mysql
* Сделать форму красивой с помощью bootstrap (а может, и не bootstrap)