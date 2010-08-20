<?php
class sfMelody
{
  public static function getInstance($name, $config = array(), sfWebController $controller = null)
  {
    $default = sfConfig::get('app_melody_'.$name, array());

    $config = array_merge($config, $default);

    if(is_null($controller))
    {
      $controller = sfContext::getInstance()->getController();
    }

    $provider = strtolower(isset($config['provider'])?$config['provider']:$name);
    $class = 'sf'.sfInflector::camelize($provider.'_melody');

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

  public static function getTokenStatuses()
  {
    $reflection = new ReflectionClass('Token');

    $statuses = array();
    foreach($reflection->getConstants() as $constant => $value)
    {
      if(strpos($constant, 'STATUS_'))
      {
        $statuses[$constant] = $value;
      }
    }

    return $statuses;
  }

  public static function deleteTokens($service = null, $user = null, $status = null)
  {
    return self::execute('deleteTokens', array($service, $user, $status));
  }

  public static function execute($method, $arguments)
  {
    $callable = array(self::getTokenOperationByOrm(), $method);

    return call_user_func_array($callable, $arguments);
  }

  public static function getTokenOperationByOrm()
  {
    if(class_exists('Doctrine'))
    {
      return Doctrine::getTable('Token');
    }
    else
    {
      return 'TokenPeer';
    }
  }
}