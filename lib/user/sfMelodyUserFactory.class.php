<?php
class sfMelodyUserFactory
{
  protected $config;
  protected $service;
  protected $user;

  public function __construct($service, $config = array())
  {
    $this->setConfig($config);
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
      $this->createUser($save);
    }

    return $this->user;
  }

  protected function createUser($save = false)
  {
    $user_class = sfConfig::get('app_melody_user_class', 'sfGuardUser');

    $user = new $user_class();

    $config = $this->getConfig();

    $modified = false;

    foreach($config as $field => $field_config)
    {
      list($call, $call_parameters, $path, $prefix, $suffix) = $this->explodeConfig($field_config);

      if(!is_null($call))
      {
        $result = $this->getService()->call($call, $call_parameters);
        $result = $this->getService()->fromPath($result, $path);

        if($result)
        {
          $result = $prefix.$result.$suffix;
          $method = 'set'.sfInflector::camelize('_'.$field);

          if(method_exists($user, $method))
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
      $call = isset($field_config['call'])?$field_config['call']:null;
      $call_parameters = isset($field_config['call_parameters'])?$field_config['call_parameters']:null;
      $path = isset($field_config['path'])?$field_config['path']:null;
      $prefix = isset($field_config['prefix'])?$field_config['prefix']:null;
      $suffix = isset($field_config['suffix'])?$field_config['suffix']:null;
    }
    else
    {

    }

    return array($call, $call_parameters, $path, $prefix, $suffix);
  }
}