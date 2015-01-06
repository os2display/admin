#Indholdskanalen backend

##Information
This project consists of the backend for Indholdskanalen. The backend is a symfony project.

###Helpful commands
We have defined a couple of commands for Indholdskanalen.

To push content
<pre>
php app/console ik:push
</pre>

To reindex search
<pre>
php app/console ik:reindex
</pre>
This does not include delete of records that are removed from symfony but not search.

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

### Copy example.configuration.js to configuration.js
Copy example.configuration.js to configuration.js in the directory (web/js/)and change the relevant settings.

For a vagrant set to: 'http://service.indholdskanalen.vm:3001'

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
app/console sonata:easy-extends:generate --dest=src SonataUserBundle
app/console sonata:easy-extends:generate --dest=src SonataMediaBundle
</pre>

<pre>
$ cp app/config/parameters.yml.dist app/config/parameters.yml
</pre>

Fill in relevant settings.

###Setup DB
To generate the relevant tables in the database, use the following command:
<pre>
$ php app/console doctrine:schema:update --force
</pre>
(--force) is not recommended for a production server, only first setup.

###Setup webserver
See the following link for different server setup (we use nginx):
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

###Set memory size
Increase the memory size in php.ini to at least 256 mb to handle image/media uploads (/etc/php5/fpm/php.ini).

###File sizes
Increase client_max_body_size in the nginx configuration.

Increase file sizes to handle larger files in php.ini:
<pre>
;The maximum size of an uploaded file.
upload_max_filesize = 256M

;Sets max size of post data allowed. This setting also affects file upload. To upload large files, this value must be larger than upload_max_filesize
post_max_size = 300M
</pre>

###Add mime type to nginx configuration
Firefox needs the ogg mime type added to the nginx configuration to be able to handle video files.
Add the following to the nginx configuration:
<pre>
  include /etc/nginx/mime.types;
  types {
    video/ogg ogg;
  }
</pre>

###Vagrant setup for video with Zencoder
For Video for Zencoder to work in your vagrant you need a public URL:
<pre>
vagrant share
</pre>
Vagrant Share is a Vagrant Cloud feature which requires an account. Create this at [www.vagrantcloud.com](http://www.vagrantcloud.com)

This URL must be setup in app/config/parameters.yml and Nginx virtual host: /etc/nginx/sites-enabled/service.indholdskanalen.vm.conf

Example of paramaters.yml:
<pre>
zencoder_api: 1234567890
</pre>

Example of line replacement in service.indholdskanalen.vm.conf (in /var/nginx/sites-enabled):
<pre>
server_name service.indholdskanalen.vm slight-gopher-8311.vagrantshare.com;
</pre>

To be able to use ZenCoder with a vagrant setup for ssl, the following hacks have to be applied to the service.indholdskanalen.vm.conf:
<pre>
upstream nodejs_search {
  server 127.0.0.1:3010;
}

#server {
#  listen 80;

#  server_name service.indholdskanalen.vm;
#  root /var/www/backend/web;

#  rewrite ^ https://$server_name$request_uri? permanent;

#  access_log /var/log/nginx/backend_access.log;
#  error_log /var/log/nginx/backend_error.log;
#}


# HTTPS server
#
server {
#  listen 443;
  listen 80;

  server_name slight-gopher-8311.vagrantshare.com;
#  server_name service.indholdskanalen.vm;

...

#  ssl on;
  ssl off;

...
</pre>
And restart nginx:
service nginx restart

Also
change the absolute_path_to_server parameter in app/config/parameters.yml to slight-gopher-8311.vagrantshare.com.
change web/js/configuration.js - address from 'https://' to "address": 'http://

With these changes it is possible to get ZenCoder to work.

Access the site through slight-gopher-8311.vagrantshare.com, upload the media.

After this revert to the setup from before.

###Setup CRONTAB for updates
To setup the pushcontent as a crontab. On the server:

<pre>
$ crontab -e
</pre>

Add the following line:

<pre>
*/1 * * * * php path_to_backend/app/console ik:cron
</pre>

This command will fetch the latest content from the providers and push to the screens if necessary.

Disable the push-on-changes feature in the src/Indholdskanalen/MainBundle/Resources/config/services.yml by commenting out the middleware listener.

<pre>
#  indholdskanalen_middleware.listener:
#    class: Indholdskanalen\MainBundle\EventListener\MiddlewareListener
#    arguments: [@indholdskanalen.middleware.communication]
#    tags:
#      - { name: doctrine.event_listener, event: postUpdate }
</pre>

###Templates
The templates are placed in the web/ik-templates/ directory.
To enable a template from the templates folder, add the name to parameters.yml, e.g.:

<pre>
  templates_enabled:
    - ik-iframe
    - manual-calender
    - only-image
</pre>


###Logo
To set a custom logo: 

1. Add logo.png to web/images/logo.png

2. Change the logo parameter in parameters.yml to 

<pre>
  logo: images/logo.png
</pre>


###Ready to go!
