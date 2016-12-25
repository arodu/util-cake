<?php
App::uses('ModelBehavior', 'Model');
class UtilitiesBehavior extends ModelBehavior {
  
  //protected $_defaults = array(
  //  //'autoValidate' => false,
  //  //'errMsg' => 'The key/ID for %s must exist.',
  //  //'exclude' => array(),
  //);
  //
  //
  //public function setup(Model $model, $config = array()) {
  //  if (!isset($this->settings[$model->alias])) {
  //    $this->settings[$model->alias] = $this->_defaults;
  //  }
  //  $this->settings[$model->alias] = array_merge(
  //  $this->settings[$model->alias], (array)$config);
  //}
  
  /*
  public function hashPassword($password_field = 'password'){
    if (isset($this->data[$this->alias][$password_field])) {
      $passwordHasher = new BlowfishPasswordHasher();
      $this->data[$this->alias][$password_field] = $passwordHasher->hash($this->data[$this->alias][$password_field]);
    }
  }
  */
  
  /** Validation dateRange
   *  How to use:
   *  'notFuture' => array(
   *    'rule' => array('dateRange', array('max'=>'today')),
   *    'message' => 'Fecha Invalida',
   *  ),
   */
   public function dateRange(Model $Model, $check, $range){
     $strtotime_of_check = strtotime(reset($check));
     if(isset($range['min']) && $range['min']){
       $strtotime_of_min = strtotime($range['min']);
       if($strtotime_of_min > $strtotime_of_check) {
         return false;
       }
     }
     if(isset($range['max']) && $range['max']){
       $strtotime_of_max = strtotime($range['max']);
       if($strtotime_of_max < $strtotime_of_check) {
         return false;
       }
     }
     return false;
   }

  /*
  'password_confirm' => array(
    'equalToField' => array(
      'rule' => array('equalToField','password'),
    ),
  ),
  */
  /*
  public function equalToField($check,$otherfield){
    $check_value = reset($check);
    return ($check_value === $this->data[$this->alias][$otherfield]);
  }
  */

  /*
  'password_check' => array(
    'passwordCheck' => array(
      'rule' => array('passwordCheck'),
    ),
  ),
  */
  /*
  public function passwordCheck($check, $passwordfield='password'){
    $check_value = reset($check);
    $user = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$this->data[$this->alias]['id'])));
    $blowfish = new BlowfishPasswordHasher();
    return $blowfish->check($check_value, $user[$this->alias][$passwordfield]);
  }
  */
  
  
}