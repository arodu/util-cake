<?php
namespace UtilCake\Model\Behavior;

use Cake\ORM\Behavior;

class FileBehavior extends Behavior{

  protected $_defaultConfig = [
      'folder' => null,
  ];

  public function initialize(array $config){

  }
}
