# UtilCake plugin for CakePHP

CakePHP Plugin, collection of utilities for CakePHP 3.x

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:
```sh
composer require arodu/util-cake
```

## Configuration

You can load the plugin using the shell command:

```
bin/cake plugin load UtilCake
```

Or you can manually add the loading statement in the **src/Application.php** file of your application:
```php
public function bootstrap()
{
    parent::bootstrap();
    $this->addPlugin('UtilCake');
}
```
Prior to 3.6.0
```php
Plugin::load('UtilCake');
```