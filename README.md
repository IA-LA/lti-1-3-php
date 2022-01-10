# LTI 1.3 Advantage Tool Server
This code consists on a LTI Tool Server that utilize the library LTI 1.3 PHP library https://github.com/IMSGlobal/lti-1-3-php-library.

# Main URLs

## Server:
Global:
~~~
http://ailanto-dev.intecca.uned.es:9002/login.php
http://ailanto-dev.intecca.uned.es:9002/jwks.php
http://ailanto-dev.intecca.uned.es:9002/launch.php

~~~
Local:
~~~
http://10.201.54.31:9002/login.php
http://10.201.54.31:9002/jwks.php
http://10.201.54.31:9002/launch.php
~~~

## Repository:
Global:

Local:
~~~
http://10.201.54.232/vanesa/servidorLTI.git
~~~

# Running The Server

## Setup
The System is all written in PHP, and it also contains a docker compose file for easy setup if you have docker installed.

### Registration and Deployment
First thing you will need is to configure your registration and deployment in the registrations database collection.

```javascript
{
    "<issuer>": { // This will usually look something like 'http://example.com'
        "client_id"         : "<client_id>", // This is the id received in the 'aud' during a launch
        "auth_login_url"    : "<auth_login_url>", // The platform's OIDC login endpoint
        "auth_token_url"    : "<auth_token_url>", // The platform's service authorization endpoint
        "key_set_url"       : "<key_set_url>", // The platform's JWKS endpoint
        "private_key_file"  : "<path_to_private_key>", // Relative path to the tool's private key
        "deployment"        : [
            "<deployment_id>" // The deployment_id passed by the platform during launch
        ]
    }
}
```

To register your tool inside a platform, the platform will need two URLs

```
OIDC Login URL: http://localhost:9002/login.php
LTI Launch URL: http://localhost:9002/launch.php
```

### Running in Docker
To run in docker you will need both `docker` and `docker-compose`

To get the server up and running in docker simply run:
```
docker-compose up --build
```

# Example Platform
An example platform has been added to show an example of launching.

The registration and deployment between the example platform and Tool Server is already set up, so no configuration is needed.

To view the example platform, go to http://localhost:9002/platform

**Note:** This example platform is for trainning purposes only and not a full platform library.

# Library Contributing
If you have improvements, suggestions or bug fixes, feel free to make a pull request or issue and someone will take a look at it.

You do not need to be an IMS Member to use or contribute to this library, however it is recommended for better access to support resources and certification.

This library was initially created by @MartinLenord from Turnitin to help prove out the LTI 1.3 specification and accelerate tool development.

**Note:** This library is for IMS LTI 1.3 based specifications only. Requests to include custom, off-spec or vendor-specific changes will be declined.
