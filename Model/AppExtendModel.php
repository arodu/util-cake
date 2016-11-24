<?php
App::uses('Model', 'Model');
class AppExtendModel extends Model {
  
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
    $field = reset($check);
    return ($this->data[$this->alias][$field] === $this->data[$this->alias][$otherfield]);
  }

  /*
  'password_check' => array(
    'passwordCheck' => array(
      'rule' => array('passwordCheck'),
    ),
  ),
  */
  public function passwordCheck($check, $passwordfield='password'){
    $field = reset($check);
    $user = $this->find('first',array('conditions'=>array($this->alias.'.id'=>$this->data[$this->alias]['id'])));
    $blowfish = new BlowfishPasswordHasher();
    return $blowfish->check($this->data[$this->alias][$field], $user[$this->alias][$passwordfield]);
  }

}