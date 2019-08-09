<?php

namespace UtilCake\View\Helper;

use Cake\View\Helper;
use UtilCake\Permit\Permit;

class PermitHelper extends Helper{

  public $helpers = ['Form', 'Html'];

  protected $_permit;

  public function initialize(array $config){
    parent::initialize($config);
    $this->_permit = Permit::getInstance($config);
  }

  public function access($role_list){
    return $this->_permit->access($role_list);
  }

  public function accessBy( $url = null ){
    $url = $this->_permit->parse($url);
    if(empty($url['controller'])){
      $url['controller'] = $this->_View->request->param('controller');
    }

    return $this->_permit->checkAutorize($url, $this->_permit->getUser('role'), true);
  }

  public function postLink($title, $url = null, array $options = []){
    if($this->accessBy($url)){
      return $this->Form->postLink($title, $url, $options);
    }
    return '';
  }

  public function link($title, $url = null, array $options = []){
    if($this->accessBy($url)){
      return $this->Html->link($title, $url, $options);
    }
    return '';
  }

}