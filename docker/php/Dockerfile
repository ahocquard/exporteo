FROM alpine:3.9 as build

ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/
RUN echo "@codecasts https://dl.bintray.com/php-alpine/v3.9/php-7.3" >> /etc/apk/repositories

RUN apk add --no-cache \
    ca-certificates \
    build-base \
    autoconf \
    bash \
    php7-dev@codecasts \
    php7-openssl@codecasts \
    openssl-dev \
    libuv-dev \
    libevent-dev \
    curl

RUN ln -s /usr/bin/php7 /usr/bin/php && \
    ln -s /usr/bin/phpize7 /usr/bin/phpize && \
    ln -s /usr/bin/php-config7 /usr/bin/php-config

RUN curl -sSL https://github.com/concurrent-php/ext-async/archive/master.tar.gz \
    | tar xz && \
    cd ext-async-master && \
    phpize && \
    bash ./configure && \
make install

FROM alpine:3.9

ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/
RUN echo "@codecasts https://dl.bintray.com/php-alpine/v3.9/php-7.3" >> /etc/apk/repositories

RUN apk add --no-cache \
    ca-certificates \
    php7@codecasts \
    php7-dom@codecasts \
    php7-ctype@codecasts \
    php7-json@codecasts \
    php7-mbstring@codecasts \
    php7-openssl@codecasts \
    php7-posix@codecasts \
    php7-sockets@codecasts \
    php7-curl@codecasts \
    php7-pcntl@codecasts \
    php7-iconv@codecasts \
    php7-phar@codecasts \
    libevent-dev \
    openssl-dev \
    libuv \
    composer

COPY --from=build /usr/lib/php7/modules/async.so /usr/lib/php7/modules/async.so
RUN echo extension=async.so > /etc/php7/conf.d/50_async.ini
RUN ln -s /usr/bin/php7 /usr/bin/php

WORKDIR /usr/src/app