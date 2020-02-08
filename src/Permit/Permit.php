<?php
namespace UtilCake\Permit;

use Cake\Core\InstanceConfigTrait;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Http\Exception\UnauthorizedException;

class Permit{

  use InstanceConfigTrait;

  private static $instance;

  protected $_defaultConfig = [
    'denyByDefault'=>true,

    'config_file'=>'permits',
    'config_name'=>'Permits',

    'public_role' => 'public',
    'logged_role' => 'logged',

    'return'=>'exception', // 'boolean', 'exception'
  ];

  protected $_permitList = [];
  protected $_user = null;

  private function __construct(array $config = [], $user = null){
    $this->setConfig($config);
    Configure::load($this->getConfig('config_file'), 'default');
    $this->_permitList = Configure::read($this->getConfig('config_name'));
    $this->setUser($user);
  }

  public function setUser($user){
    $this->_user = $user;
  }

  public static function getInstance(array $config = [], $user = null){
    if(!self::$instance instanceof self){
      self::$instance = new self($config, $user);
    }
    return self::$instance;
  }

  public function parse($url = null){
    if(is_null($url) || is_string($url)){
      $url = Router::parse($url);
    }

    return [
      'plugin' => ( !empty($url['plugin']) ? $url['plugin'] : null ),
      'controller' => ( !empty($url['controller']) ? $url['controller'] : null ),
      'action' => ( !empty($url['action']) ? $url['action'] : null ),
    ];
  }

  protected function controllerName($url){
    if(!empty($url['plugin']) && is_string($url['plugin'])){
      return $url['plugin'].'.'.Inflector::camelize($url['controller']);
    }
    return Inflector::camelize($url['controller']);
  }

  protected function getRoleList($url){
    $controller = $this->controllerName($url);
    $action = $url['action'];

    if( !empty($this->_permitList[$controller][$action]) ){
      $permit_role_list = $this->_permitList[$controller][$action];
      $permit_role_list = (is_string($permit_role_list) ? [$permit_role_list] : $permit_role_list);
      return $permit_role_list;
    }

    return false;
  }

  /**
   *  return 'public', true or false
   */
  public function checkAutorize($url, $user_role = null, $return = false){
    if($permit_role_list = $this->getRoleList($url)){
      if (in_array($this->getConfig('public_role'), $permit_role_list)) {
        return ( $return ? true : 'public' );
      }

      if(!empty($user_role)){
        $user_role_list = ( is_string($user_role) ? [$user_role] : $user_role );
        if (in_array($this->getConfig('logged_role'), $permit_role_list) || array_intersect($permit_role_list, $user_role_list) ) {
          return true;
        }
      }

    }else{
      if(!$this->getConfig('denyByDefault')){
        return true;
      }
    }

    if($return){
      return false;
    }else{
      if($this->getConfig('return') == 'boolean'){
        return false;
      }
    }

    throw new UnauthorizedException();
  }

  public function access($role_list){
    $user_role = $this->getUser('role');

    $permit_role_list = (is_string($role_list) ? [$role_list] : $role_list);
    $user_role_list = ( is_string($user_role) ? [$user_role] : $user_role );

    return !empty( array_intersect($permit_role_list, $user_role_list) );
  }

  public function accessById($item_user_id, $role_list, $return_default = true){
    $role_list = (is_string($role_list) ? [$role_list] : $role_list);

    if(in_array( $this->getUser('role'), $role_list )){
      return ($item_user_id == $this->getUser('id'));
    }

    return $return_default;
  }

  public function getUser($item = null){
    if(empty($this->_user)){
      return null;
    }

    switch ($item) {
      case 'id':
        $out = $this->_user->id;
        break;

      case 'role':
        $out = $this->_user->role->code;
        break;

      default:
        $out = $this->_user;
        break;
    }

    return $out;
  }


}