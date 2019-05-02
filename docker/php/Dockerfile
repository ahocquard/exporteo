FROM akeneo/php:7.3

COPY ./async.so /usr/lib/php/20180731/

RUN echo extension=async.so > /etc/php/7.3/cli/conf.d/50_async.ini
