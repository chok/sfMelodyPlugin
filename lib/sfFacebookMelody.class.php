<?php
class sfFacebookMelody extends sfOAuth2
{
  protected function initialize($config)
  {
    $this->request_auth_url = 'https://graph.facebook.com/oauth/authorize';
    $this->access_token_url = 'https://graph.facebook.com/oauth/access_token';
  }

  public function getContacts($uid)
  {
    $url = 'https://graph.facebook.com/226800131/friends?access_token='.$this->getToken()->key;

    return $this->call($url, null, 'GET');
  }
}