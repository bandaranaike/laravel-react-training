FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libaio1t64 \
    libzip-dev \
    && ln -s libaio.so.1t64 /usr/lib/x86_64-linux-gnu/libaio.so.1 \
    && docker-php-ext-install \
       pdo \
       zip \
    && rm -rf /var/lib/apt/lists/*

# -------------------------------------------------
# Oracle Instant Client
# -------------------------------------------------

WORKDIR /opt/oracle

RUN curl -o instantclient-basic.zip \
    https://download.oracle.com/otn_software/linux/instantclient/2380000/instantclient-basic-linux.x64-23.8.0.25.04.zip \
    && curl -o instantclient-sdk.zip \
    https://download.oracle.com/otn_software/linux/instantclient/2380000/instantclient-sdk-linux.x64-23.8.0.25.04.zip \
    && unzip instantclient-basic.zip \
    && unzip -o instantclient-sdk.zip \
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

CMD ["composer", "run", "docker-start"]
