<?php
App::uses('FormHelper', 'View/Helper');
class bsFormHelper extends FormHelper {

	public function create($model = null, $options = array()) {
		if(isset($options['inputDefaults']) && $options['inputDefaults']){
			$options['inputDefaults'] += array( 'class'=>'form-control', 'div' => array('class'=>'form-group') );
		}else{
			$options['inputDefaults'] = array( 'class'=>'form-control', 'div' => array('class'=>'form-group') );
		}
		return parent::create($model, $options);
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

	public function bsStatic( $fieldName, $options = array()){

		echo '<div class="form-group">
	    <label class="col-sm-2 control-label">Email</label>
	    <div class="col-sm-10">
	      <p class="form-control-static">email@example.com</p>
	    </div>
	  </div>';
	}


	protected function _getTextLabel($fieldName, $options){

		if( isset($options['textLabel']) && $options['textLabel'] ){
			return $options['textLabel'];
		}

		if(isset($options['label'])){
			if(is_array($options['label'])){
				$textLabel = $options['label']['text'];
			}else{
				$textLabel = $options['label'];
			}
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

	public function input($fieldName, $options = array()) {
		$this->setEntity($fieldName);
		$options = $this->_parseOptions($options);

		switch ($options['type']) {
			case 'checkbox':
			case 'bsCheckbox':
					return $this->bsCheckbox($fieldName, $options);
					break;

			case 'static':
					return $this->bsStatic($fieldName, $options);
					break;

			case 'datetime':
					break;
		}

		return parent::input($fieldName, $options);
	}



}
