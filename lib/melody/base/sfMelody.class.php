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

    $melody = new $class($key, $secret, $token, $config);

    return $melody;
  }

  public static function getAllServices()
  {
    $services = array();
    foreach(sfConfig::getAll() as $key => $value)
    {
      $params = explode('_', $key);
      if(in_array('melody', $params) && is_array($value) && isset($value['key']) && isset($value['secret']))
      {
        $services[] = substr($key, 11);
      }
    }

    return $services;
  }

  public static function getUserFactory($melody)
  {
    $config = $melody->getConfig();
    $user_config = isset($config['user'])?$config['user']:array();

    return new sfMelodyUserFactory($melody, $user_config);
  }

  public static function sleep(&$melody)
  {
    $melody->getUserFactory()->setService(null);

    $reflection = new ReflectionObject($melody);

    $fields = array();
    $ignored_properties = array('controller', 'context', 'logger');

    foreach($reflection->getProperties() as $property)
    {
      if(!in_array($property->getName(), $ignored_properties))
      {
        $fields[] = $property->getName();
      }
    }

    return $fields;
  }

  public static function wakeup(&$melody)
  {
    $melody->getUserFactory()->setService($melody);
  }
}