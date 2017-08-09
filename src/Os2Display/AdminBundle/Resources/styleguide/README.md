# OS2Display Styleguide

OS2Display styleguide using [Pattern Lab](http://patternlab.io/).

#### Install Pattern Lab

Prerequisites: [git](https://git-scm.com/) and [Composer](https://getcomposer.org/) installed.

In the `styleguide`directory run:

```sh
composer create-project pattern-lab/edition-twig-standard pattern-lab
```

When prompted for suggested starterkit choose:

```
1: pattern-lab/starterkit-twig-base
```

In the file `pattern-lab/config/config.yml` change sourceDir to:

```json
sourceDir: ../source
```

and change publicDir to:

```json
sourceDir: ../public
```

If everything went well you should now be able to generate the static Pattern Lab site. In the `pattern-lab` directory run:

```sh
php core/console --generate
```

#### Start the server

To start the server, in the `pattern-lab` directory run:

```sh
php core/console --server
```

#### Watch for Changes and Reload

Pattern Lab can watch for changes to files in the `source` folder and automatically rebuild the entire Pattern Lab
website for you. Make your changes, save the file, and Pattern Lab takes care of the rest.

Install the Auto-Reload Plugin:

```sh
composer require pattern-lab/plugin-reload
```

Run the server with watch and auto reload:

```sh
php core/console --server --with-watch
```