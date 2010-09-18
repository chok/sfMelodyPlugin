<?php
class sfFacebookMelody extends sfMelody2
{
  protected function initialize($config)
  {
    $this->setRequestAuthUrl('https://graph.facebook.com/oauth/authorize');
    $this->setAccessTokenUrl('https://graph.facebook.com/oauth/access_token');

    $this->setNamespaces(array('default' => 'https://graph.facebook.com',
                               'api'     => 'https://api.facebook.com'));

    $this->setAlias('fql', 'method/fql.query');

    if(isset($config['scope']))
    {
      $this->setAuthParameter('scope', implode(',', $config['scope']));
    }
  }

  public function fql($query)
  {
    $ns = $this->getCurrentNamespace();
    $this->ns('api');
    $result = $this->getFql(null, array('query' => $query, 'format' => 'json'));
    $this->ns($ns);

    return $result;
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
    if($token->getParam('expires'))
    {
      $token->setExpire(time() + $token->getParam('expires'));
    }
  }
}