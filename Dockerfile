FROM ghcr.io/roadrunner-server/roadrunner:2024 as roadrunner
FROM php:8.4-cli-bookworm

# https://github.com/mlocati/docker-php-extension-installer
# https://github.com/docker-library/docs/tree/0fbef0e8b8c403f581b794030f9180a68935af9d/php#how-to-install-more-php-extensions
RUN --mount=type=bind,from=mlocati/php-extension-installer:2,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions \
     install-php-extensions @composer-2 opcache zip intl sockets protobuf \
	 pdo pdo_mysql bcmath mbstring fileinfo redis

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

# Set working directory
WORKDIR /var/www/html/sunset/user_management_service

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY . .

RUN composer install --optimize-autoloader --no-dev

CMD rr serve -c .rr.yaml