<?php
/**
 * Actions class for Melody
 *
 * @author Gordon Franke <info@nevalon.de>
 * @since 29 août 2010
 */
class BasesfMelodyActions extends sfMelodyBaseActions
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
    $melody = $this->getMelody();

    $melody->setCallback('@melody_access?service='.$melody->getName());
	$parameters = array();
	if($request->getParameter('redirect_uri'))
	{
		$parameters['redirect_uri'] = $request->getParameter('redirect_uri');
	}
    $access_token = $melody->getAccessToken($this->getCode(), $parameters);

    $melody->setToken($access_token);

    $user = null;

    if($this->getUser()->isAuthenticated())
    {
      $user = $this->getUser()->getGuardUser();

      $conflict = !$melody->getUserFactory()->isCompatible($user);
      $event = new sfEvent($this, 'melody.filter_user', array('melody' => $melody, 'conflict' => $conflict));
      $dispatcher = $this->getContext()->getEventDispatcher();
      $user = $dispatcher->filter($event, $user)->getReturnValue();
    }
	//Емана!! 0ЩO
    elseif($access_token->getTokenKey())
    {
      $old_token = $this->getOrmAdapter('Token')->findOneByNameAndIdentifier($melody->getName(), $melody->getIdentifier());

      //try to get user from the token
      if($old_token)
      {
        $user = $old_token->getUser();
      }

      //try to get user by melody
      if(!$user)
      {
        $user = $this->getGuardAdapter()->findByMelody($melody);
      }

      $create_user = sfConfig::get('app_melody_create_user', false);
      $redirect_register = sfConfig::get('app_melody_redirect_register', false);

      $create_user = $melody->getConfigParameter('create_user', $create_user);
      $redirect_register = $melody->getConfigParameter('redirect_register', $redirect_register);

      //create a new user if needed
      if(!$user && ( $create_user || $redirect_register))
      {
        $user = $melody->getUser();

        if($redirect_register)
        {
		  //Bad workflow
		  $this->getUser()->setAttribute('melody_user_profile', serialize($user->getSfGuardUserProfile()));
          $this->getUser()->setAttribute('melody_user', serialize($user));
          $this->getUser()->setAttribute('melody', serialize($melody));

          $this->redirect($redirect_register);
        }
        elseif($user)
        {
          $user->save();
        }
		elseif(sfConfig::get('app_melody_oauth_fail', false))
		{
			$this->getUser()->setAttribute('error_info', $access_token->getResponseInfo());
			$this->redirect(sfConfig::get('app_melody_oauth_fail', false));
		}
		else
		{
			$this->forward404();
		}
      }


		if($user)
		{
		  $access_token->setUserId($user->getId());

		  if(!$this->getUser()->isAuthenticated() && $user->getIsActive())
		  {
			$this->getUser()->signin($user, sfConfig::get('app_melody_remember_user', true));
		  }
		}
	}
	elseif(sfConfig::get('app_melody_oauth_fail', false))
	{
		$this->getUser()->setAttribute('error_info', $access_token->getResponseInfo());
		$this->redirect(sfConfig::get('app_melody_oauth_fail', false));
	}
	else
	{
		$this->forward404();
	}

    $this->getUser()->addToken($access_token);

    $this->redirect($this->getCallback());
  }
}