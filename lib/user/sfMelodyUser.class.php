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
  public function connect($service, $config= array(), $force = false)
  {
    $melody = sfMelody::getInstance($service, $config);

    if(!$this->isConnected($service) || $force)
    {
      $this->removeTokens($service);

      $melody->setCallback('@melody_access?service='.$service);
      $melody->connect($this);
    }
    else
    {
      $melody->getController()->redirect($melody->getCallback());
    }
  }

  /**
   * (non-PHPdoc)
   * @see plugins/sfDoctrineGuardPlugin/lib/user/sfGuardSecurityUser::signIn()
   */
  public function signIn($user, $remember = false, $con = null)
  {
    parent::signIn($user, $remember, $con);

    $this->refreshTokens();
  }

  /**
   * (non-PHPdoc)
   * @see plugins/sfDoctrineGuardPlugin/lib/user/sfGuardSecurityUser::signOut()
   */
  public function signOut()
  {
    $this->getAttributeHolder()->removeNamespace('Melody');
    parent::signOut();
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
   *
   *
   * Allow to retrieve name of services which is connected with the user
   *
   * @author Maxime Picaud
   * @since 13 sept. 2010
   */
  public function getConnectedServices()
  {
    $connected_services = array();

    foreach(sfMelody::getAllServices() as $service)
    {
      if($this->isConnected($service))
      {
        $connected_services[] = $service;
      }
    }

    return $connected_services;
  }

  public function getUnconnectedServices()
  {
    $unconnected_services = array();
    $connected_services = $this->getConnectedServices();

    foreach(sfMelody::getAllServices() as $service)
    {
      if(!in_array($service, $connected_services))
      {
        $unconnected_services[] = $service;
      }
    }

    return $unconnected_services;
  }

  /**
   *
   * @param Token $token
   *
   * Add a token to the user
   *
   * @author Maxime Picaud
   * @since 13 sept. 2010
   */
  public function addToken($token)
  {
    $service = $token->getName();
    $status = $token->getStatus();

    $tokens = $this->getTokens();

    if(isset($tokens[$status][$service]))
    {
      $this->removeTokens($service, $status);
    }

    if($status == Token::STATUS_REQUEST || is_null($token->getUserId()))
    {
      $this->setAttribute($service.'_'.$status.'_token', serialize($token), 'Melody');
    }
    else
    {
      $token->save();
    }

    $this->tokens[$status][$service] = $token;
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
    $tokens = $this->getTokens();
    if(count($tokens) > 0)
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
  protected function getSessionTokens($service = null)
  {
    if(is_null($service))
    {
      $services = sfMelody::getAllServices();
    }
    else
    {
      $services = array($service);
    }

    $tokens = array();

    foreach($services as $service)
    {
      foreach(Token::getAllStatuses() as $status)
      {
        $token = $this->getAttribute($service.'_'.$status.'_token', null, 'Melody');
        if($token)
        {
          $tokens[$status][$service] = unserialize($token);
        }
      }
    }

    return $tokens;
  }

  protected function getDbTokens()
  {
    $tokens = array();

    if($this->isAuthenticated())
    {
      $db_tokens = $this->getOrmAdapter('Token')->findByUserId($this->getGuardUser()->getId());

      foreach($db_tokens as $token)
      {
        $tokens[$token->getStatus()][$token->getName()] = $token;
      }
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
   *
   *
   * set null tokens to get tokens again
   *
   * @author Maxime Picaud
   * @since 4 sept. 2010
   */
  public function refreshTokens()
  {
    $this->tokens = null;
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
    if(is_null($status))
    {
      if($this->isAuthenticated())
      {
        $this->getOrmAdapter('Token')->deleteTokens($service, $this->getGuardUser(), $status);
      }

      $this->getAttributeHolder()->removeNamespace('Melody');
    }
    else
    {
      if($this->hasAttribute($service.'_'.$status.'_token', 'Melody'))
      {
        $this->getAttributeHolder()->remove($service.'_'.$status.'_token', 'Melody');
      }

      if($this->isAuthenticated())
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