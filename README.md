Bach
====

Requirements
------------

In order to run Bach, you have to ensure requirements are met:

* PHP version 5.3 or above (5.4 recommended for better performances)
* One of the PostgreSQL or MySQL database server,
* an available Solr 4.1 instance,
* apache httpd server with mod_rewrite enabled

Installation
------------

First of all, copy `app/config/parameters.yml.dist` to 
`app/config/parameters.yml` and adapt database configuration in that new file.

Then, check your PHP parameters and modules, running::

    $ php app/check.php

You'll then have to install vendors dependencies.

In a terminal, run the following command to retrieve composer::

    $ curl -s https://getcomposer.org/installer | php

Ask composer to download and install all dependencies::

    $ php -d date.timezone=UTC composer.phar install

/!\ Solarium bug /!\
As for now, a "bug" has been spotted in solarium regarding to date range
facetting. A patch has been provided and should be included in a later
release, but while it is not, we have to apply patch manually after having
installed dependencies via composer. Patch is available at:
https://github.com/basdenooijer/solarium/issues/240

Create database if it doe not exists yet::

    $ php app/console doctrine:database:create

Create database schema::

    $ php app/console doctrine:schema:update --force

Give your apache user write access on relevant directories::

    $ sudo chown -R apache:apache app/cache app/logs

Assuming your DocumentRoot is set to the web directory of Bach,
point your browser to: `http://bach.localhost/config.php
 <http://bach.localhost/config.php>`_

That page will display all problems and some recommendations you should
follow in order to optimize Bach runtime.

Finally, enable apache rewrite module and put the following lines in
your virtual host configuration (or in `bach/web/.htaccess`)::

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ app.php [QSA,L]

You're now ready to go :)

Just put your browser to the location you've installed
Bach, and enjoy!

From Git
--------

If you're installing from Gti repository, you'll have to run some extra commands:

* generate compiled language files, running :
  $ php app/console gettext:combine en_US,fr_FR
* write asset files:
  $ php app/console assetic:dump --env=prod --no-debug
