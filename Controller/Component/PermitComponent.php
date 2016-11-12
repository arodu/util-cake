<?php
App::uses('Component', 'Controller');
class PermitComponent extends Component {

  public $settings = array(
    'config' => array(
      'file' => 'permisos',
      'name' => 'Permisos',
    ),

    'userModel' => 'User',
    'profileModel' => 'Profile',

    'defaultResponse' => false,   // false,  loged,  public
    'userRoot' => false,
    'reload' => false,
    'errorMessage' => '',
  );

  public $components = array('Auth','Session');

  public function __construct(ComponentCollection $collection, $settings = array()) {
    $this->settings = array_replace_recursive($this->settings, (array)$settings);
    $this->controller = $collection->getController();
    $this->settings['userRoot'] = ( Configure::read('debug') > 0 ? true : $this->settings['userRoot'] );

    $this->controller->helpers[] = 'UtilCake.Permit'; // <-- Cargar helper UtilCake.Permit

    parent::__construct($collection, $this->settings);
  }

  public function user($data = null){
    $this->userProfile = $this->loadUserProfile();
    if($data == null){
      return $this->userProfile;
    }else{
      return ( isset($this->userProfile[$data]) ? $this->userProfile[$data] : false );
    }
  }

  public function isAuthorized($current = null, $showError = true){
    $authorized = $this->_authorized($current);
    if($showError){
      if($this->Auth->user('id')){
        return ( $authorized ? true : $this->error() );
      }else{
        //$this->Flash->error($this->Auth->authError);
        //return $this->controller->redirect($this->Auth->loginAction);
      }
    }
    return $authorized;
  }

  public function hasPermission($current = null){
    return $this->_authorized($current);
  }

  protected function _authorized($current = null){
    if($current == null){
      $current = array(
        'controller'=>$this->controller->params['controller'],
        'action'=>$this->controller->params['action']
      );
    }

    $this->userProfile = $this->loadUserProfile();
    $this->fileConfig = $this->loadFileConfig();

    if($this->settings['userRoot']===true  &&  (isset($this->userProfile['root'])  &&  $this->userProfile['root']===true )){
      // El usuario es "root", y tiene acceso a todo por defecto
      return true;
    }

    if($actionPermit = $this->getActionPermit($current)){
      if($actionPermit == 'public' or in_array('public', (array)$actionPermit)){
        // El permiso de la accion es "public", y cualquiera tiene acceso
        $this->controller->Auth->allow($current['action']);
        return true;
      }

      if(($actionPermit == 'loged' or in_array('loged', (array)$actionPermit)) && $this->Auth->user('id')){
        // El permiso de la accion es "loged", y tienen acceso solo los usuarios logueados
        return true;
      }

      if(is_array($actionPermit)){
        foreach ($actionPermit as $allowed_profile) {
          if(isset($this->userProfile[$allowed_profile]) and $this->userProfile[$allowed_profile]){
            return true;
          }
        }
      }else{
        if(isset($this->userProfile[$actionPermit]) and $this->userProfile[$actionPermit]){
          return true;
        }
      }
    }
    return false;
  }

  public function getActionPermit($current){
    $this->fileConfig = $this->loadFileConfig();
    if(isset($this->fileConfig[$current['controller']][$current['action']])){
      $actionPermit = $this->fileConfig[$current['controller']][$current['action']];
    }else{
      $actionPermit = false;
    }
    if($this->settings['defaultResponse']!==false && $actionPermit===false){
      return [$this->settings['defaultResponse']];
    }else{
      return $actionPermit;
    }
  }

  public function reloadPermits(){
    $this->fileConfig = $this->loadFileConfig(true);
    $this->loadUserProfile(true);
  }

  private function loadFileConfig($reload = false){
    if($this->settings['reload'] or $reload){
      Configure::load($this->settings['config']['file']);
  		$fileConfig = Configure::read($this->settings['config']['name']); // Leer Arreglo de Permisos
      $this->Session->write('Permit.fileConfig', $fileConfig);
    }else{
      if( !($fileConfig = $this->Session->read('Permit.fileConfig')) ){
        return $this->loadFileConfig(true);
      }
    }
    return $fileConfig;
	}

  private function loadUserProfile($reload = false){

    if(!$this->Auth->user()) return array();

    if($this->settings['reload'] or $reload){
      App::uses($this->settings['userModel'], 'Model');
      $userModel = new $this->settings['userModel']();
      $userModel->Behaviors->load('Containable');

      App::uses($this->settings['profileModel'], 'Model');
      $profileModel = new $this->settings['profileModel']();

      $user = $userModel->find('first',array(
        'conditions'=>array($userModel->alias.'.id' => $this->Auth->user('id')),
        'contain'=>array(
          $profileModel->alias,
        ),
      ));
      $userProfile = ( isset($user[$profileModel->alias]) ? $this->recreateProfile($user[$profileModel->alias]) : null );
      $this->Session->write('Permit.userProfile', $userProfile);
    }else{
      if( !($userProfile = $this->Session->read('Permit.userProfile')) ){
        return $this->loadUserProfile(true);
      }
    }
    return $userProfile;
  }

  public function recreateProfile($arrayProfile = array()){
		$aux = array();
		foreach($arrayProfile as $profile) {
			$aux[$profile['code']] = true;
		}
		if(!$this->settings['userRoot']){ unset($aux['root']); }
		return $aux;
	}

  public function error(){
    throw new ForbiddenException($this->settings['errorMessage']);
  }

}
