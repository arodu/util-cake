<?php
class PermitHelper extends AppHelper {

  public $helpers = array('Html','Form');
  private $permisosBuffer = null;
  public $settings = array(
    'returnDeny' => '',
  );

  public function __construct(View $View, $settings = array()) {
    parent::__construct($View, $settings);
    App::uses('UtilCake.Permit', 'Component');
    $collection = new ComponentCollection();
    $this->component = new PermitComponent($collection, $settings['component_settings']);
  }

  private function rebuildUrl($url){
    $here = array(
      'controller'=>$this->params['controller'],
      'action'=>$this->params['action'],
    );
    return array_merge($here, $url);
  }

  public function link($title, $url = array(), $options = array(), $confirmMessage = false) {
    $url = $this->rebuildUrl($url);
    if($this->component->isAuthorized($url,false)){
      if(isset($options['tag']) and $options['tag']){
        $tag = $this->create_tag($options['tag']);
        unset($options['tag']);
        return $this->Html->tag(
        $tag['name'],
        $this->Html->link($title, $url, $options, $confirmMessage),
        array($tag['options'])
      );
    }else{
      return $this->Html->link($title, $url, $options, $confirmMessage);
    }
  }else{
    return $this->settings['returnDeny'];
  }
  }

  private function create_tag($option_tag){
    if(is_array($option_tag)){
      $tag['name'] = $option_tag['name'];
      unset($option_tag['name']);
      $tag['options'] = $option_tag;
    }else{
      $tag['name'] = $option_tag;
      $tag['options'] = array();
    }
    return $tag;
  }

  public function postLink($title, $url = null, $options = array(), $confirmMessage = false) {
    $url = $this->rebuildUrl($url);
    if($this->component->isAuthorized($url,false)){
      return $this->Form->postLink($title, $url, $options, $confirmMessage);
    }else{
      return $this->settings['returnDeny'];
    }
  }

  public function hasPermission($profiles = array()){
    $this->userProfile = $this->component->user();
    if(!is_array($profiles)){ $profiles = array($profiles); }
    foreach ($profiles as $profile) {
      if(isset($this->userProfile[$profile]) and $this->userProfile[$profile]){
        return true;
      }
    }
    return false;
  }

}
