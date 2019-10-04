# Status

[![Latest Stable Version](https://poser.pugx.org/code-foundation/flow-config/v/stable)](https://packagist.org/packages/code-foundation/flow-config) [![License](https://poser.pugx.org/code-foundation/flow-config/license)](https://packagist.org/packages/code-foundation/flow-config) [![codecov](https://codecov.io/gh/code-foundation/flow-config/branch/master/graph/badge.svg)](https://codecov.io/gh/code-foundation/flow-config)
[![CircleCI](https://circleci.com/gh/code-foundation/flow-config.svg?style=svg)](https://circleci.com/gh/code-foundation/flow-config)

# Introduction

Flow Config is a key value configuration platform built on top of doctrine. It provides an PHP API for setting configuration
 at the platform that can be set by an install, and then set for a user, or other entity. Defaults are set in a single
 location, rather than scattering them through the code.

# Installation

```php
composer require code-foundation/flow-config
```

# Usage

## Attach the EntityIdentifier interface to user classes

This allows EntityConfig classes to use the above 

```php
class User implements CodeFoundation\FlowConfig\Interfaces\EntityIdentifier
{
    public function getEntityType(): string
    {
        return 'user';
    }

    public function getEntityId(): string
    {
        return $this->id;
    }
}

```

## Build configuration classes

```php
// Build a read-only config
$baseConfig = new ReadonlyConfig([
    'timezone' => 'UTC'
]);

// Build a system config
$systemConfig = new DoctrineConfig($this->getEntityManager());

// Build a entity based config
$entityConfig = new DoctrineEntityConfig($this->getEntityManager());

// Build the cascading configuration objects that tries each of the above in turn.
$cascadeConfig = new CascadeConfig($baseConfig, $systemConfig, $entityConfig);
```

If you don't want the Config services to automatically flush changes to the database, pass a false for `$autoFlush` in
 the constructor of the service.

```php
$systemConfig = new DoctrineConfig($this->getEntityManager(), false);

$entityConfig = new DoctrineEntityConfig($this->getEntityManager(), false);
```

## Control key accessibility using the AccessibilityInterface
In some cases, you may wish to control each specific attempt to get/set specific keys, and this is where implementing
the AccessibilityInterface is useful.

By default, the `NullAccessibility` class is instantiated, which defaults to allowing all `get` and `set` attempts.
```php
class MyAccessibilityClass implements AccessibilityInterface
{
    // Set our read-only keys.
    private $readOnlyKeys = ['abc123'];
    
    // Set our keys that are restricted and never returned.
    private $restrictedKeys = ['xyz987'];

    public function canGetKey(string $key): bool
    {
        // Return whether the key is not in our read-only array.
        return \in_array($key, $this->readOnlyKeys) === false;
    }
    
    public function getSetKey(string $key): bool
    {
        // Return whether the key is not in our restricted array.
        return \in_array($key, $this->restrictedKeys) === false;
    }
}
``` 

## Examples

```php
$user1 = new User()->setId(999);
$user1 = new User()->setId(1001);

echo $systemConfig->get('timezone'); // UTC
echo $entityConfig->get('timezone'); // UTC
echo $cascadeConfig->getEntityConfigItem('timezone', $user1); // UTC
echo $cascadeConfig->getEntityConfigItem('timezone', $user2); // UTC

// Update the setting for that platform.
$entityConfig->set('timezone', 'Australia/Melbourne');
echo $systemConfig->get('timezone'); // UTC
echo $entityConfig->get('timezone'); // 'Australia/Melbourne'
echo $cascadeConfig->getEntityConfigItem('timezone', $user1); // 'Australia/Melbourne'
echo $cascadeConfig->getEntityConfigItem('timezone', $user2); // 'Australia/Melbourne'

// Update a given users settings
$cascadeConfig->setByEntity($user1, 'timezone', 'Pacific/Auckland');
echo $systemConfig->get('timezone'); // UTC
echo $entityConfig->get('timezone'); // 'Australia/Melbourne'
echo $cascadeConfig->getEntityConfigItem('timezone', $user1); // 'Pacific/Auckland'
echo $cascadeConfig->getEntityConfigItem('timezone', $user2); // 'Australia/Melbourne'
```

# Supported platforms
* PHP 7.1+
* Doctrine

# Future plans
* Symfony package
* Validation
* Allowed values
* Factories
* Eloquent backend

# Contact

Github: https://github.com/code-foundation/flow-config
Email: contact@codefoundation.com.au

# License
Flow Config is distributed under the MIT license.
