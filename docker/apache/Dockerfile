FROM php:7.3-fpm
 
RUN docker-php-ext-install mysqli

# FROM php:7.2.2-apache
# RUN apt-get update && apt-get install -y --no-install-recommends \
#     libmagickwand-dev \
#     && rm -rf /var/lib/apt/lists/* \
#     && pecl install imagick-3.4.3 \
#     && docker-php-ext-enable imagick \
#     && docker-php-ext-install mysqli \
#     && docker-php-ext-enable mysqli \
#     && a2enmod rewrite 
#COPY ./app/ /var/www/html/
#EXPOSE 80