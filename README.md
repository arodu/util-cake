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
```sh
bin/cake plugin load UtilCake
```

Or you can manually add the loading statement in the **src/Application.php** file of your application:
```php
public function bootstrap(){
    parent::bootstrap();
    $this->addPlugin('UtilCake');
}
```

Prior to 3.6.0
```php
Plugin::load('UtilCake');
```

## How to use

### Permit
> Under construction

### reCaptcha V3

In the controller file
```php
public function initialize(){
  parent::initialize();
  $this->loadComponent('UtilCake.reCaptcha', [
    'public_key' => 'RECAPTCHA_PUBLIC_KEY',
    'secret_key' => 'RECAPTCHA_SECRET_KEY',
  ]);
}

public function action(){
  // ...
  if ($this->request->is('post')) {
    if($this->reCaptcha->verify($this->request)){
      // when the verification is successful
    }else{
      // when the verification is not successful
      $this->Flash->error(__('reCaptcha failed, try again'));
    }
  }
  // ...
}
```

In the template `Template/ControllerName/action.ctp`
```php
echo $this->Form->create();
echo $this->element('UtilCake.reCaptcha/input');
echo $this->Form->end();

echo $this->element('UtilCake.reCaptcha/script', ['action'=>'IdName']);
```


