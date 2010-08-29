<?php
class sfLinkedinMelody extends sfMelody1
{
  protected function initialize($config)
  {
    $this->setRequestTokenUrl('https://api.linkedin.com/uas/oauth/requestToken');
    $this->setRequestAuthUrl('https://www.linkedin.com/uas/oauth/authorize');
    $this->setAccessTokenUrl('https://api.linkedin.com/uas/oauth/accessToken');

    $this->setOutputFormat('xml');

    $this->setNamespace('default', 'https://api.linkedin.com/v1');
    $this->setAlias('me', 'people/~');
  }

  protected function setExpire(&$token)
  {
    $token->setExpire(time() + $token->getParam('oauth_authorization_expires_in'));
  }
}