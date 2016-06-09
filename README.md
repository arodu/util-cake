# UtilCake
CakePHP Plugin, collection of utilities for cakephp 2.x

## Instalation
  * Clone this repo in "app/Plugin"
  * Active the plugin in "app/Config/bootstrap.php", adding this line
  ``` php
  // app/Config/bootstrap.php
  CakePlugin::load('UtilCake');
  ```

## Utilities

### MysqlBackup
  * Generate and Restore Backups of Mysql DataBase

  * Example
    ``` php
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
