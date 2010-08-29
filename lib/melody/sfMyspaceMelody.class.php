<?php
class sfMyspaceMelody extends sfMelody1
{
  protected function initialize($config)
  {
    $this->setRequestTokenUrl('http://api.myspace.com/request_token');
    $this->setRequestAuthUrl('http://api.myspace.com/authorize');
    $this->setAccessTokenUrl('http://api.myspace.com/access_token');

    $this->setNamespace('default', 'http://api.myspace.com/v1');

    $this->setAlias('me', 'user.json');
  }

  public function getIdentifier()
  {
    $me = $this->getMe();

    if($me)
    {
      return $me->userId;
    }
    else
    {
      return parent::getIdentifier();
    }
  }

}