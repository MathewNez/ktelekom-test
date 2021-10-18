# How to run this site?

1. Install dependencies:
```shell
$ sudo apt install mysql-server apache2
```
2. Clone this project
```shell
$ git clone <url>
```
3. Copy files from `site/` directory to `/var/www/html/`

(only files from folder, without the folder itself)
```shell
$ sudo cp -r site/. /var/www/html/
```
4. Set the default page as `form.php` by editing `/etc/apache2/sites-available/000-default.conf` configuration file:
```shell
$ sudo nano /etc/apache2/sites-available/000-default.conf
```
Before the line that contains the `DocumentRoot` option add this line:
```shell
DirectoryIndex form.php
```
5. Get a database and copy it to the `/var/lib/mysql`
```shell
$ sudo cp -r /path/to/db/dbname /var/lib/mysql
```
Where `/path/to/db` is a path to a database and `dbname` is a database name.

I named database "hardware".

5. Create a `db.ini` file somewhere outside the server root folder (outside the `/var/www/html/`).

6.The `db.ini` structure should be like this:
```
username=root
password=pass
db=hardware
```
These credentials will be used to connect to a database.

7. In `/var/www/html/form.php` find this:
```php
$config = parse_ini_file('/home/mathew/Documents/work/ktelekom_test_task/ktelekom-test/db.ini');
```
And replace this filename to `/path/to/dbini/db.ini`, 

where `/path/to/dbini` is a path to a place where tou store your `db.ini`.

8. Start apache2 service:
```shell
$ sudo systemctl start apache2
```
You may check its condition by
```shell
$ systemctl status apache2
```
9. Start mysql service
```shell
$ sudo systemctl start mysql
```
You may check its condition by
```shell
$ systemctl status mysql
```
### Congrats! You made it. You can now find site at [127.0.0.1](http://127.0.0.1)

# Plans for the future:
* Refactor code
* Make message about successful adding a record to a db possible to see after sending form
* Make form beautiful via bootstrap
