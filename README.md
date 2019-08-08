# Ship Wise REST API

Ship Wise REST API is based on Yii 2 Advanced Project Template with few modifications on layers.

The API layer is introduced as main application entry point for RESTful API calls.

Console layer is kept for database migrations.
And the common folder to have all data models shared by API versions in one place.

Data models under api/ namespace MUST extend from their parent class from common/.

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers. This template includes two tiers: api and console, each of which
is a separate Yii application.


The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Deployment is handled by [BitBucket pipelines](bitbucket-pipelines.yml). Their is a deploy script on the web server that
sits in `/usr/local/bin/deploy-api.sh` that can also be [viewed in the repo](deploy-api.sh).


DIRECTORY STRUCTURE
-------------------

```
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
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in api versions
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
