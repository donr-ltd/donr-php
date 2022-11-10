Donr Webhook
=================

Introduction
------------
Handler for the Donr webhooks.

Example usage
-------------
```php
$webhookSecret = 'wh_********************************';

$payload = @file_get_contents('php://input');

$authorisation = $_SERVER['HTTP_AUTHORIZATION'];

$payload = Donr\Webhook::constructEvent($payload, $authorisation, $webhookSecret);
```
