ARG builder_image=reload/os2display-admin-php

# Source release and init container.
FROM ${builder_image} as builder
ARG revision=master
ARG repository=https://github.com/os2display/admin.git

RUN rm -rf /var/www/admin/

# Fetch minimal history
RUN echo git clone --depth=1 --branch=${revision} ${repository} /var/www/admin
RUN git clone --depth=1 --branch=${revision} ${repository} /var/www/admin
RUN rm -rf /var/www/admin/.git

RUN echo "Build time: $(date -u)" > /var/www/admin/.release && \
    echo "Repository: ${repository}" >> /var/www/admin/.release && \
    echo "Revision: ${revision}" >> /var/www/admin/.release

WORKDIR /var/www/admin
# TODO - do not run as root
RUN cp app/config/parameters.yml.dist app/config/parameters.yml
ENV SYMFONY_ENV="prod"
RUN composer install
RUN rm app/config/parameters.yml
RUN rm -fr /var/www/admin/app/cache/*

# Make the release-image as small as possible.
FROM busybox

RUN mkdir -p /var/www
COPY --chown=root:root --from=builder /var/www/admin /var/www/admin

RUN mkdir -p web/uploads
RUN mkdir -p app/cache
RUN mkdir -p app/logs
RUN chown -R www-data:www-data web/uploads app/cache app/logs
RUN chmod -R g+w web/uploads app/cache app/logs

RUN mkdir /var/www/admin/tools
# We leave tools un-executable and owned by root for good measure.
COPY tools/init-environment.sh /var/www/admin/tools

COPY tools/init-copy-source.sh /opt/
RUN chmod +x /opt/init-copy-source.sh

CMD ["/bin/true"]

