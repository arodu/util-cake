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
