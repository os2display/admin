# os2display administrator interface

For general information about installation see the https://github.com/os2display/docs/blob/development/Installation%20guide.md in the docs repository.

# Information
When working with os2display together with the vagrant provide. You have to visit screen.os2display.vm, search.os2display.vm, middleware.os2display.vm, admin.os2display.vm and accept the self-sign certificates. If you don't open a tab for each in Chrome, if not it will not work.

# Gulp
To build the js for production and the sass we use gulp.

### NB! if you add a js file or js assets file, add it to the gulpfile.js and to /src/Indholdskanalen/MainBundle/Resources/views/Main/index.html.twig

Run gulp help to see list of commands.

To compile the js asset files: gulp assets

To compile ikApp js files: gulp js

To compile sass: gulp sass

To compile the templates: gulp sassTemplates

# Helpful commands
We have defined a couple of commands for os2display.

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

To brute force clear cache
<pre>
rm -rf app/cache/*
</pre>


# Acceptance tests

## Prerequisites for running UI acceptance tests

The UI acceptance tests use [Selenium WebDriver](http://www.seleniumhq.org/projects/webdriver/), and at the time of writing, we need Selenium Standalone Server 2.53 and Firefox 46 to be able to run the tests.

Install Firefox 46.0.1 for testing (on macOS):

```
mkdir -p ~/.firefox/46.0.1
cd ~/.firefox/46.0.1
curl https://ftp.mozilla.org/pub/firefox/releases/46.0.1/mac/en-US/Firefox%2046.0.1.dmg > Firefox.dmg
hdiutil mount Firefox.dmg
cp -Rv /Volumes/Firefox/Firefox.app .
hdiutil unmount /Volumes/Firefox
rm Firefox.dmg
cd -
```

Download Selenium server:

```
curl --output /tmp/selenium-server-standalone.jar http://selenium-release.storage.googleapis.com/2.53/selenium-server-standalone-2.53.1.jar
```

Start Selenium using Firefox 46.0.1

```
java -Dwebdriver.firefox.bin="$HOME/.firefox/46.0.1/Firefox.app/Contents/MacOS/firefox" -jar /tmp/selenium-server-standalone.jar                                                                                                      ⏎ feature/behat ✱ ◼
```

## Running acceptance tests

Clear out the acceptance test cache and set up the database:

```
app/console --env=acceptance cache:clear
app/console --env=acceptance doctrine:database:create
```

Now we can run the tests:

```
./vendor/behat/behat/bin/behat
```
