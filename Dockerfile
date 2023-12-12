FROM debian:bookworm-slim

EXPOSE 80

WORKDIR /

COPY "docker/texlive.profile" /texlive.profile

# See also https://github.com/reitzig/texlive-docker
RUN apt-get update && \
  apt-get install -qy --no-install-recommends  \
    wget ca-certificates perl \
    ghostscript \
  && wget https://mirror.ctan.org/systems/texlive/tlnet/install-tl-unx.tar.gz \
  && tar -xzf install-tl-unx.tar.gz \
  && rm install-tl-unx.tar.gz \
  && mv install-tl-* install-tl \
  && tlversion=$(cat install-tl/release-texlive.txt | head -n 1 | awk '{ print $5 }') \
  && mkdir -p /usr/local/texlive/${tlversion}/bin \
  && ( cd install-tl \
         && tlversion=$(cat release-texlive.txt | head -n 1 | awk '{ print $5 }') \
         && sed -i "s/\${tlversion}/${tlversion}/g" /texlive.profile \
         && ./install-tl -profile /texlive.profile \
      ) \
  && rm -rf install-tl \
  && tlmgr version | tail -n 1 > version \
  && echo "Installed on $(date)" >> version \
  && apt-get remove -y wget ca-certificates perl  \
  && apt-get autoremove -y \
  && rm -rf /var/lib/apt/lists/* \
  && rm -rf /var/cache/apt/

WORKDIR /var/www/i.upmath.me

RUN apt-get update && apt-get -y install \
    nginx-extras \
    php8.2-fpm \
    php8.2-curl \
    php8.2-xml \
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

RUN npm install -g yarn && \
    npm install -g bower && \
    npm install -g grunt-cli

COPY . .
RUN mkdir -p logs
RUN composer install --no-dev
RUN yarn install
RUN bower install
RUN grunt

RUN mkdir -p /var/run/php-fpm/

RUN cp config.php.dist config.php  \
    && tlversion=$(cat /usr/local/texlive/20*/release-texlive.txt | head -n 1 | awk '{ print $5 }') \
    && sed -i "s/\${tlversion}/${tlversion}/g" config.php
RUN cp docker/nginx.conf /etc/nginx/nginx.conf
RUN cp docker/www.conf /etc/php/8.2/fpm/pool.d/www.conf && \
    cp docker/www-tex.conf /etc/php/8.2/fpm/pool.d/www-tex.conf

RUN cp docker/superv.conf /etc/superv.conf

ENTRYPOINT [ "/var/www/i.upmath.me/docker/entrypoint.sh" ]
