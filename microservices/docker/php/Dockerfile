FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libaio1 \
    libzip-dev \
    && docker-php-ext-install \
       pdo \
       zip

# -------------------------------------------------
# Oracle Instant Client
# -------------------------------------------------

WORKDIR /opt/oracle

RUN curl -o instantclient-basic.zip \
    https://download.oracle.com/otn_software/linux/instantclient/2380000/instantclient-basic-linux.x64-23.8.0.25.04.zip \
    && curl -o instantclient-sdk.zip \
    https://download.oracle.com/otn_software/linux/instantclient/2380000/instantclient-sdk-linux.x64-23.8.0.25.04.zip \
    && unzip instantclient-basic.zip \
    && unzip instantclient-sdk.zip \
    && rm instantclient-basic.zip instantclient-sdk.zip \
    && ln -s /opt/oracle/instantclient_23_8 /opt/oracle/instantclient

RUN echo /opt/oracle/instantclient > /etc/ld.so.conf.d/oracle.conf \
    && ldconfig

RUN echo "instantclient,/opt/oracle/instantclient" | pecl install oci8 \
    && docker-php-ext-enable oci8

# -------------------------------------------------
# Composer
# -------------------------------------------------

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]