MdgMultiUser
============

Created by Michael Gooden (#MichaelGooden).

I can usually be found on [#zftalk on Freenode](http://webchat.freenode.net/?channels=zftalk)
if you need help setting this up.

Introduction
------------

MdgMultiUser is a module for [Zend Framework 2](https://github.com/zendframework/zf2)
that enables you to configure and use multiple instances of the 
[ZfcUser](https://github.com/ZF-Commons/ZfcUser) user registration and
authentication module.

Requirements
------------

* [ZfcUser](https://github.com/ZF-Commons/ZfcUser) (>=v0.1.0,<v0.2.0).

Installation
------------

### Main Setup

#### With composer

1. Add this project to your composer.json:

    ```json
    "require": {
        "michaelgooden/mdg-multi-user": "0.1.*"
    }
    ```

2. Now tell composer to download MdgMultiUser by running the command:

    ```bash
    $ php composer.phar update
    ```

#### Post installation

1. Enabling it in your `application.config.php`file.

    ```php
    <?php
    return array(
        'modules' => array(
            // ...
            'MdgMultiUser',
        ),
        // ...
    );
    ```

### Post-Install: Zend\Db

1. You can use the schema provided by ZfcUser, just change the table name for
   each subsystem you setup.

2. This module does require a minimal amount of configuration to work. An
   example configuration file has been provided `./config/mdgmultiuser.example.global.php.dist`.

   Copy this file to your projects `./config/autoload/` folder, and edit the
   examples to suit your requirements.

   Importantly, you will be required to setup a full route structure for any
   subsystems you wish to have.

### Usage Notes

In order to access the view helpers and controller plugins, you need to call a
different set of commands.

 -  Controller plugin `ZfcUserAuthentication()` maps to `MdgMultiUserAuthentication($alias)`
 -  View helper `ZfcUserDisplayName()` maps to `MdgMultiUserDisplayName($alias)`
 -  View helper `ZfcUserIdentity()` maps to `MdgMultiUserIdentity($alias)`
 -  View helper `ZfcUserLoginWidget()` maps to `MdgMultiUserLoginWidget($alias)`

In all cases `$alias` must be replaced by the name of your subsystem. This is
derived from the key of the config array under `'mdgmultiuser'`.
