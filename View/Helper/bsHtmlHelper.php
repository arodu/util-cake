<?php
  /**
   * bsHtmlHelper - Bootstrap Html Helper
   *
   */

App::uses('HtmlHelper', 'View/Helper');
class bsHtmlHelper extends HtmlHelper {
  
  
  protected function mergeClass($class_1 = null, $class_2 = null){
    $class_1 = ( $class_1 == null ? '' : $class_1);
    $class_2 = ( $class_2 == null ? '' : $class_2);
    return implode(' ', array_unique(array_merge(explode(' ', $class_1), explode(' ', $class_2))));
  }
  
  public function dropdown($fieldName, $options = array()){
    
    $links = ( isset($options['links']) ? $options['links'] : array() );
    unset($options['links']);
    
    // echo $this->Html->tag('span', 'Hello World.', array('class' => 'welcome'));
    $divOptions = (isset($options['div']) ? $options['div'] : array());
    unset($options['div']);
    
    $options['class'] = $this->mergeClass(@$options['class'], 'dropdown-toggle');
    $options['data-toggle'] = 'dropdown';
    $options['aria-expanded'] = 'false';
    $into = $this->tag('button', $fieldName, $options);
    
    $into .= '<ul class="dropdown-menu" role="menu">';
    foreach ($links as $link){
      if(!is_array($link)){
        if($link == ':separator:' || $link == ':divider:'){
          $into .= '<li class="divider"></li>';
        }else{
          $into .= '<li>'.$link.'<li>';
        }
      }
    }
    $into .= '</ul>';
    
    $divOptions['class'] = (isset($divOptions['class']) ? $this->mergeClass($divOptions['class'], 'btn-group') : 'btn-group');
    $div = $this->tag('div', $into, $divOptions);
    return $div;
  }
  

  
  
}