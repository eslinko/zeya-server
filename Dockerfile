# syntax=docker/dockerfile:1

FROM yiisoftware/yii2-php:8.1-apache

ENV YII_DEBUG=false
ENV YII_ENV=prod

WORKDIR /app/web

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer/composer:2-bin --link /composer /usr/bin/composer

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* ./
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
	composer clear-cache

# copy sources
COPY --link . .
RUN rm -Rf .docker/

RUN set -eux; \
	composer dump-autoload --classmap-authoritative --no-dev; \
    php ./init --env=Production --overwrite=a

RUN mv "/usr/local/etc/php/php.ini-production" "/usr/local/etc/php/php.ini"
COPY --link .docker/php/php.ini /usr/local/etc/php/conf.d/user.ini