<?php
/**
 * Hyves provider voor sfMelodyPlugin
 * door Djuri Baars
 */
class sfHyvesMelody extends sfMelody1
{
  protected function initialize($config)
  {
	$this->setRequestTokenUrl('http://data.hyves-api.nl/');
    $this->setRequestAuthUrl('http://www.hyves.nl/api/authorize/');
    $this->setAccessTokenUrl('http://data.hyves-api.nl/');
	
	$this->setOutputFormat('xml');

	$this->setRequestParameter('methods', 'users.getLoggedin,users.get,media.getByLoggedin');
	$this->setRequestParameter('ha_method', 'auth.requesttoken');
	$this->setRequestParameter('ha_format', 'json');
	$this->setRequestParameter('ha_version', '2.0');
	$this->setRequestParameter('ha_fancylayout', 'false');
	
	$this->setAccessParameter('ha_format', 'json');
	$this->setAccessParameter('ha_version', '2.0');
	$this->setAccessParameter('ha_method', 'auth.accesstoken');
	$this->setAccessParameter('ha_fancylayout', 'false');
	
	$this->setCallParameter('ha_method', 'users.getLoggedin');
	$this->setCallParameter('ha_format', 'xml');
	$this->setCallParameter('ha_version', '2.0');
	$this->setCallParameter('ha_fancylayout', 'false');
	$this->setCallParameter('ha_responsefields', 'profilepicture');
	$this->setNamespace('default', 'http://data.hyves-api.nl/');
	
	$this->setAlias('me', 'users.getLoggedin');
  }

  protected function setExpire(&$token)
  {
    if($token->getParam('expiredate'))
    {
      $token->setExpire(time() + $token->getParam('expiredate'));
    }
  }

  public function getIdentifier()
  {
    return $this->getToken()->getParam('userid');
  }
}