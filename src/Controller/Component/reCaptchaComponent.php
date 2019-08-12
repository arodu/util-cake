<?php
namespace UtilCake\Controller\Component;

use Cake\Controller\Component;
//use Cake\Controller\ComponentRegistry;
use Cake\Event\Event;

/**
 *  configuration file example (config/permits.php):
 *    return ['Permits'=>[
 *      'PluginName.ControllerName'=>[
 *        'ActionName' => ['role list'],
 *      ],
 *      'Users'=>[
 *        'index' => ['public'],
 *        'view' => ['public', 'admin'],
 *        'edit' => 'public',
 *      ],
 *    ]];
 */

class reCaptchaComponent extends Component{

  protected $_defaultConfig = [
    'secret_key' => null,
    'public_key' => null,
    'version' => 'v3',

    'input_name' => 'recaptcha_response',
    'recaptcha_min_score' => 0.5,
    'recaptcha_url' => 'https://www.google.com/recaptcha/api/siteverify?secret=%s&response=%s', //sprintf(recaptcha_url, secret_key, recaptcha_response);
  ];

  //protected $_permit;

  //public function __construct(ComponentRegistry $registry, array $config = []){
    //$this->setConfig($config);
    //$this->components = [$this->getConfig('component')];
    //parent::__construct($registry, $config);
  //}

  public function beforeRender(Event $event){
    $controller = $event->getSubject();

    $controller->set('google_recaptcha', [
        'public_key' => $this->getConfig('public_key'),
        'input_name' => $this->getConfig('input_name'),
      ]);
  }

  public function verify($request){
    if ($request->is('post')) {
      $config = $this->getConfig();
      $data = $request->getData();
      if (isset($data[$config['input_name']])) {

        // Build POST request:
        $url = sprintf($config['recaptcha_url'], $config['secret_key'], $data[$config['input_name']]);

        // Make and decode POST request:
        $recaptcha = file_get_contents($url);
        $recaptcha = json_decode($recaptcha);

        // Take action based on the score returned:
        if ($recaptcha->success && $recaptcha->score >= $config['recaptcha_min_score']) {
          return true;
        }
      }
    }
    return false;
  }

  public function getPublicKey(){
    return $this->getConfig('public_key');
  }

}
