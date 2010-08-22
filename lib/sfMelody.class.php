<?php
/**
 * Tool Class to create melodies :) on do other stuffs
 *
 * @author Maxime Picaud
 * @since 21 août 2010
 */
class sfMelody
{
  /**
   *
   * @param string $name
   * @param array $config
   *
   * create a melody using the name in the app.yml file
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public static function getInstance($name, $config = array())
  {
    $default = sfConfig::get('app_melody_'.$name, array());

    $config = array_merge($default, $config);

    $provider = strtolower(isset($config['provider'])?$config['provider']:$name);
    $class = 'sf'.sfInflector::camelize($provider.'_melody');

    $key = isset($config['key'])?$config['key']:null;
    $secret = isset($config['secret'])?$config['secret']:null;
    $token = isset($config['token'])?$config['token']:null;
    $config['name'] = isset($config['name'])?$config['name']:$name;

    $oauth = new $class($key, $secret, $token, $config);

    return $oauth;
  }

  /**
   * get possible tokens statuses
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
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

  /**
   *
   * @param string $service
   * @param sfMelodyUser $user
   * @param string $status
   *
   * allow to delete tokens in the database
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public static function deleteTokens($service = null, $user = null, $status = null)
  {
    return self::execute('deleteTokens', array($service, $user, $status));
  }

  /**
   *
   * @param string $method
   * @param array $arguments
   *
   * Call an operation on token
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public static function execute($method, $arguments)
  {
    $callable = array(self::getTokenOperationByOrm(), $method);

    return call_user_func_array($callable, $arguments);
  }

  /**
   * Retrieve the good class depending on ORM
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
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