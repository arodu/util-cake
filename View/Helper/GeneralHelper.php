<?php
class GeneralHelper extends AppHelper {

	//public $helpers = array('Time');

	public $helpers = array('Html');

	private $meses = array(
			'corto' => array('1'=>'Ene','2'=>'Feb','3'=>'Mar','4'=>'Abr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Ago','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'),
			'largo' => array('1'=>'Enero','2'=>'Febrero','3'=>'Marzo','4'=>'Abril','5'=>'Mayo','6'=>'Junio','7'=>'Julio','8'=>'Agosto','9'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'),
		);
	private $dias = array('1'=>'Lunes','2'=>'Martes','3'=>'Miercoles','4'=>'Jueves','5'=>'Viernes','6'=>'Sabado','7'=>'Domingo');



	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
	}

	public function formatDateTime($date = null){
		return ( $date == null ? date() : strtotime($date) );
	}

	public function formatTime($date = null){
		return date("H:i:s", $this->formatDateTime($date));
	}

	public function dateTimeFormatView($date=null){
		$date = $this->formatDateTime($date);
		$strmes = $this->meses['corto'];
		//debug(date('d m Y h:i a',$date));
		return $strmes[date('n',$date)].' '.date('d Y, h:i a ',$date);
	}

	public function timeFormatView($date=null){
		$date = $this->formatDateTime($date);
		return date('h:i a ',$date);
	}

	public function dateTimeFormatPrint($date=null){
		$date = $this->formatDateTime($date);
		return date('d/m/Y h:ia',$date);
	}

	public function dateFormatPrint($date=null){
		$date = $this->formatDateTime($date);
		return date('d/m/Y',$date);
	}

	public function dateFormatView($date=null){
		$date = $this->formatDateTime($date);
		$strmes = $this->meses['corto'];
		//debug(date('d m Y h:i a',$date));
		return $strmes[date('n',$date)].' '.date('d Y',$date);
	}

	public function dateFormatComplete($date=null){
		$date = $this->formatDateTime($date);
		$strmes = $this->meses['largo'];
		//debug(date('d m Y h:i a',$date));
		return date('d',$date).' de '.$strmes[date('n',$date)].' de '.date('Y',$date);
	}

	public function niceDateFormatView($date=null){
		$dateF = $this->formatDateTime($date);
		$strmes = $this->meses['largo'];

		if( date('Ymd', $dateF) == date('Ymd', strtotime('now')) ){ // HOY
			return "Hoy";

		}elseif( date('Ymd', $dateF) == date('Ymd', strtotime('yesterday')) ){  // AYER
			return "Ayer";

		}elseif( date('Ymd', $dateF) >= date('Ymd', strtotime('-6 days')) &&  date('Ymd', $dateF) <= date('Ymd', strtotime('now'))){ // Esta Semana
			return $this->dias[ date('N',$dateF) ];

		}else{
			return $strmes[date('n',$dateF)].' 	'.date('d Y',$dateF);
		}
	}

	public function byteSize($bytes){
		$size = $bytes;
		if($bytes > 1024000000){
			$size = round($size / 1024000000, 1);
			$ext = 'GB';
		}else if($bytes > 1024000){
			$size = round($size / 1024000, 1);
			$ext = ' MB';
		}else if($bytes > 1024){
			$size = round($size / 1024, 1);
			$ext = ' KB';
		}else{
			$ext = ' Bytes';
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


	public function htmlTrim($pinfo) {
		/* $cortar = array('\n','\r','\t',' ','	','&nbsp;','<br />','<br>','<br/>');

		$lcut = count($cortar) - 1;
		$ucut = "0";
		$rcut = "0";
		$wiy = true;
		$so = false;

		while ($wiy) {

			if ($so) {
				$ucut = "0";
				$rcut = "0";
				$so = false;
			}

			if (!$cortar[$ucut]) {
				$so = true;
			} else {
				$pinfo = rtrim($pinfo);
				$bpinfol = strlen($pinfo);
				$tcut = $cortar[$ucut];
				$pinfo = rtrim($pinfo,"$tcut");
				$pinfol = strlen($pinfo);

				if ($bpinfol == $pinfol) {
					$rcut++;
					if ($rcut == $lcut) {
						$wiy = false;
					}
					$ucut++;
				} else {
					$so = true;
				}
			}
		} /**/
		return $pinfo;
	}



	public function soloLetras($texto){
		$palabras = explode(' ',$texto);
		$out = '';
		foreach ($palabras as $palabra) {
			$out .= $palabra[0].'.';
		}
		return $out;
	}

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

}

?>
