# HostIran Finance service

This application developed and customized by [Laravel Framework](https://laravel.com/)  version 10.*.

## Setup
1. Copy root `.env.example` into `.env` and change the env variables
   1. `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   2. Check the values for `WWWUSER` and `WWWUSERUID` to match your host's ID
   3. If you'd like to change the default port for application change `APP_PORT` default is `80`
2. RUN `docker-compose build` to build your project
3. RUN `docker-compose up -d` to bring up your containers
4. RUN ` docker-compose exec --user root app .docker-compose/commands/bootup`
5. OPEN `http://localhost` to check if everything is working

### Author(s)
* **Esmaeel Cheshmeh Khavar** ([Gmail](mailto:e.cheshmehkhavar@gmail.com))
