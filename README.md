Bach
====

First of all, copy `app/config/parameters.yml.dist` to 
`app/config/parameters.yml` and adapt database configuration in that new file.

Then, check your PHP parameters and modules, running:
$ php app/check.php

You'll then have to install vendors dependencies.

In a terminal, run the following command to retrieve composer:
$ curl -s https://getcomposer.org/installer | php

Ask composer to download and install all dependencies:
$ php -d date.timezone=UTC composer.phar install

Create database if it doe not exists yet:
$ php app/console doctrine:database:create

Create database schema:
$ php app/console doctrine:schema:update --force

Give your apache user write access on relevant directories:
$ sudo chown -R apache:apache app/cache app/logs

Assuming your DocumentRoot is set to the web directory of Bach,
point your browser to: http://bach.host/config.php

That page will display all problems and some recommendations you should
follow in order to optimize Bach runtime.

Finally, enable apache rewrite module and put the following lines in
your virtual host configuration (or in bach/web/.htaccess):
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ app.php [QSA,L]
RewriteRule ^/bachdev/(.*) /$1 [PT]


You're now ready to go :)

Just put your browser to the location you've installed
Bach, and enjoy!
