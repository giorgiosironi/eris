ARG BASE_IMAGE=php:8.1-cli-alpine
FROM ${BASE_IMAGE}

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN apk add --no-cache python3 git

RUN addgroup -g 1000 appuser && \
    adduser -D -u 1000 -G appuser -h /home/appuser appuser

USER 1000:1000
