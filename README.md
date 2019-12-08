# kinduct-codeigniter-api-test
My stab at writing a transformer based API service because... why not?

# Installation
* Clone the repository 

    ``git clone https://github.com/sonoftheweb/kinduct-codeigniter-api-test.git``

* Run composer install. This installs all the required packages and one that helps me define setup .env 
(environment variables) file for safeguarding sensitive data. Note that the env example file is in the app folder.

    ``composer install``

* Setup a MySQL database

* Setup .env file. Copy ``app/.env_example`` to ``app/.env`` and add the correct values to variables:

    CI_BASE_URL: the base URL of the installation eg ``http://localhost/kinduct-codeigniter-api-test``
    
    DB_HOST: MySQL host address
    
    DB_USER: MySQL user
    
    DB_PASSWORD: MySQL password
    
    DB_NAME: MySQL database name
    
    CI_ENV: ``development`` or ``production`` as based on installation environment
    
    CI_ENCRYPTION_KEY: remove the ``#`` sign and set an encryption key
    
### About the app
The API is built around Codeigniter and utilizes a much more "code reusable" approach. Each
resource is basically a model (database table). As such because the app might have multiple 
resources, it would be best to use one set of routes to not only call any resource, but also
run resource based methods located in transformers.

Transformers were added to separate the web application layer from the API layer, just in-case
you are looking to build out pages with functionality. Transformers are created as libraries and are located in the app/libraries/api folder.

Models remain where they are as they may be used even in the application in the future. Controllers are free to do any web app
functions and load the result to the front-end (If you are building a "non spa" application or even one using a singular view file to load JS app into it).  
