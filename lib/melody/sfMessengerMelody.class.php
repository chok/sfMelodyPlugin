<?php
class sfMessengerMelody extends sfMelodyWrap
{
  protected function initialize($config)
  {
    $this->setRequestAuthUrl('https://consent.live.com/Connect.aspx');
    $this->setAccessTokenUrl('https://consent.live.com/AccessToken.aspx');

    if(isset($config['scope']))
    {
      $this->setAuthParameter('wrap_scope', implode(',', $config['scope']));
    }

    $this->setNamespace('default', 'http://apis.live.net/V4.1');
    $this->setNamespace('profile', 'http://profiles.apis.live.net/V4.1');

    $this->setCallParameter('format', 'json');
  }

  protected function initializeFromToken($token)
  {
    if($token && $token->getStatus() == Token::STATUS_ACCESS)
    {
      $this->setAliases(array('contacts' => 'cid-'.strtoupper($this->getIdentifier()).'/Contacts/AllContacts',
                              'invitations' => 'cid-'.strtoupper($this->getIdentifier()).'/Contacts/Invitations',
                              )
                       );
    }
  }

  public function getIdentifier()
  {
    return $this->getToken()->getParam('uid');
  }

  protected function setExpire(&$token)
  {
    $token->setExpire(time() + $token->getParam('wrap_access_token_expires_in'));
  }
}