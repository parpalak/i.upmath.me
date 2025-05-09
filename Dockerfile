FROM ghcr.io/parpalak/upmath-texlive-docker:2023.0

EXPOSE 80

WORKDIR /var/www/i.upmath.me

RUN apt-get update && apt-get -y --no-install-recommends install \
    nginx-extras lua-zlib \
    zip unzip \
    php8.2-fpm \
    php8.2-curl \
    php8.2-xml \
    php8.2-gd \
    composer \
    librsvg2-bin \
    optipng \
    supervisor \
    curl gnupg && \
    mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg && \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_20.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list && \
    apt-get update && \
    apt-get install -y nodejs && \
    apt-get remove -y curl gnupg && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm -rf /var/cache/apt/

COPY . .

RUN mkdir -p logs
RUN composer install --no-dev

RUN npm install -g yarn grunt-cli && \
    yarn install && \
    grunt && \
    yarn install --prod && \
    npm uninstall -g yarn grunt-cli

RUN mkdir -p /var/run/php-fpm/

RUN cp config.php.dist config.php  \
    && tlversion=$(cat /usr/local/texlive/20*/release-texlive.txt | head -n 1 | awk '{ print $5 }') \
    && sed -i "s/\${tlversion}/${tlversion}/g" config.php
RUN cp docker/nginx.conf /etc/nginx/nginx.conf
RUN cp docker/www.conf /etc/php/8.2/fpm/pool.d/www.conf && \
    cp docker/www-tex.conf /etc/php/8.2/fpm/pool.d/www-tex.conf

RUN cp docker/superv.conf /etc/superv.conf

ENTRYPOINT [ "/var/www/i.upmath.me/docker/entrypoint.sh" ]
