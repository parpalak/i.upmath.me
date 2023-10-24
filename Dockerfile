FROM debian:bookworm-slim
WORKDIR /var/www/i.upmath.me

RUN apt-get update && apt-get -y install \
    texlive \
    nginx-extras \
    php8.2-fpm \
    php-curl \
    php-xml \
    npm \
    composer \
    ghostscript \
    librsvg2-bin \
    optipng \
    supervisor

RUN npm install -g yarn && \
    npm install -g composer && \
    npm install -g bower && \
    npm install -g grunt-cli

COPY . .
RUN mkdir -p logs
RUN yarn install
RUN composer install
RUN bower install
RUN grunt

RUN mkdir -p /var/run/php-fpm/

RUN cp config.php.dist config.php
RUN cp docker/nginx.conf /etc/nginx/nginx.conf
RUN cp docker/www.conf /etc/php/8.2/fpm/pool.d/www.conf && \
    cp docker/www-tex.conf /etc/php/8.2/fpm/pool.d/www-tex.conf

RUN cp docker/superv.conf /etc/superv.conf

ENTRYPOINT [ "/var/www/i.upmath.me/docker/entrypoint.sh" ]

