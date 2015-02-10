oath-service-php
================

PHP implementation of an OATH service, with support for HOTP, TOTP, and OCRA. See
- http://www.ietf.org/rfc/rfc4226.txt
- http://www.ietf.org/rfc/rfc6238.txt
- http://www.ietf.org/rfc/rfc6287.txt

Dependencies
============

PHP 5.3.3 or later is required, as well as the `ext-curl` and `ext-intl` extensions.
Composer [https://getcomposer.org] is used for package management. 

Install
=======

First, install dependencies.

On a debian-based system:

    sudo apt-get install -y git curl ntpdate php5 php5-curl php5-intl

Clone this repo.

    git clone https://github.com/SURFnet/oath-service-php.git
    cd oath-service-php/

Update Composer, and install packages.

    ./composer.phar selfupdate
    ./composer.phar install

During the composer install, the file `app/config/parameters.yml` is auto-generated. You will need to provide configuration parameters, such as the database for storing user data. 

In the remainder of this document, we'll assume defaults for all parameters.

In a production environment, it is important to change the defaults. In particular, it is important to provide decent random values for secrets. These can be generated using `/dev/urandom`, for instance:

    tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo

At this point you should be able to run the service using the php build-in web server:

    php app/console server:run 

and access the api docs:

    curl -I http://0:8000/api/doc/

which should result in a `HTTP 200 OK` message.

Install a database
------------------

To store secrets in a database, install mysql server.

    sudo apt-get install -y mysql-server php5-mysql

Create the database

    mysql -u root -p
    create database oathservice;
    use oathservice;

In a production environment, create a dedicated user for accessing the database and add required privileges.

Next, create the `secret` table

    CREATE TABLE `secret` (
     `identifier` varchar(100) NOT NULL,
     `secret` varchar(255) NOT NULL,
     `counter` int(10) DEFAULT '0',
     PRIMARY KEY (`identifier`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

You should now be able to store user's secrets and validate OTPs using the API. Note that the API requires an HTTP header carrying a consumer key. To store a secret for user `john`:

    curl --header "x-oathservice-consumerkey: ThisKeyShouldBeSecret" 'http://0:8000/secrets/john' --data secret=12345678901234567890

To validate an OTP for this user (using test vectors from RFC 4226):

    curl --header "x-oathservice-consumerkey: ThisKeyShouldBeSecret" 'http://0:8000/oath/validate/hotp?userId=john&counter=0&response=755224'

To delete the secret for this user:

    curl --header "x-oathservice-consumerkey: ThisKeyShouldBeSecret" 'http://0:8000/secrets/john' --request DELETE

To calculate an OTP according to the HOTP algorithm (for testing purposes), for a given secret and counter, use

	php -r 'include("src/SURFnet/OATHBundle/OATH/HOTP.php");echo (new SURFnet\OATHBundle\OATH\HOTP())->calculateResponse("12345678901234567890",0);'

Alternatively, you can use the `oathtool` package:

	sudo apt-get install -y oathtool
	oathtool --hotp --counter=0 3132333435363738393031323334353637383930


Running the API from a web server
---------------------------------

Please refer to the symfony docs:

http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html

