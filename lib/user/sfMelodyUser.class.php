<?php
/**
 *
 *
 *
 * Allow to easily connect an user
 *
 * @author Maxime Picaud
 * @since 21 août 2010
 */
class sfMelodyUser extends sfGuardSecurityUser
{
  protected $tokens;

  /**
   *
   * @param string $service
   * @param boolean $force
   *
   * connect to a service. if already connected redirect to the callback
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public function connect($service, $force = false)
  {
    $melody = sfMelody::getInstance($service);

    if(!$this->isConnected($service) || $force)
    {
      $this->removeTokens($service);

      $melody->setCallback('@melody_access?service='.$service);
      $melody->connect($this);
    }
    else
    {
      $oauth->getController()->redirect($oauth->getCallback());
    }
  }

  /**
   *
   * @param string $service
   *
   * check if the user is connected to the service
   *
   * @return boolean
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public function isConnected($service)
  {
    $token = $this->getToken($service);

    return !is_null($token) && $token->isValidToken();
  }

  /**
   *
   * @param string $service
   * @param string $status
   * @param boolean $remove_in_session
   *
   * get a token from a service
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public function getToken($service, $status = Token::STATUS_ACCESS)
  {
    $token = null;

    if(count($this->getTokens()) > 0)
    {
      if(!is_null($status))
      {
        $token = isset($tokens[$status][$service])?$tokens[$status][$service]:null;
      }
      else
      {
        foreach(Token::getAllStatuses() as $status)
        {
          if(isset($tokens[$status][$service]))
          {
            $token = $tokens[$status][$service];
            break;
          }
        }
      }
    }

    return $token;
  }

  /**
   *
   * @param string $service
   * @param string $status
   * @param boolean $remove
   *
   * get token from the session
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  protected function getSessionTokens($service)
  {
    $tokens = array();

    foreach(Token::getAllStatuses() as $status)
    {
      $token = $this->getAttribute($service.'_'.$status.'_token');
      if($token)
      {
        $tokens[$status][$service] = unserialize($token);
      }
    }

    return $tokens;
  }

  protected function getDbTokens()
  {
    $db_tokens = $this->getOrmAdapter('Token')->findByUserId($this->getGuardUser()->getId());

    $tokens = array();
    foreach($db_tokens as $token)
    {
      $tokens[$token->getStatus()][$token->getName()] = $token;
    }

    return $tokens;
  }

  /**
   *
   *
   *
   * getTokens store in the database
   *
   * TODO in the session too
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public function getTokens()
  {
    if(is_null($this->tokens))
    {
      $this->tokens = array_merge($this->getSessionTokens(), $this->getDbTokens());
    }

    return $this->tokens;
  }

  /**
   *
   * @param string $service
   * @param string $status
   *
   * removeTokens from database or session
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public function removeTokens($service, $status = null)
  {
    $this->removeTokensRecursive($service, $status);
  }

  /**
   *
   * @param string $service
   * @param string $status
   * @param boolean $remove_from_db
   *
   * To hide recursive parameter $remove_from_db
   *
   * @author Maxime Picaud
   * @since 29 août 2010
   */
  protected function removeTokensRecursive($service, $status = null, $remove_from_db = true)
  {
    if(is_null($status))
    {
      if($this->isAuthenticated())
      {
        $this->getOrmAdapter('Token')->deleteTokens($service, $this->getGuardUser(), $status);
      }

      foreach(Token::getAllStatuses() as $status)
      {
        $this->removeTokens($service, $status, false);
      }
    }
    else
    {
      if($this->hasAttribute($service.'_'.$status.'_token'))
      {
        $this->getAttributeHolde()->remove($service.'_'.$status.'_token');
      }

      if($remove_from_db && $this->isAuthenticated())
      {
        $this->getOrmAdapter('Token')->deleteTokens($service, $this->getGuardUser(), $status);
      }
    }
  }

  /**
   *
   * @param string $service
   * @param array $config
   * @param $in_session
   *
   * get a melody
   *
   * @author Maxime Picaud
   * @since 21 août 2010
   */
  public function getMelody($service, $config = array())
  {
    $token = $this->getToken($service);

    $config = array_merge(array('token' => $token), $config);

    return sfMelody::getInstance($service, $config);
  }

  protected function getOrmAdapter($model)
  {
    return sfMelodyOrmAdapter::getInstance($model);
  }
}