Donr Webhook
=================

The Donr PHP library provides convenient access to the Donr API.

At present the library only supports the processing of webhooks requests.

## Requirements

PHP 5.4.0 and later.

## Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require donr/donr-php
```

## Setup Integration

To use a webhook the endpoint must first be registered within the Donr Dashboard. On registration a unique secret will be generated for the webhook. The secret is required by the library so that it can interact with the request.  

Example usage
-------------
```php
$webhookSecret = '{webhook-secret}';

$payload = @file_get_contents('php://input');

$authorisation = $_SERVER['HTTP_AUTHORIZATION'];

$payload = Donr\Webhook::constructEvent($payload, $authorisation, $webhookSecret);
```
