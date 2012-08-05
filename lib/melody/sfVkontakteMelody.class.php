<?php
class sfVkontakteMelody extends sfMelody2
{
  protected function initialize($config)
  {
    $this->setRequestAuthUrl('http://oauth.vk.com/authorize');
    $this->setAccessTokenUrl('https://oauth.vk.com/access_token');

    $this->setNamespaces(array('default' => 'https://api.vk.com'));

    if(isset($config['scope']))
    {
      $this->setAuthParameter('scope', implode(',', $config['scope']));
    }

    if(isset($config['display']))
    {
      $this->setAuthParameter('display', $config['display']);
    }
  }

  public function initializeFromToken($token)
  {
    if($token && $token->getStatus() == Token::STATUS_ACCESS)
    {
      $this->setAlias(
        'me',
        'method/users.get?fields=fields=uid,first_name,last_name,nickname,screen_name,sex,bdate(birthdate),city,country,timezone,photo,photo_medium,photo_big,has_mobile,rate,contacts,education,online,counters&uid=' . $this->getToken()->getParam('user_id')
      );
    }
  }

  public function getIdentifier()
  {
    $me = $this->get('me');
    if(isset($me->id))
    {
      return $me->id;
    }

    return null;
  }

  protected function setExpire(&$token)
  {
    if($token->getParam('expires'))
    {
      $token->setExpire(time() + $token->getParam('expires'));
    }
  }
}
