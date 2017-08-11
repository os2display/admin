# UPGRADELOG

## v4.x => v5.0.0

The major change in the this release is that all code has been removed from the web/ folder.

The goal of this manuever is to make os2display more extensible, especially with regards to new templates and tools for templates.

The files should be placed in the bundles: Resources/public/ folder. These will be symlinked during "composer install" to web/bundles/ folder.

### Upgrading

* Create a bundle with a structure like src/Os2Display/DefaultTemplateBundle.
* Update [Bundlename]Extension.php to match Os2DisplayDefaultTemplateExtension.php.
* Change nginx setup for admin. Change

```
  location /templates/ {
    add_header 'Access-Control-Allow-Origin' "*";
  }
```

to

```
  location /bundles/ {
    add_header 'Access-Control-Allow-Origin' "*";
  }
```

to allow access to the templates from the Screen.

* Change all template .json files, so the paths match their new location in web/bundles/ .
* All tools should be angularified like in src/Os2Display/DefaultTemplateBundle/Resources/public/apps/toolsModule. New tools should be attached as directives to "toolsModule". The new tool directive should be namespaced with organization to avoid clashes:

```
angular.module('toolsModule').directive('itkColorTool', []);
```

* To inject the tools into the angular administration, modify the bundle's Resources/config/angular.yml file with the new additions to toolsModule. See htdocs/admin/src/Os2Display/DefaultTemplateBundle/Resources/config/angular.yml for an example.
* Supply a gulp file to compile the new js files.

### TODO

* How are the new bundles installed? - Should os2display be a distribution you can extend?
* Create an extension example bundle to copy for new bundles.
* Create master gulp file to compile bundle's js.
