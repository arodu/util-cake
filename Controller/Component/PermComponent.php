<?php
App::uses('Component', 'Controller');
class PermComponent extends Component {

  private $userProfiles = array();
  private $user_id = null;

  private $User = null;
  private $Profile = null;

  public $settings = array(
    'profile_type' => 'single',  // single o multiple
    'userModel' => 'User',
    'profileModel' => 'Profile',
    'defaultLoged' => false,
    'userRoot' => false,
    'errorMessage' => '',
  );

  public $components = array('Auth','Session');

  public function __construct(ComponentCollection $collection, $settings = array()){
    $this->settings = array_merge($this->settings, (array)$settings);
    $this->Controller = $collection->getController();

    $this->settings['userRoot'] = ( Configure::read('debug') > 0 ? true : $this->settings['userRoot'] );
    //$this->Controller->helpers['Permiso'] = array('component'=>$this); // <-- Cargar helper Permiso

    $this->_loadModels();

    //$this->fileConfig();
    parent::__construct($collection, $this->settings);
  }

  private function _loadModels(){
    App::uses($this->settings['userModel'], 'Model');
		$this->User = new $this->settings['userModel']();

    App::uses($this->settings['profileModel'], 'Model');
		$this->Profile = new $this->settings['profileModel']();
  }

  private function getUserProfiles($user_id){
    if($this->settings['profile_type'] == 'single'){

    }


  }

}
