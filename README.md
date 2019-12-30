# S2 LaTeX Renderer

Service for generating nice [SVG pictures from LaTeX equations](https://i.upmath.me/) for web. You can try it in action in the [Markdown and LaTeX online editor](https://upmath.me).

## Requirements

1. [TeX Live](https://www.tug.org/texlive/quickinstall.html). I prefer a full installation and disabling write18 support.
2. `nginx` web server with [ngx_http_lua_module](https://github.com/openresty/lua-nginx-module) (for example, [nginx-extras Debian package](https://packages.debian.org/search?searchon=names&keywords=nginx-extras)).
3. `php-fpm`. `proc_open()` function must be enabled. Add the TeX bin directory (e.g. '/home/tex/tl-2016/bin/x86_64-linux') to the PHP PATH environment variable. Otherwise there can be floating bugs with generating font files.
4. Node.js and frontend tools: `npm`, `bower`, `grunt-cli`. Make the following symlink on Debian: `root:/usr/bin# ln -s nodejs node`.
5. `ghostscript` (used internally by `dvisvgm` TeX component).
6. Utilities: `rsvg-convert`, `optipng`, `pngout`. Install them or modify the code to disable PNG support.

## Installation

Deploy files:

```
git clone git@github.com:parpalak/tex.s2cms.ru.git
cd tex.s2cms.ru
npm install
composer install
bower install
grunt
```

Create the site config file:

```
cp config.php.dist config.php
mcedit config.php # specify the LaTeX bin dir and other paths
```

Set up the host:

```
sudo cp nginx.conf.dist /etc/nginx/sites-available/tex.s2cms.ru
sudo mcedit /etc/nginx/sites-available/tex.s2cms.ru
```
