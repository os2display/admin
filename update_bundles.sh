#!/usr/bin/env bash
bold=$(tput bold)
normal=$(tput sgr0)

composer="$(which composer)"
php -d memory_limit=-1 $composer update symfony/symfony os2display/core-bundle os2display/admin-bundle os2display/default-template-bundle os2display/media-bundle os2display/campaign-bundle
