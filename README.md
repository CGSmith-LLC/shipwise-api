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


### Cronjobs:

To create/edit crontab file:

```
crontab -e
```

1. Overnight cronjob:

```
15 1 * * * /var/www/api/yii cron/overnight >> /var/www/api/console/runtime/logs/cronjob.log 2>&1
```

To list existing cronjobs:

```
crontab -l
```


### Job Workers

**Yii2 Queue** is an extension for running tasks asynchronously via queues.

https://github.com/yiisoft/yii2-queue/blob/master/docs/guide/worker.md


`sudo vim /etc/systemd/system/yii-queue@.service`

```
[Unit]
Description=Yii Queue Worker %I
After=network.target
# the following two lines only apply if your queue backend is mysql
# replace this with the service that powers your backend
After=mysql.service
Requires=mysql.service

[Service]
User=apache
Group=apache
ExecStart=/usr/bin/php /var/www/api/yii queue/listen --verbose
Restart=on-failure

[Install]
WantedBy=multi-user.target
```

`sudo systemctl daemon-reload`

`sudo systemctl start yii-queue@1 yii-queue@2`

`sudo systemctl enable yii-queue@1 yii-queue@2`

`systemctl status "yii-queue@*"`

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

### Running queue jobs locally

When you are developing in a local environment, all you need to have the queue jobs executed is this:

- open the CLI on your local dev server, eg. docker or vagrant instance, then enter this command and keep the terminal open:

`php yii queue/listen --verbose`

