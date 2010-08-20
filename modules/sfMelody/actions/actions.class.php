<?php
class sfMelodyActions extends sfActions
{
  public function executeAccess(sfWebRequest $request)
  {
    $service = $request->getParameter('service');
    $token = $this->getUser()->getToken($service, Token::STATUS_REQUEST);

    $oauth = sfMelody::getInstance($service, array('token' => $token));

    if($oauth->getVersion() == 1)
    {
      $code = $request->getParameter('oauth_verifier');
    }
    else
    {
      $code = $request->getParameter('code');
    }

    $callback = $oauth->getCallback();
    //for oauth 2 the same redirect_uri
    $oauth->setCallback('@melody_access?service='.$service);
    $extra_params = $oauth->getAccessToken($code);

    if($token)
    {
      $token->delete();
    }

    $access_token = $oauth->getToken();
    $access_token->setUser($this->getUser()->getGuardUser());
    $access_token->save();

    $this->getUser()->setAttribute($service.'_extra_params', $extra_params);

    $this->redirect($callback);
  }
}