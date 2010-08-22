<?php
class sfTwitterMelody extends sfOAuth1
{
  protected function initialize($config)
  {
    $this->setRequestTokenUrl('https://api.twitter.com/oauth/request_token');
    $this->setRequestAuthUrl('https://api.twitter.com/oauth/authorize');
    $this->setAccessTokenUrl('https://api.twitter.com/oauth/access_token');

    $this->setNamespaces(array('default' => 'http://api.twitter.com'));
  }

  public function getIdentifier()
  {
    return $this->getToken()->getParam('user_id');
  }
}