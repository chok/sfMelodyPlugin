<?php
class sfMelodyUserFactory
{
  protected $config;
  protected $service;
  protected $user;

  public function __construct($service, $config = array())
  {
    $this->setConfig($config);
    $this->setService($service);
  }

  public function setConfig($config)
  {
    $this->config = $config;
  }

  public function getConfig()
  {
    return $this->config;
  }

  public function setService($service)
  {
    $this->service = $service;
  }

  public function getService()
  {
    return $this->service;
  }

  public function getUser($save = false, $refresh = false)
  {
    if(is_null($this->user) || $refresh)
    {
      $this->user = $this->createUser($save);
    }

    return $this->user;
  }

  protected function createUser($save = false)
  {
    // maybe later :)
    //$user_class = sfConfig::get('app_melody_user_class', 'sfGuardUser');

    $user = new sfGuardUser();

    $config = $this->getConfig();

    $modified = false;

    $last_call = null;
    $last_result = null;
    foreach($config as $field => $field_config)
    {
      list($call, $call_parameters, $path, $prefix, $suffix) = $this->explodeConfig($field_config);

      if(!is_null($call))
      {
        if($last_call == $call)
        {
          $result = $last_result;
        }
        else
        {
          $result = $this->getService()->get($call, $call_parameters);
          $last_result = $result;
          $last_call = $call;
        }

        $result = $this->getService()->fromPath($result, $path);

        if($result)
        {
          $result = $prefix.$result.$suffix;
          $method = 'set'.sfInflector::classify($field);

          if(is_callable(array($user, $method)))
          {
            $user->$method($result);
            $modified = true;
          }
        }
      }
    }

    if($save)
    {
      $user->save();
    }

    return $user;
  }

  protected function explodeConfig($config)
  {
    $call = null;
    $call_parameters = array();
    $path = '';
    $prefix = '';
    $suffix = '';

    if(is_array($config))
    {
      $call = isset($config['call'])?$config['call']:null;
      $call_parameters = isset($config['call_parameters'])?$config['call_parameters']:null;
      $path = isset($config['path'])?$config['path']:null;
      $prefix = isset($config['prefix'])?$config['prefix']:null;
      $suffix = isset($config['suffix'])?$config['suffix']:null;
    }
    else
    {
      $call = $config;
    }

    return array($call, $call_parameters, $path, $prefix, $suffix);
  }

  public function getKeys()
  {
    $keys = array();
    foreach($this->getConfig() as $field => $field_condig)
    {
      if(isset($field_condig['key']) && $field_condig['key'])
      {
        $keys[] = $field;
      }
    }

    if(count($keys) == 0)
    {
      $keys = array_keys($this->getConfig());
    }

    return $keys;
  }

  public function isCompatible($user)
  {
    $melody_user = $this->getUser();

    $compatible = true;
    foreach($this->getKeys() as $key)
    {
      $method = 'get'.sfInflector::classify($key);

      if(is_callable(array($user, $method)))
      {
        if($user->$method() != $melody_user->$method())
        {
          $compatible = false;
          break;
        }
      }
      else
      {
        throw new sfException(sprintf('"%s" don\'t have field "%s"', get_class($user, $key)));
      }
    }

    return $compatible;
  }
}