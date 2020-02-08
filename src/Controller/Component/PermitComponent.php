<?php
namespace UtilCake\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use UtilCake\Permit\Permit;

/**
 *  configuration file example (config/permits.php):
 *    return ['Permits'=>[
 *      'PluginName.ControllerName'=>[
 *        'ActionName' => ['role list'],
 *      ],
 *      'Users'=>[
 *        'index' => ['public'],
 *        'view' => ['public', 'admin'],
 *        'edit' => 'public',
 *      ],
 *    ]];
 */

class PermitComponent extends Component{

  protected $_defaultConfig = [
    /**   default: Authentication.Authentication
      *   Auth
      */
    'component' => 'Authentication.Authentication',
  ];

  protected $_permit;

  public function __construct(ComponentRegistry $registry, array $config = []){
    $this->setConfig($config);
    $this->components = [$this->getConfig('component')];
    parent::__construct($registry, $config);
  }

  public function initialize(array $config){
    parent::initialize($config);
    $this->_permit = Permit::getInstance($config, $this->getIdentity() );
  }

  public function isAuthorize($user = null){
    if(!empty($user)){
      $this->_permit->setUser($user);
    }
    if( !empty($this->_permit->getUser()) ){
      $role_code = ( !empty($this->_permit->getUser('role')) ? $this->_permit->getUser('role') : 'logged' );
    }else{
      $role_code = null;
    }

    $access = $this->_permit->checkAutorize( $this->request->params, $role_code );
    if($access === 'public'){
      $this->allowAction($this->request->params['action']);
      return true;
    }
    return $access;
  }

  protected function getIdentity(){
    if($this->getConfig('component') == 'Authentication.Authentication'){
      return $this->request->getAttribute('identity');

    }else if($this->getConfig('component') == 'Auth'){
      return $this->Auth->user();
    }

    return null;
  }

  protected function allowAction($action_list){
    $action_list = ( is_string($action_list) ? [$action_list] : $action_list);
    if($this->getConfig('component') == 'Authentication.Authentication'){
      $this->Authentication->allowUnauthenticated($action_list);

    }else if($this->getConfig('component') == 'Auth'){
      $this->Auth->allow($action_list);

    }
  }

  public function access($role_list){
    return $this->_permit->access($role_list);
  }

  public function accessById($item_user_id, $role_list, $return_default = true){
    return $this->_permit->accessById($item_user_id, $role_list, $return_default);
  }

  public function getUser($item = null){
    return $this->_permit->getUser($item);
  }

}