FROM php:7.4-apache

RUN a2enmod rewrite

ENV DEBIAN_FRONTEND noninteractive
RUN apt-get -qq update && apt-get -qq -y upgrade
RUN apt-get -qq update && apt-get -qq -y --no-install-recommends install \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libjpeg-dev \
    libmemcached-dev \
    zlib1g-dev \
    imagemagick \
    libmagickwand-dev \
    wget \
    ghostscript \
    ffmpeg

# Install the PHP extensions we need
RUN docker-php-ext-install -j$(nproc) iconv pdo pdo_mysql mysqli

# GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j "$(nproc)" gd

RUN usermod -u 10000 www-data
RUN wget --no-verbose "https://github.com/omeka/omeka-s/releases/download/v2.1.2/omeka-s-2.1.2.zip" -O /var/www/omeka-s.zip
RUN unzip -q /var/www/omeka-s.zip -d /var/www/ \
&&  rm /var/www/omeka-s.zip \
&&  rm -rf /var/www/html/ \
&&  mv /var/www/omeka-s /var/www/html/ \
&&  chown -R www-data:www-data /var/www/html/

ADD php.ini-development /usr/local/etc/php

COPY extra.ini /usr/local/etc/php/conf.d/

VOLUME /var/www/html/

CMD ["apache2-foreground"]
