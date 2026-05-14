FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
		freetype-dev \
		libjpeg-turbo-dev \
		libpng-dev \
		oniguruma-dev \
		bash

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-camagru.ini

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/camagru-entrypoint
RUN chmod +x /usr/local/bin/camagru-entrypoint

ENTRYPOINT ["camagru-entrypoint"]
CMD ["php-fpm"]
