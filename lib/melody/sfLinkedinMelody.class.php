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
    /*
    * if expiration = 0 then it means that the expiration limit is unlimited
     * -> the default is to never expire.
    */
  protected function setExpire(&$token)
  {
    if($token->getParam('oauth_authorization_expires_in') == 0){
        $token->setExpire(null);
    }
    else{
        $token->setExpire(time() + $token->getParam('oauth_authorization_expires_in'));
    }
  }
}