# Ship Wise 
### Multi-Tier Project

This project includes three tiers: api, frontend, and console, each of which
is a separate Yii application.

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

For the first time installation, please refer to the following guide: 
https://github.com/yiisoft/yii2-app-advanced/blob/master/docs/guide/start-installation.md



Deployment is handled by [BitBucket pipelines](bitbucket-pipelines.yml). There is a deploy script on the web server that
sits in `/usr/local/bin/deploy-api.sh` that can also be [viewed in the repo](deploy-api.sh).


DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both api and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
api
    config/              contains api configurations
    modules/             contains api versions, this is the main folder for api source code.
                         each module is an api version, example: v1/
                         each version module contains controller and models specific to that version.
                         data models extend from common/models
                         
                         v1/
                            components/
                                parameters/         contains pagination, limit and search behaviours
                                security/           contains the api consumer security behaviour
                                ControllerEx.php    base controller with attached behaviours
                            controllers/            contains controllers per api ressource
                            models/
                                core/               extended models with core functionality
                                customer/           extended models grouped by customer entity
                                forms/              models representing the api request forms
                                order/              extended models grouped by order entity
                                swagger/            swagger definitions
                            
    runtime/             contains files generated during runtime
    tests/               contains tests for api application
    views/               not used
    web/                 contains the entry script and Web resources
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```

### Local development with Docker

Prerequisite: Download and install Docker Desktop for your OS.

Start your Docker container with:

`docker-compose up -d`


Example of importing a gzipped mysql dump:

`zcat cgsmpoim_shipwise.sql.gz | mysql -h mysql -u root -p cgsmpoim_shipwise`
