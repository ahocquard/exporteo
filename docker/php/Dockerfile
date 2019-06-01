FROM akeneo/php:7.3

ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/
RUN echo "@codecasts https://dl.bintray.com/php-alpine/v3.9/php-7.3" >> /etc/apk/repositories

RUN apk add --no-cache \
ca-certificates \
build-base \
autoconf \
bash \
php7-dev@codecasts \
openssl-dev \
libuv-dev \
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

FROM akeneo/php:7.3

COPY --from=build /usr/lib/php7/modules/async.so /usr/lib/php/20180731/
RUN echo extension=/usr/lib/php/20180731/async.so > /etc/php/7.3/cli/conf.d/50-async.ini
