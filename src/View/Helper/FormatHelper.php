<?php

namespace UtilCake\View\Helper;

use Cake\View\Helper;

class FormatHelper extends Helper{

  public function bytes($size, $precision = 2){
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $base = log($size, 1024);
    return round(pow(1024, $base - floor($base)), $precision) .' '. $units[floor($base)];
  }

}
