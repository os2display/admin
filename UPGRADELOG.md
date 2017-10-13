# UPGRADELOG

## v4.x => 5.0.0

The major change in the this release is that all code has been removed from the
src/ and web/ folders.

The goal of this maneuver is to make os2display more extensible, especially with
regards to new templates and tools for templates.

The web files should be placed in bundles' Resources/public/ folder. These will
be symlinked during "composer install" to web/bundles/ folder.

### Changes

* All code in web (administration and templates) has been moved to bundles that
  get installed by composer.
* All bundles in src/ have been moved to seperate bundles. These will be imported
  with composer now:

  - os2display/core-bundle: Contains entities, API and services.
  - os2display/admin-bundle: Contains the administration.
  - os2display/default-template-bundle: Contains default templates and tools.

* As a result the os2display/admin (5.0.0) project contains no code, but only
  configuration.

### Upgrading

* Requires PHP 5.6 now.
* Create a bundle with a structure like src/Os2Display/DefaultTemplateBundle
  to contain all custom code and templates. If a bundle is relevant for
  os2display, think about contributing it back to `github.com/os2display`.
* All commands have changed name to start with os2display: instead of ik:. This especially can affect cron execution where the name of the cron command has changed to "os2display:core:cron". 
* Create a bundle with a structure like src/Os2Display/DefaultTemplateBundle.
* Update [Bundlename]Extension.php to extend Os2DisplayBaseExtension.php like
  Os2DisplayCoreExtension.
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
* All slide tools should be made into angular directives like in 
  `Os2Display/DefaultTemplateBundle/Resources/public/apps/toolsModule`.
  New tools should be attached as directives to the "toolsModule". The new tool
  directive should be namespaced with organization to avoid clashes:

```
angular.module('toolsModule').directive('itkColorTool', []);
```

* To inject the tools into the angular administration, modify the bundle's 
  `Resources/config/angular.yml` file with the new additions to toolsModule. 
  See `Os2Display/DefaultTemplateBundle/Resources/config/angular.yml`
  for an example:
  
  ``` 
  assets:
    js_prod:
      - bundles/os2displaydefaulttemplate/assets/build/os2displaydefaulttemplate.min.js
  
  modules:
    toolsModule:
      files:
        - bundles/os2displaydefaulttemplate/apps/toolsModule/background-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/base-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/header-editor-responsive.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/logo-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/manual-calendar-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/rss-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/slideshow-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/slideshow-effects-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/slideshow-order-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/source-editor.js
        - bundles/os2displaydefaulttemplate/apps/toolsModule/text-editor.js
  apps:
  ```
  the assets.js_prod should contain the path to the minified js.
  
  All these angular.yml files are gathered (array_merge_recursive) after a 
  cache:clear and make sure the correct files are bootstrapped when index file
  is loaded (admin-bundle/Resources/views/Main/index.html.twig).
  
* Supply a gulp file to compile the new js files.
* Custom bundles should be imported last in AppKernel.php.
* Templates should be placed in [Bundlename]/Resources/public/templates. See
  DefaultTemplateBundle for an example.
