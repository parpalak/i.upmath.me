# Upmath LaTeX Renderer

Service for generating nice [SVG pictures from LaTeX equations](https://i.upmath.me/) for web. You can try it in action in the [Markdown and LaTeX online editor](https://upmath.me).

## Run with Docker
You can run the project locally using docker.
The docker image is built automatically after a push to the main branch using the github actions.
Before using the image, make sure you are [logged in at ghcr.io](https://github.com/features/packages).

You can run it with
```bash
docker run -t -p 8080:80 ghcr.io/parpalak/i.upmath.me:master
```

You will find the service active at `localhost:8080`.

## Manual installation

### Requirements

1. [TeX Live](https://www.tug.org/texlive/quickinstall.html). I prefer a full installation and disabling write18 support.
2. `nginx` web server with [ngx_http_lua_module](https://github.com/openresty/lua-nginx-module) (for example, [nginx-extras Debian package](https://packages.debian.org/search?searchon=names&keywords=nginx-extras)).
3. `php-fpm`. `proc_open()` function must be enabled.
   * Make the `/home/tex/tl-202*/texmf-var` dir writable for the `php-fpm` process user.
4. Node.js and frontend tools: `npm`, `grunt-cli`.
5. `ghostscript` (used internally by `dvisvgm` TeX component).
6. Utilities: `rsvg-convert`, `optipng`, `pngout`. Install them or modify the code to disable PNG support.

### Installation steps

Deploy files:

```
git clone git@github.com:parpalak/i.upmath.me.git
cd i.upmath.me
yarn install
composer install
grunt
```

Create the site config file:

```
cp config.php.dist config.php
mcedit config.php # specify the LaTeX bin dir and other paths
```

Set up the host:

```
sudo cp nginx.conf.dist /etc/nginx/sites-available/i.upmath.me
sudo mcedit /etc/nginx/sites-available/i.upmath.me
```

Set up systemd unit for SVGO http service:

```
sudo cp http-svgo.service.dist /etc/systemd/system/http-svgo.service
sed -i "s~@@DIR@@~$PWD~g" /etc/systemd/system/http-svgo.service
sudo systemctl start http-svgo
sudo systemctl enable http-svgo
```
