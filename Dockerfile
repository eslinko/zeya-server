#syntax=docker/dockerfile:1.4

FROM yiisoftware/yii2-php:8.1-apache

ARG APP_INIT_ENV=Production
ENV APP_INIT_ENV $APP_INIT_ENV

ENV YII_DEBUG=false
ENV YII_ENV=prod

ENV MNT_DIR /app/web/backend/web/uploads
ENV DB_PORT 3306

# Install system dependencies
RUN apt-get update -y && apt-get install -y \
    netcat-openbsd \
    nfs-common \
    && apt-get clean

WORKDIR /app/web

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer/composer:2-bin /composer /usr/bin/composer

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* ./

RUN set -eux; \
    if [ "$APP_INIT_ENV" = "Production" ]; then export ARGS="--no-dev"; fi

RUN set -eux; \
	composer install --prefer-dist --no-autoloader --no-scripts --no-progress ${ARGS:=""}; \
	composer clear-cache

# copy sources
COPY . ./
RUN chmod +x /app/web/docker-entrypoint.sh

RUN rm -Rf .docker/

RUN set -eux; \
	composer dump-autoload --classmap-authoritative ${ARGS:=""}; \
    php ./init --env=${APP_INIT_ENV} --overwrite=a

RUN mv "/usr/local/etc/php/php.ini-production" "/usr/local/etc/php/php.ini"
COPY .docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY .docker/apache/ports.conf /etc/apache2/ports.conf

COPY .docker/php/php.ini /usr/local/etc/php/conf.d/user.ini

EXPOSE 8080

ENTRYPOINT ["/app/web/docker-entrypoint.sh"]

CMD ["apache2", "-D", "FOREGROUND"]