# Ship Wise REST API

Ship Wise REST API is based on Yii 2 Advanced Project Template with few modifications on layers.
The API layer is introduced as main application entry point for RESTful API calls.

Console layer is kept for database migrations. 
And the common folder to have all data models shared by API versions in one place.

Data models under api/ namespace must extend from their parent class from common.

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes two tiers: api and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-advanced.svg)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-advanced.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-advanced)

DIRECTORY STRUCTURE
-------------------

```
api
    config/              contains api configurations
    controllers/         not used
    models/              not used
    modules/             contains API versions, this is the main folder for API source code.
                         each folder is an API version, example: v1/
                         each version folder contains controller and models specific to that version.
                         data models extend from common/models
    runtime/             contains files generated during runtime
    tests/               contains tests for api application
    views/               not used
    web/                 contains the entry script and Web resources
    widgets/             not used
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
