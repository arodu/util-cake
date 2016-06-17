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
    $this->Controller = $collection->getController();
    $this->settings['userRoot'] = ( Configure::read('debug') > 0 ? true : $this->settings['userRoot'] );

    $this->Controller->helpers[] = 'UtilCake.Permit'; // <-- Cargar helper UtilCake.Permit

    parent::__construct($collection, $this->settings);
  }


  public function reloadPermits(){
    $this->Session->write('Permit.fileConfig', $this->loadFileConfig());
    $this->Session->write('Permit.userProfile', $this->loadUserProfile());
  }

  public function loadPermits($reload = false){
    if($this->settings['reload']){
      $this->reloadPermits();
    }

    if( !($this->fileConfig = $this->Session->read('Permit.fileConfig') )){
      $this->fileConfig = $this->loadFileConfig();
      $this->Session->write('Permit.fileConfig', $this->fileConfig);
    }
    if( !($this->userProfile = $this->Session->read('Permit.userProfile') )){
      $this->userProfile = $this->loadUserProfile();
      $this->Session->write('Permit.userProfile', $this->userProfile);
    }

  }

  public function isAuthorized($current = null, $error = true){
    $authorized = $this->_authorized($current);
    if($error){
      return ( $authorized ? true : $this->error() );
    }else{
      return $authorized;
    }
  }

  public function hasPermission($current = null){
    return $this->_authorized($current);
  }

  private function _authorized($current = null){
    if($current == null){
      $current = array(
        'controller'=>$this->Controller->params['controller'],
        'action'=>$this->Controller->params['action']
      );
    }

    $this->loadPermits();

    if( $this->settings['userRoot']===true  &&  isset($userProfile['root'])  &&  $userProfile['root']===true ){
      // El usuario es "root", y tiene acceso a todo por defecto
      return true;
    }

    if($actionPermit = $this->getActionPermit($current)){
      if($actionPermit == 'public' or in_array('public', (array)$actionPermit)){
        // El permiso de la accion es "public", y cualquiera tiene acceso
        $this->Controller->Auth->allow($current['action']);
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
    $this->loadPermits();
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

  /*
  private function __authorized($current = null){

    if($current == null){
      $current = array(
        'controller'=>$this->Controller->params['controller'],
        'action'=>$this->Controller->params['action']
      );
    }

    $userPerfil = $this->recreatePerfil($this->Auth->user('Perfil'));

    $currentPermisos = $this->getPermiso($current);

    //debug($userPerfil);
    //debug($currentPermisos);

    if( $this->settings['userRoot']===true  &&  isset($userPerfil['root'])  &&  $userPerfil['root']===true ){
      // Si el usuario es ROOT tiene acceso
      return true;
    }

    if($currentPermisos == 'public'){  // Si el acceso el publico, el usuario tiene acceso
      return true;
    }elseif(is_array($currentPermisos)){
      foreach ($currentPermisos as $permiso){  // Si el acceso el publico, el usuario tiene acceso
        if($permiso == 'public'){
          return true;
        }
        if(isset($userPerfil[$permiso]) && $userPerfil[$permiso]){ // Si el usuario tiene permiso , el usuario tiene acceso
          return true;
        }
      }
    }
    return false;
  }
  */



  private function loadFileConfig(){
		Configure::load($this->settings['config']['file']);
		$fileConfig = Configure::read($this->settings['config']['name']); // Leer Arreglo de Permisos
    return $fileConfig;
	}

  private function loadUserProfile(){
    App::uses($this->settings['userModel'], 'Model');
    $userModel = new $this->settings['userModel']();
    $userModel->Behaviors->load('Containable');

    App::uses($this->settings['profileModel'], 'Model');
    $profileModel = new $this->settings['profileModel']();

    $userProfile = $userModel->find('first',array(
      'conditions'=>array($userModel->alias.'.id' => $this->Auth->user('id')),
      'contain'=>array(
        $profileModel->alias,
      ),
    ));

    if(isset($userProfile[$profileModel->alias])){
      return $this->recreateProfile($userProfile[$profileModel->alias]);
    }else{
      return null;
    }

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



  /*
  public function get_users_profiles(){
    App::uses($this->settings['UserModel'], 'Model');
    $userModel = new $this->settings['UserModel']();
    $userModel->Behaviors->load('Containable');

    App::uses($this->settings['profileModel'], 'Model');
    $profileModel = new $this->settings['profileModel']();
    $profileModel->Behaviors->load('Containable');

    $user = $userModel->find('first',array(
      'conditions'=>array($userModel->alias.'.id' => $this->Auth->user('id')),
      'contain'=>array(
        $profileModel->alias,
      ),
    ));

    if($this->settings['profile_type'] == 'single'){
      debug($user);
    }elseif($this->settings['profile_type'] == 'double'){
      debug($user);
    }else{
      throw new ForbiddenException($this->settings['errorMessage']);
    }

  }



  */


}
