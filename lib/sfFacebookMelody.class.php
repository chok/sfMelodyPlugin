<?php
class sfFacebookMelody extends sfOAuth2
{
  protected function initialize($config)
  {
    $this->setRequestAuthUrl('https://graph.facebook.com/oauth/authorize');
    $this->setAccessTokenUrl('https://graph.facebook.com/oauth/access_token');

    $this->setNamespaces(array('default' => 'https://graph.facebook.com'));

    if(isset($config['scope']))
    {
      $this->setAuthParameter('scope', implode(',', $config['scope']));
    }
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
    $token->setExpire(time() + $token->getParam('expires'));
  }
}