<img src="https://s3-eu-west-1.amazonaws.com/eventsquare.assets/ext/payconiq_logo.png" alt="Payconiq" width="300"/>

# Payconiq API client for PHP #

Accepting [Payconiq](https://www.payconiq.com/) payments with the use of the QR code.

## Requirements ##
To use the Payconiq API client, the following things are required:

+ Payconiq Merchant Id and Access Token
+ PHP >= 5.6
+ PHP cURL extension

## Installation ##

The best way to install the Payconiq API client is to require it with [Composer](http://getcomposer.org/doc/00-intro.md).

    $ composer require eventsquare/payconiq-api-php

You may also git checkout or [download all the files](https://github.com/EventSquare/payconiq-api-php/archive/master.zip), and include the Payconiq API client manually.



## Parameters ##

We use the following parameters in the examples below:

```php
$merchant_id = ''; // The merchant ID registered with Payconiq.
$access_token = ''; // Used to secure request between merchant backend and Payconiq backend.

$amount = 1000; // Transaction amount in cents
$currency = 'EUR'; // Currency
$callbackUrl = 'http://yoursite.com/postback'; // Callback where Payconiq needs to POST confirmation status
```

To learn more about how, when and what Payconiq  will POST to your callbackUrl, please refer to the developer documentation [right here](https://dev.payconiq.com/online-payments-dock).

## Usage ##


### Create a transaction ###


```php
use Payconiq\Client;

$payconiq = new Client($merchant_id, $access_token);
	
// Create a new transaction
$transaction_id = $payconiq->createTransaction($amount, $currency, $callbackUrl);
	
// Assemble QR code content
$qrcode = 'https://payconiq.com/pay/1/' . $transaction_id;
```

### Retrieve a transaction ###

```php
use Payconiq\Client;

$payconiq = new Client($merchant_id, $access_token);

// Retrieve a transaction
$transaction = $payconiq->retrieveTransaction($transaction_id);
```
	
## Laravel support ##

We have provided a service provider to use this class with Laravel > 5.1.


Add the following line to the Framework Service Providers in config/app.php

```php
Payconiq\Support\Laravel\PayconiqServiceProvider::class,
```

Add the following entry to the aliases

```php
'Payconiq' => Payconiq\Support\Laravel\PayconiqFacade::class,
```

Publish the Payconiq config file with the artisan command and fill in your credentials in the config/payconiq.php config file.

```php
php artisan vendor:publish
```
	
### Create a transaction ###
```php
use Payconiq;

// Create a new transaction
$transaction_id = Payconiq::createTransaction($amount, $currency, $callbackUrl);
	
// Assemble QR code content
$qrcode = 'https://payconiq.com/pay/1/' . $transaction_id;
```	
	
### Retrieve a transaction ###
```php
use Payconiq;

// Retrieve a transaction
$transaction = Payconiq::retrieveTransaction($transaction_id);
```	