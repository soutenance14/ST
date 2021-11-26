# ST Snow Tricks
## Développez de A à Z un site communautaire
## CODACY Analyse
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/71e83b7650d54648929ac723f2fe32d5)](https://www.codacy.com/gh/soutenance14/ST/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=soutenance14/ST&amp;utm_campaign=Badge_Grade)
## Install git
GIT (https://git-scm.com/downloads) 
## Install the project with git
https://github.com/soutenance14/ST.git
## Use composer
composer install
## Use credentials in the .env file for the database
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7
## Get an account on a mail server and use credential in the .env file
MAILER_DSN=smtp://user:password@server:port?verify_peer=0
## Create database
php bin/console doctrine:database:create
## Create migrations
php bin/console doctrine:migrations:migrate    
## Launch server
php bin/console server:run
## Load data fixtures
php bin/console doctrine:fixtures:load
## Login
You can use different account, for example:
* email: test@gmail.com
* password: password


