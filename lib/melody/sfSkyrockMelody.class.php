<?php
class sfSkyrockMelody extends sfMelody1
{
  protected function initialize($config)
  {
    $this->setRequestTokenUrl('https://api.skyrock.com/v2/oauth/initiate');
    $this->setRequestAuthUrl('https://api.skyrock.com/v2/oauth/authenticate');
    $this->setAccessTokenUrl('https://api.skyrock.com/v2/oauth/token');
    
    $this->setNamespaces(array('default' => 'https://api.skyrock.com/v2'));
  }

  public function initializeFromToken($token)
  {
    if($token && $token->getStatus() == Token::STATUS_ACCESS)
    {
      $this->setAlias('me', 'user/get.json');
    }
  }

}