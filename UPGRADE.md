# Creating os2display symfony 3.4 installation:

Install symfony 3.4
<pre>
symfony new admin 3.4
</pre>

Add repositories to composer.json
<pre>
    "repositories": {
        "os2display/media-bundle": {
            "type": "vcs",
            "url": "https://github.com/itk-os2display/media-bundle"
        },
        "os2display/core-bundle": {
            "type": "vcs",
            "url": "https://github.com/itk-os2display/core-bundle"
        },
        "os2display/admin-bundle": {
            "type": "vcs",
            "url": "https://github.com/itk-os2display/admin-bundle"
        },
        "os2display/default-template-bundle": {
            "type": "vcs",
            "url": "https://github.com/itk-os2display/default-template-bundle"
        }
    },
</pre>

Require os2display bundles
<pre>
php -d memory_limit=-1 /usr/local/bin/composer require os2display/admin-bundle os2display/core-bundle os2display/media-bundle os2display/default-template-bundle -vvv
</pre>


## Notes: Steps to upgrade to symfony 3.4

<pre>
mv admin adminOLD
symfony new admin 3.4

cp -R adminOLD/.git admin/

# Add repositories to composer.json

php -d memory_limit=-1 /usr/local/bin/composer require os2display/admin-bundle:dev-symf34 os2display/core-bundle:dev-symf34 os2display/media-bundle:dev-symf34 os2display/default-template-bundle -vvv

</pre>


Require bundles for core-bundle
<pre>
php -d memory_limit=-1 /usr/local/bin/composer require debril/rss-atom-bundle doctrine/doctrine-migrations-bundle friendsofsymfony/rest-bundle friendsofsymfony/user-bundle guzzlehttp/guzzle jms/job-queue-bundle jms/serializer-bundle nelmio/api-doc-bundle
php -d memory_limit=-1 /usr/local/bin/composer require itk-os2display/aarhus-data-bundle itk-os2display/aarhus-second-template-bundle aakb/os2display-aarhus-templates itk-os2display/template-extension-bundle itk-os2display/lokalcenter-template-bundle itk-os2display/vimeo-bundle itk-os2display/campaign-bundle
</pre>

Add to AppKernel.php

Add config.yml from old
Copy security.yml from old

Move requirements to os2display/core-bundle requirements

