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
    $access_token = $melody->getAccessToken($this->getCode());

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
    else
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
          $this->getUser()->setAttribute('melody_user', serialize($user));
          $this->getUser()->setAttribute('melody', serialize($melody));

          $this->redirect($redirect_register);
        }
        else
        {
          $user->save();
        }
      }
    }

    if($user)
    {
      $access_token->setUserId($user->getId());

      if(!$this->getUser()->isAuthenticated())
      {
        $this->getUser()->signin($user, sfConfig::get('app_melody_remember_user', true));
      }
    }

    $this->getUser()->addToken($access_token);

    $this->redirect($this->getCallback());
  }
}