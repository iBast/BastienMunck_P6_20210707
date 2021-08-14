# BastienMunck_P6_20210707

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ff6180a9244241b5b5652cd5263baef6)](https://app.codacy.com/gh/iBast/BastienMunck_P6_20210707?utm_source=github.com&utm_medium=referral&utm_content=iBast/BastienMunck_P6_20210707&utm_campaign=Badge_Grade_Settings)

Project 6 of [Openclassrooms](https://openclassrooms.com) php symfony training courses


## Project
The aim of the project is to create a community web site on snowboard tricks. 
Users can create and edit there own tricks and every verified user can use the comment section

## Installation
* Clone the repository with git clone

```console
git clone https://github.com/iBast/BastienMunck_P6_20210707.git
```

* Install the dependencies with
```console
composer install
```

* create a .env file or set your hosting environnement with : 
```
APP_ENV=prod
APP_SECRET= $$ your secret $$
MAILER_DSN=smtp://user:password@host:port
DATABASE_URL="mysql://user:password@host/db?serverVersion=X.X.XX"
```

* install the database
    * you can use the sql file in the repository 
    * or you can install the db with doctrine 

        if the database does not exist, create it with the following command in the project directory

            ```console
            php bin/console doctrine:database:create
            ```

        then
        
            ```console
            php bin/console doctrine:migration:migrate
            ```

* Install fixtures to have first contents and the admin account
```console
php bin/console doctrine:fixtures:load
```
