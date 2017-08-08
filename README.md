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


# API tests

Clear out the acceptance test cache and set up the database:

```
app/console --env=acceptance cache:clear
app/console --env=acceptance doctrine:database:create
```

Run API tests:

```
./vendor/behat/behat/bin/behat --suite=api_features
```

Run only tests with a specific tag:

```
./vendor/behat/behat/bin/behat --suite=api_features --tags=group
```

# Extending the angular UI

To extend the UI, create 