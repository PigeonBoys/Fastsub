# Fastsub

## Introduction

The Fastsub package leverages Redis hashes to efficiently manage subscriptions. Each identity—such as a serial number, user ID, or other unique identifiers—has a dedicated hash. Subscription data is stored as key-value pairs within these hashes, where the values can be plain strings or JSON-encoded objects. This setup allows for easy tracking of subscription details like products, expiration dates, and more. JSON encoding also supports multiple subscriptions per identity, enabling the management of various services under one unique identifier. Other services can query these identity-specific Redis hashes for real-time access to subscription data.

## Installation

To install the package, run the following command:

```bash
$ composer require pigeonboys/fastsub
```

## Usage

### Configuration

Before calling the `SubscriptionQuery` class for the first time, the `SubscriptionConfiguration` class must be initialized:

```php
use PigeonBoys\Fastsub;

Fastsub\SubscriptionConfiguration::initialize(host: '127.0.0.1', port: 6379, database: 0);
```

### Set Subscriptions

You can set the value of a field (key) as a plain string. However, it is recommended to use a JSON-encoded object to store additional subscription details.

```php
use PigeonBoys\Fastsub;

// Set key-value pair in the <serialnumber> hash
Fastsub\SubscriptionQuery::hash('serialnumber')->updateOrCreate('111111', '[{"product":"test-product","ends_at":1738774349}]');

// Set multiple key-value pairs in the <serialnumber> hash
Fastsub\SubscriptionQuery::hash('serialnumber')->upsert([
    '111111' => '[{"product":"test-product","ends_at":1738774349}]',
    '222222' => '[{"product":"test-product","ends_at":1738774349}]',
]);
```

### Get Subscriptions from field

You can retrieve the plain string value of a field (key) using the `get` method. Each field corresponds to an array index.

```php
use PigeonBoys\Fastsub;

// Retrieve the field '111111' from the 'serialnumber' hash
$s = Fastsub\SubscriptionQuery::hash('serialnumber')->field('111111')->get();

// Response:
array:1 [▼
  111111 => "[{"product":"test-product","ends_at":1738774349}]"
]
```

To retrieve the field as an array (assuming it was stored as a JSON-encoded string), use the `json` method:

```php
use PigeonBoys\Fastsub;

// Retrieve the field '111111' from the 'serialnumber' hash as a JSON object
$s = Fastsub\SubscriptionQuery::hash('serialnumber')->field('111111')->json();

// Response:
array:1 [▼
  111111 => array:1 [▼
    0 => array:2 [▼
      "product" => "test-product"
      "ends_at" => 1738774349
    ]
  ]
]
```

### Get Subscriptions from Multiple Fields

To retrieve the plain string values of multiple fields (keys), use the `get` method with an array of field names.

```php
use PigeonBoys\Fastsub;

// Retrieve fields '111111' and '222222' from the 'serialnumber' hash
$s = Fastsub\SubscriptionQuery::hash('serialnumber')->fields(['111111', '222222'])->get();

// Response:
array:2 [▼
  111111 => "[{"product":"test-product","ends_at":1738774349}]"
  222222 => "[{"product":"test-product","ends_at":1738774349}]"
]
```

Similarly, to retrieve the fields as JSON-encoded arrays, use the `json` method:

```php
use PigeonBoys\Fastsub;

// Retrieve fields '111111' and '222222' from the 'serialnumber' hash as JSON objects
$s = Fastsub\SubscriptionQuery::hash('serialnumber')->fields(['111111', '222222'])->json();

// Response:
array:2 [▼
  111111 => array:1 [▼
    0 => array:2 [▼
      "product" => "test-product"
      "ends_at" => 1738774349
    ]
  ]
  222222 => array:2 [▼
    0 => array:2 [▼
      "product" => "test-product"
      "ends_at" => 1738774349
    ]
  ]
]
```
