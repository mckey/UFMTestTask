FROM php:8.0.10-fpm-alpine3.14

# package delete/update/install #
RUN apk update
RUN apk upgrade
RUN apk add \
    nano \
    git \
    curl \
    libxml2-dev \
    sqlite-dev \
    curl-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libmcrypt-dev \
    libzip-dev \
    icu-dev \
    libressl-dev \
    ca-certificates \
    mysql-client \
    gcc glib make glib-dev g++ autoconf

# Configure PHP extensions #
RUN docker-php-ext-configure zip && \
    docker-php-ext-configure opcache && \
    docker-php-ext-configure pdo_mysql && \
    docker-php-ext-configure mysqli && \
    docker-php-ext-configure bcmath && \
    docker-php-ext-configure exif && \
    docker-php-ext-configure gd && \
    docker-php-ext-configure pcntl

# Build and install PHP extensions #
RUN docker-php-ext-install \
    zip \
    opcache \
    pdo_mysql \
    mysqli \
    bcmath \
    exif \
    gd \
    pcntl

# php-imagemagick #
RUN set -xe \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && apk add --no-cache bash imagemagick-dev \
    && git clone https://github.com/Imagick/imagick \
    && cd imagick \
    && git checkout master && git pull \
    && sed -i 's/@PACKAGE_VERSION@/3.5.1/g' php_imagick.h \
    && phpize && ./configure && make && make install \
    && cd .. && rm -Rf imagick \
    && docker-php-ext-enable imagick \
    && apk del .build-deps \
    && rm -rf /tmp/* /var/cache/apk/*

# conf #
RUN mkdir /var/tmp/php && mkdir /var/tmp/php/opcache
# COPY conf/php/php-fpm.conf /usr/local/etc/php-fpm.conf
# COPY conf/php/www.conf /usr/local/etc/php-fpm.d/www.conf
# COPY conf/php/php.ini /usr/local/etc/php/php.ini

# cleanup #
RUN update-ca-certificates
RUN rm -rf /tmp/* && \
    rm -rf /var/cache/apk/*

# Override stop signal to stop process gracefully
# https://github.com/php/php-src/blob/17baa87faddc2550def3ae7314236826bc1b1398/sapi/fpm/php-fpm.8.in#L163
STOPSIGNAL SIGQUIT

EXPOSE 9000
CMD ["php-fpm"]
