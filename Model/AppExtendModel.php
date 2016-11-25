<?php
App::uses('Model', 'Model');
class AppExtendModel extends Model {
  
  public function hashPassword($password_field = 'password'){
    if (isset($this->data[$this->alias][$password_field])) {
      $passwordHasher = new BlowfishPasswordHasher();
      $this->data[$this->alias][$password_field] = $passwordHasher->hash($this->data[$this->alias][$password_field]);
    }
  }
  
  /** Validation dateRange
   *  How to use:
   *  'notFuture' => array(
   *    'rule' => array('dateRange', array('max'=>'today')),
   *    'message' => 'Fecha Invalida',
   *  ),
   */
  public function dateRange($check, $range){
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
    return true;
  }

  /*
  'password_confirm' => array(
    'equalToField' => array(
      'rule' => array('equalToField','password'),
    ),
  ),
  */
  public function equalToField($check,$otherfield){
    $check_value = reset($check);
    return ($check_value === $this->data[$this->alias][$otherfield]);
  }

  /*
  'password_check' => array(
    'passwordCheck' => array(
      'rule' => array('passwordCheck'),
    ),
  ),
  */
  public function passwordCheck($check, $passwordfield='password'){
    $check_value = reset($check);
    $user = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$this->data[$this->alias]['id'])));
    $blowfish = new BlowfishPasswordHasher();
    return $blowfish->check($check_value, $user[$this->alias][$passwordfield]);
  }
  
}