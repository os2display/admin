#!/bin/sh
# Initscript for kubernetes.
# When used in k8 this container is added as an init-container and is expected
# copy the embedded release-source into release directory and fix any
# necessary permissions.

if [ ! -z "$(ls -A /release)" ]; then
   echo "Release-dir is not empty, exiting"
   exit 1
fi

start=`date +%s`
if [[ -f var/www/admin/.release ]]; then
  echo "Release data"
  echo "------------"
  cat /var/www/admin/.release
  echo
fi

echo "Copying release into /release"
cp -r /var/www/admin /release/
mkdir -p /release/admin/var
chown -R www-data:www-data /release/admin/var
end=`date +%s`
runtime=$((end-start))
echo "Release copy completed in ${runtime} seconds"
