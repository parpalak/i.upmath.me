FROM debian:bookworm-slim

EXPOSE 80

WORKDIR /var/www/i.upmath.me

RUN apt-get update && apt-get -y install \
    texlive-full \
    nginx-extras \
    php8.2-fpm \
    php8.2-curl \
    php8.2-xml \
    composer \
    ghostscript \
    librsvg2-bin \
    optipng \
    supervisor \
    curl gnupg && \
    mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list && \
    apt update -y && \
    apt install nodejs -y && \
    apt remove -y curl gnupg && \
    apt autoremove -y && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN npm install -g yarn && \
    npm install -g composer && \
    npm install -g bower && \
    npm install -g grunt-cli

COPY . .
RUN mkdir -p logs
RUN yarn install
RUN composer install --no-dev
RUN bower install
RUN grunt

RUN mkdir -p /var/run/php-fpm/

RUN cp config.php.dist config.php
RUN cp docker/nginx.conf /etc/nginx/nginx.conf
RUN cp docker/www.conf /etc/php/8.2/fpm/pool.d/www.conf && \
    cp docker/www-tex.conf /etc/php/8.2/fpm/pool.d/www-tex.conf

RUN cp docker/superv.conf /etc/superv.conf

ENTRYPOINT [ "/var/www/i.upmath.me/docker/entrypoint.sh" ]
