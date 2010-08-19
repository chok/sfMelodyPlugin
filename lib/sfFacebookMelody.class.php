<?php
class sfFacebookMelody extends sfOAuth2
{
  protected function initialize($config)
  {
    $this->request_auth_url = 'https://graph.facebook.com/oauth/authorize';
    $this->access_token_url = 'https://graph.facebook.com/oauth/access_token';

    $this->setNamespaces(array('default' => 'https://graph.facebook.com'));
  }
}