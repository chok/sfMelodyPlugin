<?php
class sfTwitterMelody extends sfOAuth1
{
  protected function initialize($config)
  {
    $this->request_token_url = 'https://api.twitter.com/oauth/request_token';
    $this->request_auth_url = 'https://api.twitter.com/oauth/authorize';
    $this->access_token_url = 'https://api.twitter.com/oauth/access_token';

    $this->setNamespaces(array('default' => 'http://api.twitter.com'));
  }

  public function getIdentifier()
  {
    return $this->getToken()->getParam('user_id');
  }
}