CryptBundle
===========

[![Build Status](https://travis-ci.org/GlobalTradingTechnologies/crypt-bundle.svg?branch=master)](https://travis-ci.org/GlobalTradingTechnologies/crypt-bundle)
[![Latest Stable Version](https://poser.pugx.org/gtt/crypt-bundle/version)](https://packagist.org/packages/gtt/crypt-bundle)
[![Latest Unstable Version](https://poser.pugx.org/gtt/crypt-bundle/v/unstable)](//packagist.org/packages/gtt/crypt-bundle)
[![License](https://poser.pugx.org/gtt/crypt-bundle/license)](https://packagist.org/packages/gtt/crypt-bundle)

Provides a simple way to configure symfony services for data encryption and decryption.

Requirements
============

Requires only PHP 5.3+ and symfony/framework-bundle.

Installation
============

Bundle should be installed via composer

```
composer require gtt/crypt-bundle
```
After that you need to register the bundle inside your application kernel.

Encryption
==========

Under the hood bundle uses well-known php components for encrypting data and provides implementation of
[encryptor](https://github.com/GlobalTradingTechnologies/crypt-bundle/blob/master/Encryption/EncryptorInterface.php) and [decryptor](https://github.com/GlobalTradingTechnologies/crypt-bundle/blob/master/Encryption/DecryptorInterface.php) interfaces based on them.
This implementations are registered as a Symfony 2 services and can be injected using symfony's DI tag attached to your consumer service or directly by ids.

Supported encryption components
===============================
* RSA (Based on [zendframework/zend-crypt](https://github.com/zendframework/zend-crypt))