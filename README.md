#Indholdskanalen backend

##Information
This project consists of the backend for Indholdskanalen. The backend is a symfony project.

###Helpful commands
To clear cache
<pre>
php app/console cache:clear
</pre>

To generate a bundle
<pre>
php app/console generate:bundle
</pre>

To make a super-user
<pre>
$ php app/console fos:user:create [admin_username] [test@example.com] [p@ssword] --super-admin
</pre>


##Installation instructions
###Get composer
With brew (global install)
<pre>
$ brew install composer
</pre>

Without brew. Go to project directory:

<pre>
$ curl -sS http://getcomposer.org/installer | php
</pre>

This will download composer.phar to the project directory.

###Install dependencies for project
With brew:
<pre>
$ composer install
</pre>

Without brew:
<pre>
$ php composer.phar install
</pre>

If there are problems with this, try with apc.enable_cli = Off in php.ini or from the cli:
<pre>
php -d apc.enable_cli=Off composer.phar install
</pre>

###Setup project
If you use composer install this step should be covered.

<pre>
$ cp app/config/parameters.yml.dist app/config/parameters.yml
</pre>

Fill in relevant settings.

###Setup DB and after changing an entity
<pre>
$ php app/console doctrine:schema:update --force
</pre>
(--force) is not for production server, only first setup.

###Set up webserver
<pre>
http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html
</pre>

###Check system configuration
Check that local system is properly configured for Symfony.

<pre>
$ php app/check.php
</pre>

> Look for ERROR or WARNING and correct these.

Check config link in browser to run check requirements for the web server.

<pre>
http://[path_to_project/app/web]/config.php
</pre>

Fix problems.

###Ready to go!
