<?php
/**
 * Actions class for Melody
 *
 * @author Maxime Picaud
 * @since 29 août 2010
 */
class sfMelodyActions extends sfActions
{
  /**
   *
   * @param sfWebRequest $request
   *
   * Store access token and manage user
   *
   * @author Maxime Picaud
   * @since 29 août 2010
   */
  public function executeAccess(sfWebRequest $request)
  {
    $service = $request->getParameter('service');
    $request_token = $this->getUser()->getToken($service, Token::STATUS_REQUEST, true);

    $melody = sfMelody::getInstance($service, array('token' => $request_token));

    list($access_token, $callback) = $this->getAccessToken($melody);

    $this->manageToken($access_token);

    $this->redirect($callback);
  }

  /**
   *
   * @param sfMelody(1|2) $melody
   *
   * get Access code according OAuth version
   *
   * @author Maxime Picaud
   * @since 29 août 2010
   */
  protected function getCode($melody)
  {
    if($melody->getVersion() == 1)
    {
      $code = $request->getParameter('oauth_verifier');
    }
    else
    {
      $code = $request->getParameter('code');
    }

    return $code;
  }

  /**
   *
   * @param sfMelody(1|2) $melody
   *
   * Step to get access token
   *
   * @return array(access_token, callback)
   *
   * @author Maxime Picaud
   * @since 29 août 2010
   */
  protected function getAccessToken($melody)
  {
    $callback = $melody->getCallback();

    //for oauth 2 the same redirect_uri
    $melody->setCallback('@melody_access?service='.$melody->getName());

    $access_token = $melody->getAccessToken($this->getCode($melody));

    return array($access_token, $callback);
  }

  /**
   *
   * @param Token $token
   *
   * Create and/or signin user
   *
   * @author Maxime Picaud
   * @since 29 août 2010
   */
  protected function manageToken($token)
  {
    $melody->setToken($token);

    $user = $melody->getGuardAdapter()->find();

    if($this->getUser()->isAuthenticated())
    {
      if($user->getId() == $this->getUser()->getGuardUser()->getId())
      {
        //yeah !
      }
      else
      {
        // WTF ?!
      }
    }
    if($user)
    {
      $token->setUserId($user->getId());

      $this->getUser()->signin($user, sfConfig::get('app_melody_remember_user', true));
    }
    if($this->getUser()->isAuthenticated())
    {
      $access_token->setUserId($this->getUser()->getGuardUser()->getId());
    }
    else
    {

      //we looking for an existing token
      $old_token = sfMelody::execute('findOneByNameAndIdentifier', array($service, $oauth->getIdentifier()));

      if($old_token)
      {
        $access_token->setUserId($old_token->getUserId());

        $old_token->delete();
      }
      else
      {
        $user = $melody->createUser();

        if(!is_null($user))
        {
          $access_token->setUserId($user);

          //logged in the new user

        }
      }
    }

    if($this->getUser()->isAuthenticated())
    {
      if($access_token->isValidToken())
      {
        $access_token->save();
      }
    }
    else
    {
      $this->getUser()->setAttribute($service.'_'.Token::STATUS_ACCESS.'_token', serialize($access_token));
    }

    $this->getUser()->removeTokens($service, Token::STATUS_REQUEST, true);
  }
}