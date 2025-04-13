FROM dunglas/frankenphp:1.5.0-php8.4-bookworm

LABEL authors="Cybrarist"

ENV SERVER_NAME=":80"
ENV FRANKENPHP_CONFIG="worker /app/public/index.php"
ENV FRANKEN_HOST="localhost"


RUN apt update && apt install -y supervisor  \
        libbz2-dev \
        libzip-dev \
        libmcrypt-dev \
        libicu-dev \
        gnupg \
        ca-certificates \
        libx11-xcb1 \
        libxcomposite1 \
        libxdamage1 \
        libxrandr2 \
        libatk1.0-0 \
        libnspr4 \
        && apt-get clean



RUN install-php-extensions @composer

RUN docker-php-ext-install   pcntl \
        opcache \
        pdo_mysql \
        pdo \
        bz2 \
        intl \
        bcmath \
        zip


COPY ./docker/base_supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY . /app

WORKDIR /app

EXPOSE 80 443 2019 8080


RUN chmod +x /app/*

ENTRYPOINT ["docker/entrypoint.sh"]
