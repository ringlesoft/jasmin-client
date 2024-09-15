# Jasmin SMS Gateway Client for Laravel
[![Latest Version on Packagist](https://img.shields.io/packagist/v/ringlesoft/jasmin-client.svg)](https://packagist.org/packages/ringlesoft/jasmin-client)
[![Total Downloads](https://img.shields.io/packagist/dt/ringlesoft/jasmin-client.svg)](https://packagist.org/packages/ringlesoft/jasmin-client)
[![PHP Version Require](https://poser.pugx.org/ringlesoft/jasmin-client/require/php)](https://packagist.org/ringlesoft/jasmin-client)
[![Dependents](https://poser.pugx.org/ringlesoft/jasmin-client/dependents)](https://packagist.org/packages/ringlesoft/jasmin-client)
***
A Laravel package for seamless integration with Jasmin SMS Gateway, supporting HTTP, REST API, and SMPP connections.

## Features

- Easy-to-use interface for sending and receiving SMS
- Support for HTTP and REST API jasmin options
- SMPP support is coming soon
- Delivery report handling

## Installation

You can install the package via composer:

```bash
composer require ringlesoft/jasmin-client
```

## Configuration

### Publish the configuration file:

```bash
php artisan vendor:publish --provider="RingleSoft\JasminClient\JasminClientServiceProvider"
```
Then, edit the `config/jasmin_client.php` file with your Jasmin SMS Gateway credentials and preferred settings.

### Available configurations

The following are available configurations. Most of these are just defaults and can be overridden in your code

- `url` : The base url of the jasmin server (include the port if different from `80` or `443/446`)
- `username` : The username for your jasmin account
- `password` : The password for your jasmin account
- `dlr_callback_url` : The default sms delivery callback url
- `batch_callback_url` : the default batch callback url
- `batch_errback_url` : the default batch errback url
- `default_dlr_method` : the default dlr method (GET/POST)
- `default_dlr_level` : The default DLR level (1, 2, or 3)
- `batch_chunk_size` : The default Chunk size for batches


## Usage

### Sending an SMS

#### Sending a single message  (Http & Rest)

```php
$sms = JasminClient::message()
    ->content('Hello there! Have a nice day')
    ->to("255711000000")
    ->from('INFO')
    ->via('rest') // 'rest' or 'http'
    ->send();
```

- Returns  `RingleSoft\JasminClient\Models\Jasmin\SentMessage`

#### Sending multiple messages as a batch (Rest only)

```php
    $message = JasminClient::message(to: "255711000000", content: "Hello There. Have a nice day");
    $message2 = JasminClient::message(to: "255711000002", content: "Hello There. Have a nice day");
    $batch = JasminClient::batch()
    ->addMessage($message)
    ->addMessage($message2)
    ->from("INFO")
    ->send();
```

- Returns  `RingleSoft\JasminClient\Models\Jasmin\SentBatch`

### Handling Delivery Statuses

### Handling Batch Callback Requests

When messages are sent in a batch, Jasmin responds with the ID of the batch created and enqueue the messages.
When each message is sent to SMC, jasmin fires a callback (batch callback) with messageIds of each message within the
batch

### Checking rates (Http & Rest)
```php
    $route = JasminClient::rest()->checkRoute("255711000000");
```

### Checking account balance (Http & Rest)
```php
    $balance = JasminClient::rest()->checkBalance();
```

### Monitoring Metrics (Http)
```php
    $metrics = JasminClient::http()->getMetrics();
```


## Contributing

I'll soon let you know when you can contribute to this project.

## License

This package is open-sourced software licensed under the MIT license.

## Support

- [Buy me a Coffee](https://www.buymeacoffee.com/ringunger)
- [Github Sponsors](https://github.com/sponsors/ringlesoft)

## Contacts

Follow me on <a href="https://x.com/ringunger">X</a>: <a href="https://x.com/ringunger">@ringunger</a><br>
Email me: <a href="mailto:ringunger@gmail.com">ringunger@gmail.com</a><br>
Website: [https://ringlesoft.com](https://ringlesoft.com/packages/jasmin-client)

> Note: This package is still under development. Please report any issues you encounter.
