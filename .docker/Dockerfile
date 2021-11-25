FROM php:7.4-cli-alpine3.14

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer
RUN apk add --no-cache python3 \
  && ln -s /usr/bin/python3 /usr/bin/python
