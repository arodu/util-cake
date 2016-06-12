<?php
App::uses('Component', 'Controller');
class PermComponent extends Component {

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

    //$this->fileConfig();
    parent::__construct($collection, $this->settings);
  }



}
