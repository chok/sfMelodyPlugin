<?php
class sfMelody
{
  public static function getInstance($name, $config = array(), sfWebController $controller = null)
  {
    $default = sfConfig::get('app_oauth_'.$name, array());

    $config = array_merge($config, $default);

    if(is_null($controller))
    {
      $controller = sfContext::getInstance()->getController();
    }

    $provider = strtolower(isset($config['provider'])?$config['provider']:$name);
    $class = 'sf'.sfInflector::camelize($provider.'_o_auth');

    $key = isset($config['key'])?$config['key']:null;
    $secret = isset($config['secret'])?$config['secret']:null;

    $oauth = new $class($key, $secret, null, $config);
    $oauth->setName($name);
    $oauth->setController($controller);

    if(isset($config['token']))
    {
      $oauth->setToken($config['token']);
    }

    return $oauth;
  }

  public function connect($user)
  {
    $user->setAttribute('provider', $this->getName());
    $this->requestAuth($this->getController());
  }


  public function connect($user)
  {
    $this->getRequestToken();

    $user->setAttribute('token', $this->getToken());
    $user->setAttribute('provider', $this->getName());

    $this->requestAuth($this->getController());
  }
}