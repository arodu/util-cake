<?php
class GeneralHelper extends AppHelper {

	public $helpers = array('Html');

	private $months = array(
		'short' => array('1'=>'Ene','2'=>'Feb','3'=>'Mar','4'=>'Abr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Ago','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'),
		'large' => array('1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'),
	);
	private $days = array(
		'short' => array('1'=>'Lu','2'=>'Ma','3'=>'Mi','4'=>'Ju','5'=>'Vi','6'=>'Sa','7'=>'Do'),
		'large' => array('1'=>'Lunes','2'=>'Martes','3'=>'Miercoles','4'=>'Jueves','5'=>'Viernes','6'=>'Sabado','7'=>'Domingo'),
	);

	private $niceFormat = array(
		'today' => 'Hoy',
		'yesterday' => 'Ayer',
		'tomorrow' => 'Mañana',
	);

	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
	}

	private function _formatDateTime($datetime = null){
		return ( $datetime == null ? strtotime(date('c')) : strtotime($datetime) );
	}

	public function formatDate($format = null, $date = null){
		$date = $this->_formatDateTime($date);
		switch ($format) {
			case 'print':
					$out = date('d/m/Y',$date);
				break;

			case 'complete':
					$out = date('d',$date).' de '.$this->$months['large'][date('n',$date)].' de '.date('Y',$date);
				break;

			case 'view':
			default:
					$out = $this->months['short'][date('n',$date)].' '.date('d Y',$date);
				break;
		}
		return $out;
	}

	public function formatDateTime($format = null, $datetime = null){
		$datetime = $this->_formatDateTime($datetime);
		switch ($format) {
			case 'print':
					$out = date('d/m/Y h:ia',$datetime);
				break;

			case 'view':
			default:
					$out = $this->months['short'][date('n',$datetime)].' '.date('d Y, h:i a ',$datetime);
				break;
		}
		return $out;
	}

	public function formatTime($format = null, $time = null){
		$time = $this->_formatDateTime($time);
		switch ($format) {
			case 'print':
			case 'view':
			default:
					$out = date('h:i a',$date);
				break;
		}
		return $out;
	}

	public function dateCompare($date, $to_compare = null){
		$to_compare = date('Ymd',$this->_formatDateTime($to_compare));
		$date = date('Ymd',$this->_formatDateTime($date));
		if($date == $to_compare){
			return '=';
		}else{
			if($date > $to_compare){
				return '>';
			}else{
				return '<';
			}
		}
	}
  
  public function get_age($birthdate){
    $diff = abs(time() - strtotime($birthdate));
    return  floor((($diff / 3600) / 24) / 360);
  }

	/*********************************************************************************************************/


	public function niceDateFormatView($date=null){
		$dateF = $this->formatDateTime($date);
		$strmes = $this->meses['largo'];

		if( date('Ymd', $dateF) == date('Ymd', strtotime('now')) ){ // HOY
			return "Hoy";

		}elseif( date('Ymd', $dateF) == date('Ymd', strtotime('yesterday')) ){  // AYER
			return "Ayer";

		}elseif( date('Ymd', $dateF) >= date('Ymd', strtotime('-6 days')) &&  date('Ymd', $dateF) <= date('Ymd', strtotime('now'))){ // Esta Semana
			return $this->days[ date('N',$dateF) ];

		}else{
			return $strmes[date('n',$dateF)].' 	'.date('d Y',$dateF);
		}
	}

	public function byteSize($bytes){
		$size = $bytes;
		$ext = ' Bytes';
		if($bytes > 1024000000){
			$size = round($size / 1024000000, 1);
			$ext = 'GB';
		}else if($bytes > 1024000){
			$size = round($size / 1024000, 1);
			$ext = ' MB';
		}else if($bytes > 1024){
			$size = round($size / 1024, 1);
			$ext = ' KB';
		}

		return $size.$ext;
	}

	public function fileType($fileName){
		$name = explode('.', $fileName);
		return end($name);
	}

	public function esImagen($fileName){
		$type = $this->fileType($fileName);
		if($type == 'jpg' || $type == 'png' || $type == 'gif' || $type == 'jpej'){
			return true;
		}else{
			return false;
		}
	}

	public function iconFileType($fileName){
		$type = $this->fileType($fileName);
		$icon = '';

		switch ($type) {
			case 'doc':
			case 'docx':
							$icon = 'fa-file-word-o';
							break;
			case 'gif':
			case 'png':
			case 'jpg':
							$icon = 'fa-file-image-o';
							break;
			case 'pdf':
							$icon = 'fa-file-pdf-o';
							break;
			case 'odt':
							$icon = 'fa-file';
							break;
			case 'txt':
							$icon = 'fa-file-text-o';
							break;

			default:
							$icon = 'fa-file-o';
							break;
		}

		return $icon;

	}

	public function soloLetras($texto){
		$palabras = explode(' ',$texto);
		$out = '';
		foreach ($palabras as $palabra) {
			$out .= $palabra[0].'.';
		}
		return $out;
	}

	/*
	public function userFoto($tipo_foto = null, $id = null, $updated_foto = null){

		return false;

		$foto = $this->requestAction('/usuarios/existeFoto/'.$tipo_foto.'/'.$id);

		if($updated_foto == null){
			$updated_foto = $this->requestAction('usuarios/getUpdatedFoto/'.$id);
		}

		if($foto){
			$code = urlencode( convert_uuencode($updated_foto.'$'.$tipo_foto.'$'.$id) );
			return $this->Html->url(array('controller'=>'usuarios','action'=>'getFoto', $code, 'admin'=>false ),true);
		}else{

			$imageBaseUrl = DS.Configure::read('App.imageBaseUrl');
			switch ($tipo_foto) {
				case 'xs':	$file = 'user.xs.png'; break;

				case 'md':	$file = 'user.md.png'; break;

				default:	$file = 'user.default.png'; break;
			}

			return $imageBaseUrl.$file;
		}
	}
  */

	function arrayYears( $options = array() ){
	  $out = array();

	  $maxYear = (isset($options['maxYear']) ? $options['maxYear'] : date('Y') );
	  $minYear = (isset($options['minYear']) ? $options['minYear'] : date('Y')-100 );
		$interval = (isset($options['interval']) ? $options['interval'] : 1 );
		$order = (isset($options['order']) ? $options['order'] : 'desc' );

	  if($order == 'desc'){
			for( $i = $maxYear; $i >= $minYear; $i = $i - $interval ){
					$out["$i"] = "$i";
			}
	  }elseif($order == 'asc'){
			for( $i = $minYear; $i <= $maxYear; $i = $i + $interval ){
					$out["$i"] = "$i";
			}
	  }
		return $out;
	}

	function progress_bar($value){
		$color = 'progress-bar-primary';
		if($value < 100){ $color = 'progress-bar-info'; }
		if($value < 90){ $color = 'progress-bar-success'; }
		if($value < 40){ $color = 'progress-bar-warning'; }
		if($value < 20){ $color = 'progress-bar-danger'; }
		echo '<div class="progress progress_xs">';
		echo '<div aria-valuenow="'.$value.'" style="width: '.$value.'%;" class="progress-bar '.$color.'" role="progressbar" data-transitiongoal="'.$value.'">';
		echo '<small>'.$value.'%</small>';
		echo '</div>';
		echo '</div>';
	}

}

?>
