ARG BASE_IMAGE=php:8.1-cli-alpine
FROM ${BASE_IMAGE}

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN apk add --no-cache python3
