<?php
class sfFacebookMelody extends sfOAuth2
{
  protected function initialize($config)
  {
    $this->request_auth_url = 'https://graph.facebook.com/oauth/authorize';
    $this->access_token_url = 'https://graph.facebook.com/oauth/access_token';

    $this->setNamespaces(array('default' => 'https://graph.facebook.com'));
  }

  public function getIdentifier()
  {
    $me = $this->getMe();
    if(isset($me->id))
    {
      return $me->id;
    }

    return null;
  }

  protected function setExpire(&$token)
  {
    $token->setExpire(date('Y-m-d H:i:s', time() + $token->getParam('expires')));
  }
}