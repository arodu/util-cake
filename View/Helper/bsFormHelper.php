<?php
  /**
   * bsFormHelper - Bootstrap Form Helper
   *
   */

App::uses('FormHelper', 'View/Helper');
class bsFormHelper extends FormHelper {
  
  public $form_type = 'default'; // default, horizontal, inline
  
  public $settings = array(
    'col-left'=>'col-sm-2',
    'col-right'=>'col-sm-10',
  );
  
  public function create($model = null, $options = array()){
    $form_class = (isset($options['class']) ? $options['class'] : 'form-default');
    if(isset($options['inputDefaults']) && $options['inputDefaults']){
      $options['inputDefaults'] += $this->_selectFormType($form_class);
    }else{
      $options['inputDefaults'] = $this->_selectFormType($form_class);
    }
    return parent::create($model, $options);
  }
  
  public function setColLeft($left = 'col-sm-2'){
    $this->settings['col-left'] = $left;
  }
  
  public function setColRight($right = 'col-sm-10'){
    $this->settings['col-right'] = $right;
  }
  
  protected function _selectFormType($form_class = ''){
    $inputDefaults = array(
      'label' => array('class'=>'control-label'),
      'class'=>'form-control',
      'div'=>array('class'=>'form-group'),
      'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-block')),
    );
    
    if(strpos($form_class, 'form-horizontal')!==false){
      $this->form_type = 'horizontal';
      
      $inputDefaults = array(
        'format' => array('before', 'label', 'between', 'input', 'error', 'after'),
        'label' => array('class'=>$this->settings['col-left'].' control-label'),
        'div' => array('class'=>'form-group'),
        'between' => '<div class="'.$this->settings['col-right'].'">',
        'after' => '</div>',
        'class' => 'form-control',
        'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-block')),
      );
      
    }elseif(strpos($form_class, 'form-inline')!==false){
      $this->form_type = 'inline';
    }else{
      $this->form_type = 'default';
    }
    
    return $inputDefaults;
  }
  
  protected function fixOptions($options){
    $out = array();
    if(isset($options['label'])){
      if(!is_array($options['label'])){
        $options['label'] = array('text'=>$options['label']);
      }
      $out['label'] = array_merge_recursive($this->_inputDefaults['label'], $options['label']);
    }
    
    return array_merge($options, $out);
  }
  
  protected function mergeClass($class_1, $class_2){
    return implode(' ', array_unique(array_merge(explode(' ', $class_1), explode(' ', $class_2))));
  }
  
  public function input($fieldName, $options = array()) {
    $this->setEntity($fieldName);
    $options = $this->fixOptions($options);
    $options = $this->_parseOptions($options);

    switch ($options['type']) {
      case 'checkbox':
        $options['class'] = '';
        if($this->form_type != 'horizontal'){
          $options['style'] = 'margin-right:6px;';
          $options['format'] = array('before', 'input', 'between', 'label', 'error', 'after');
        }
      break;
      
      case 'select':
        if(isset($options['multiple']) && ($options['multiple'] === 'checkbox')){
          $options['class'] = '';
        }
        break;
        
      case 'radio':
          //debug($options);
        break;
        
      case 'static_input':
      case 'static':
        return $this->static_input($fieldName, $options){;
      break;
        
      
      case 'time':
      case 'date':
      case 'datetime':
        $options['separator'] = '&nbsp;';
        $options['style'] = array('display:inline-block;width:auto;');
      break;
    }
    
    if($this->isFieldError($fieldName)){
      $options['div']['class'] = $this->mergeClass($options['div']['class'],'has-error') ;
    }
    
    return parent::input($fieldName, $options);
  }
  
  public function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $attributes = array()){
    $dateTime = parent::dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
    $inter_div = ( isset($attributes['inter_div']) ? $attributes['inter_div'] : '');
    
    if($this->form_type == 'inline'){
      return $dateTime;
    }elseif($this->form_type == 'horizontal'){
      return $this->Html->tag('div', $dateTime, $inter_div);
    }else{
      return $this->Html->tag('div', $dateTime, $inter_div);
    }
  }
  
  public function static_input($fieldName, $options){ 
    $options = array_merge($options, array('class'=>'form-control-static'));
    $options = $this->_initInputField($fieldName, $options);
    $out = $this->Html->tag('span', $options['value'], array('class'=>$options['class']));
    if(isset($options['hidden_value']) && $options['hidden_value']){
      $out .= parent::input($fieldName, array('type'=>'hidden', 'value'=>$options['hidden_value']));
    }
    return $out;
  }
  
  
  public function bsCheckbox( $fieldName, $options = array() ) {

    $options['type'] = 'checkbox';

    $options = $this->_parseOptions($options);
    $textLabel = $this->_getTextLabel($fieldName,$options);

    $divOptions = $this->_divOptions($options);
    unset($options['div']);

    $ck_options = array(
        'div' => array('tag'=>'label','class'=>false),
        'label' => false,
        'type' => 'checkbox',
        'between' => $options['between'].$textLabel,
        'class'=>'',
      );

    $options = array_merge($options,$ck_options);

    $checkbox = parent::input($fieldName, $options);

    if($divOptions){
      $tag = $divOptions['tag'];
      unset($divOptions['tag']);
      $divOptions = $this->addClass($divOptions, $options['type']);
      return $this->Html->tag($tag, $checkbox, $divOptions);
    }
    return $checkbox;
  }
  
  
  protected function _getTextLabel($fieldName, $options){
    if( isset($options['textLabel']) && $options['textLabel'] ){
      return $options['textLabel'];
    }

    if(isset($options['label']['text'])){
      $textLabel = $options['label']['text'];
    }elseif(isset($options['label']) && !is_array($options['label'])){
      $textLabel = $options['label'];
    }else{
      if (strpos($fieldName, '.') !== false) {
        $fieldElements = explode('.', $fieldName);
        $text = array_pop($fieldElements);
      } else {
        $text = $fieldName;
      }
      if (substr($text, -3) === '_id') {
        $text = substr($text, 0, -3);
      }
      $textLabel = __(Inflector::humanize(Inflector::underscore($text)));
    }
    $textLabel = ( $textLabel === false ? '' : $textLabel );

    return $textLabel;
  }
  
  public function submit($caption = null, $options = array()) {
    $options['class'] = ( isset($options['class']) && $options['class'] ? $options['class'] : 'btn btn-primary' );
    $options['div'] = ( isset($options['div']) && $options['div'] ? $options['div'] : false );
    return parent::submit($caption, $options);
  }
  
  public function reset($caption = null, $options = array()) {
    $options['class'] = ( isset($options['class']) && $options['class'] ? $options['class'] : 'btn btn-default' );
    return parent::reset($caption, $options);
  }
  
  public function button($title, $options = array()) {
    $options['class'] = ( isset($options['class']) && $options['class'] ? $options['class'] : 'btn btn-default' );
    return parent::button($title, $options);
  }


  /*
	public function selectMultilevel($fieldName, $options = array() ) {

		if (func_num_args() > 2) {
			$selectOptions = $options;
			$attributes = func_get_arg(2);
		}else{
			$selectOptions = $options['options'];
			unset($options['options']);
			$attributes = $options;
		}

		$attributes = $this->_initInputField($fieldName, array_merge( (array)$attributes, array('secure' => self::SECURE_SKIP) ));

		$space = 15;
		if(isset($attributes['space']) && $attributes['space']){
			$space = $attributes['space'];
			unset($attributes['space']);
		}

		$out = $this->_multilevelOptions($selectOptions, 0, $space);

		if(isset($attributes['empty']) && $attributes['empty']){
			$out = $this->Html->useTag('selectoption','','',$attributes['empty']).$out;
			unset($attributes['empty']);
		}

		return $this->Html->tag('select',$out, $attributes);
	}

	protected function _multilevelOptions($selectOptions = array() , $level = 0, $space = 15){
		$out = array();

		$attributes = '';
		if($level*$space > 0){ // Acomodar ESTO
			$attributes = ' style="padding-left:'.($level*$space).'px"';
		}

		foreach ($selectOptions as $key => $option) {

			if(is_array($option)){
				$out[] = $this->Html->useTag('optiongroup',$key,$attributes).$this->Html->useTag('optiongroupend');
				$out[] = $this->_multilevelOptions($option, $level+1, $space);
			}else{
				$out[] = $this->Html->useTag('selectoption',$key,$attributes,$option);
			}
		}
		return implode('', $out);
	}
 */

}
