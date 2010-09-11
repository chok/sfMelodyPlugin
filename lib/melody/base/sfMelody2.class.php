<?php
class sfMelody2 extends sfOAuth2 implements Serializable
{
  protected $user_factory;

  public function &getUserFactory()
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

  public function connect($user, $auth_parameters = array(), $request_params = array())
  {
    $this->requestAuth($auth_parameters);
  }

  public function serialize()
  {
    return sfMelody::serialize($this);
  }

  public function unserialize($serialized)
  {
    return sfMelody::unserialize($this);
  }
}