# Ship Wise REST API

This layer is introduced as main application entry point for RESTful API calls.

Data models under api/ namespace MUST extend from their parent class from `common/`.


DIRECTORY STRUCTURE
-------------------

```
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
```
