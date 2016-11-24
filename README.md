# UtilCake
CakePHP Plugin, collection of utilities for cakephp 2.x

## Instalation
  http://book.cakephp.org/2.0/en/plugins/how-to-install-plugins.html

## Utilities

### PermitComponent

### GeneralHelper

### bsFormHelper
  * Helper to bootstrap forms
  
  * How to use
  ```php
  // app/Controller/AnyController.php
  public $helpers = array(
    'Form'=>array('className'=>'UtilCake.bsForm')
  );
  ```

### SoftDeleteBehavior

### MysqlBackup
  * Generate and Restore Backups of Mysql DataBase

  * Example
    ```php
    // app/Controller/AnyController.php
    public function mysql_backup($tables = '*'){
      $this->loadModel('UtilCake.MysqlBackup');
      $result = $this->MysqlBackup->generate($tables);
      $fileName = $this->MysqlBackup->generateName();

      $this->autoRender = false;
      $this->response->type('text/x-sql');
      $this->response->charset('utf8');
      $this->response->body($result);
      $this->response->download($fileName);

      return $this->response;
    }
    ```
    
### AppExtendModel
  * News validations rules to models
  * How to use
    ```php
    App::uses('AppExtendModel', 'UtilCake.Model');
    class MyModel extends AppExtendModel {
      ...
    }
    ```
  * Validations Rules
    * dateRange: Min and Max dates
        ```php
        'notFuture' => array(
          'rule' => array('dateRange',
           array('min'=>'2010-01-01', 'max'=>'today')),
        ```
    * equalToField: to check if two fields are equals
        ```php
        'email_confirm' => array(
          'equalToField' => array(
            'rule' => array('equalToField','email'),
            //'rule' => array('equalToField',<< field to check >>),
          ),
        ),
        ```
    * passwordCheck: to check if a field is equal to password_field
        ```php
        'password_check' => array(
          'passwordCheck' => array(
            'rule' => array('passwordCheck', 'password'),
            //'rule' => array('passwordCheck', << password field >> ),
          ),
        ),
        ```
