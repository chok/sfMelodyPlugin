<?php
class sfMelodyWrap extends sfOAuthWrap
{
  protected $user_factory;

  public function getUserFactory()
  {
    if(is_null($this->user_factory))
    {
      $this->user_factory = sfMelody::getUserFactory($this);
    }

    return $this->user_factory;
  }

  public function setUserFactory($user_factory)
  {
    $this->user_factory = $user_factory;
  }

  public function getUser()
  {
    return $this->getUserFactory()->getUser();
  }

  public function connect($user, $auth_parameters = array(), $request_parameters = array())
  {
    $this->requestAuth($auth_parameters);
  }

  public function __sleep()
  {
    return sfMelody::sleep($this);
  }

  public function __wakeup()
  {
    sfMelody::wakeup($this);
  }
}