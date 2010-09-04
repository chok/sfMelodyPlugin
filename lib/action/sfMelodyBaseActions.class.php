<?php
/**
 *
 *
 *
 * Set of methods to use if need to override the default module
 *
 * @author Maxime Picaud
 * @since 31 août 2010
 */
class sfMelodyBaseActions extends sfActions
{
  /**
   * callback is the original callback
   * from the current melody
   *
   * @var string
   */
  protected $callback;

  /**
   * @var sfMelody(1|2)
   */
  protected $melody = -1;

  /**
   *
   * @param string $callback
   *
   * setter callback
   *
   * @author Maxime Picaud
   * @since 31 août 2010
   */
  protected function setCallback($callback)
  {
    $this->callback = $callback;
  }

  /**
   * getter callback
   * Allow to retrieve the original callback from the current melody
   *
   * @author Maxime Picaud
   * @since 31 août 2010
   */
  protected function getCallback()
  {
    if(is_null($this->callback))
    {
      $this->callback = $this->getMelody()->getCallback();
    }

    return $this->callback;
  }

  /**
   *
   * @param sfMelody(1|2) $melody
   *
   * setter melody
   *
   * @author Maxime Picaud
   * @since 31 août 2010
   */
  protected function setMelody($melody)
  {
    $this->melody = $melody;
  }

  /**
   *
   * @param string $service
   *
   * getter melody
   * Allow to retrieve the current melody by the default parameter service
   * or by a targeted service
   *
   * @author Maxime Picaud
   * @since 31 août 2010
   */
  protected function getMelody($service = null)
  {
    if((is_numeric($this->melody) && $this->melody == -1) || !is_null($service))
    {
      if(is_null($service))
      {
        $service = $this->getRequestParameter('service');
      }

      if(!is_null($service))
      {
        $request_token = $this->getUser()->getToken($service, Token::STATUS_REQUEST);

        $this->melody = sfMelody::getInstance($service, array('token' => $request_token));
        $this->setCallback($this->melody->getCallback());
      }
      else
      {
        $this->melody = null;
      }
    }

    return $this->melody;
  }

  protected function getOrmAdapter($model)
  {
    return sfMelodyOrmAdapter::getInstance($model);
  }

  protected function getGuardAdapter()
  {
    return $this->getOrmAdapter('sfGuardUser');
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
  protected function getCode()
  {
    if($this->getMelody()->getVersion() == 1)
    {
      $code = $this->getRequestParameter('oauth_verifier');
    }
    else
    {
      $code = $this->getRequestParameter('code');
    }

    return $code;
  }
}