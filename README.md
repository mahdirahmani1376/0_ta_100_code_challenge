# HostIran Finance service

This application developed and customized by [Laravel Framework](https://laravel.com/)  version 10.*.

## Setup
1. Copy root `.env.example` into `.env` and change the env variables
   1. `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   2. Check the values for `WWWGROUP` and `WWWUSER` to match your host's ID
   3. If you'd like to change the default port for application change `APP_PORT` default is `80`
2. RUN `./vendor/bin/sail up -d` to build and start your project
    1. To make running `sail` commands easier you can add an alias to your `~/.bashrc` by adding the following line to it
    `alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'` and then you can simply run `sail up -d`

### Author(s)
* **Esmaeel Cheshmeh Khavar** ([Gmail](mailto:e.cheshmehkhavar@gmail.com))
