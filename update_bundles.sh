#!/usr/bin/env bash

###############################
# Updates bundles and symfony #
###############################

bold=$(tput bold)
normal=$(tput sgr0)

php -d memory_limit=-1 $(which composer) update symfony/symfony twig/twig os2display/core-bundle os2display/admin-bundle os2display/default-template-bundle os2display/media-bundle os2display/campaign-bundle os2display/screen-bundle
