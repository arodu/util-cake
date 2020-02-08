<?php
namespace UtilCake\Thumb;

use Cake\Core\InstanceConfigTrait;
//use Cake\Core\Configure;
//use Cake\Routing\Router;
//use Cake\Utility\Inflector;

class Thumb{

  use InstanceConfigTrait;

  private $image;
  private $type;
  private $mime;
  private $width;
  private $height;

  protected $_defaultConfig = [];

  public function __construct(array $config = []){
    $this->setConfig($config);
  }

  //---Método de leer la imagen
  public function loadImage($name) {

    //---Tomar las dimensiones de la imagen
    $info = getimagesize($name);

    $this->width = $info[0];
    $this->height = $info[1];
    $this->type = $info[2];
    $this->mime = $info['mime'];

    //---Dependiendo del tipo de imagen crear una nueva imagen
    switch($this->type){
      case IMAGETYPE_JPEG:
        $this->image = imagecreatefromjpeg($name);
      break;

      case IMAGETYPE_GIF:
        $this->image = imagecreatefromgif($name);
      break;

      case IMAGETYPE_PNG:
        $this->image = imagecreatefrompng($name);
      break;

      default:
        trigger_error('No se puede crear un thumbnail con el tipo de imagen especificada', E_USER_ERROR);
      break;
    }

  }

  //---Método de guardar la imagen
  public function save($name, $quality = 100, $type = false) {

    //---Si no se ha enviado un formato escoger el original de la imagen
    $type = ($type) ? $type : $this->type;

    //---Guardar la imagen en el tipo de archivo correcto
    switch($type){

      case IMAGETYPE_JPEG:
        imagejpeg($this->image, $name . image_type_to_extension($type), $quality);
      break;

      case IMAGETYPE_GIF:
        imagegif($this->image, $name . image_type_to_extension($type));
      break;

      case IMAGETYPE_PNG:
        $pngquality = floor($quality / 100 * 9);
        imagepng($this->image, $name . image_type_to_extension($type), $pngquality);
      break;

      default:
        trigger_error('No se ha especificado un formato de imagen correcto', E_USER_ERROR);

    }

  }

  //---Método de mostrar la imagen sin guardarla
  public function show($type = false, $base64 = false) {

    //---Si no se ha enviado un formato escoger el original de la imagen
    $type = ($type) ? $type : $this->type;

    if($base64) ob_start();

    //---Mostrar la imagen dependiendo del tipo de archivo
    switch($type){

      case IMAGETYPE_JPEG:
        imagejpeg($this->image);
      break;

      case IMAGETYPE_GIF:
        imagegif($this->image);
      break;

      case IMAGETYPE_PNG:
        $this->prepareImage($this->image);
        imagepng($this->image);
      break;

      default:
        trigger_error('No se ha especificado un formato de imagen correcto', E_USER_ERROR);
        exit;

    }

    if($base64) {

      $data = ob_get_contents();

      ob_end_clean ();

      return 'data:' . $this->mime . ';base64,' . base64_encode($data);

    }

  }

  //---Método de redimensionar la imagen sin deformarla
  public function resize($value, $prop){

    //---Determinar la propiedad a redimensionar y la propiedad opuesta
    $prop_value = ($prop == 'width') ? $this->width : $this->height;
    $prop_versus = ($prop == 'width') ? $this->height : $this->width;

    //---Determinar el valor opuesto a la propiedad a redimensionar
    $pcent = $value / $prop_value;
    $value_versus = $prop_versus * $pcent;

    //---Crear la imagen dependiendo de la propiedad a variar
    $image = ($prop == 'width') ? imagecreatetruecolor($value, $value_versus) : imagecreatetruecolor($value_versus, $value);  

    //---Tratar la imagen
    if($this->type == IMAGETYPE_GIF || $this->type == IMAGETYPE_PNG) $this->prepareImage($image); 

    //---Hacer una copia de la imagen dependiendo de la propiedad a variar
    switch($prop){

      case 'width':

        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $value, $value_versus, $this->width, $this->height);

      break;

      default:

        imagecopyresampled($image, $this->image, 0, 0, 0, 0, $value_versus, $value, $this->width, $this->height);

    }

    //---Actualizar la imagen y sus dimensiones
    $this->width = imagesx($image);
    $this->height = imagesy($image);
    $this->image = $image;

  }

  //---Método de extraer una sección de la imagen sin deformarla
  public function crop($cwidth, $cheight, $pos = 'center') {

    $pcent = min($this->width / $cwidth, $this->height / $cheight);
    $bigw = (int) ($pcent * $cwidth);
    $bigh = (int) ($pcent * $cheight);

    //---Crear la imagen
    $image = imagecreatetruecolor($cwidth, $cheight);

    //---Tratar la imagen
    if($this->type == IMAGETYPE_GIF || $this->type == IMAGETYPE_PNG) $this->prepareImage($image);

    //---Dependiendo de la posición copiar
    switch($pos){

      case 'left':

        imagecopyresampled($image, $this->image, 0, 0, 0, abs(($this->height - $bigh) / 2), $cwidth, $cheight, $bigw, $bigh);

      break;

      case 'right':

        imagecopyresampled($image, $this->image, 0, 0, $this->width - $bigw, abs(($this->height - $bigh) / 2), $cwidth, $cheight, $bigw, $bigh);

      break;

      case 'top':

        imagecopyresampled($image, $this->image, 0, 0, abs(($this->width - $bigw) / 2), 0, $cwidth, $cheight, $bigw, $bigh);

      break;

      case 'bottom':

        imagecopyresampled($image, $this->image, 0, 0, abs(($this->width - $bigw) / 2), $this->height - $bigh, $cwidth, $cheight, $bigw, $bigh);

      break;

      default:

        imagecopyresampled($image, $this->image, 0, 0, abs(($bigw - $this->width) / 2), abs(($bigh - $this->height) / 2), $cwidth, $cheight, $bigw, $bigh);

    }

    $this->width = $cwidth;
    $this->height = $cheight;
    $this->image = $image;

  }

  //---Método privado de tratar las imágenes antes de mostrarlas
  private function prepareImage($image){

    //---Dependiendo del tipo de imagen
    switch($this->type){

      case IMAGETYPE_GIF:

        $background = imagecolorallocate($image, 0, 0, 0);
        imagecolortransparent($image, $background);

      break;

      case IMAGETYPE_PNG:

        imagealphablending($image, FALSE);
        imagesavealpha($image, TRUE);

      break;

    }

  }

}