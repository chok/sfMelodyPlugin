<?php
class sfYahooMelody extends sfMelody1
{
  protected function initialize($config)
  {
    $this->setRequestTokenUrl('https://api.login.yahoo.com/oauth/v2/get_request_token');
    $this->setRequestAuthUrl('https://api.login.yahoo.com/oauth/v2/request_auth');
    $this->setAccessTokenUrl('https://api.login.yahoo.com/oauth/v2/get_token');

    $this->setNamespace('default', 'http://social.yahooapis.com/v1');

    $this->setCallParameter('format', 'json');
  }

  protected function initializeFromToken($token)
  {
    if($token && $token->getStatus() == Token::STATUS_ACCESS)
    {
      $this->setAliases(array('me' => 'user/'.$this->getToken()->getParam('xoauth_yahoo_guid'),
                              'uid' => $this->getToken()->getParam('xoauth_yahoo_guid')
                              )
                       );
    }
  }

  public function getIdentifier()
  {
    return $this->getToken()->getParam('xoauth_yahoo_guid');
  }

  protected function setExpire(&$token)
  {
    $token->setExpire(time() + $token->getParam('oauth_expires_in'));
  }

  public function refreshToken()
  {
    $this->setAccessParameter('oauth_session_handle', $this->getToken()->getParam('oauth_session_handle'));

    $request = OAuthRequest::from_consumer_and_token($this->getConsumer(), $this->getToken('oauth'), 'POST', $this->getAccessTokenUrl(), $this->getAccessParameters());
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->getConsumer(), $this->getToken('oauth'));

    $params = OAuthUtil::parse_parameters($this->call($this->getAccessTokenUrl(), $request->to_postdata()));
    var_dump($params);die();
    $oauth_token = isset($params['oauth_token'])?$params['oauth_token']:null;
    $oauth_token_secret = isset($params['oauth_token_secret'])?$params['oauth_token_secret']:null;

    if(is_null($oauth_token) || is_null($oauth_token_secret))
    {
      $error = sprintf('{OAuth} access token failed - %s returns %s', $this->getName(), print_r($params, true));
      sfContext::getInstance()->getLogger()->err($error);
    }

    $token = new Token();
    $token->setTokenKey($oauth_token);
    $token->setTokenSecret($oauth_token_secret);
    $token->setStatus(Token::STATUS_ACCESS);
    $token->setName($this->getName());
    $token->setOAuthVersion($this->getVersion());

    unset($params['oauth_token'], $params['oauth_token_secret']);
    if(count($params) > 0)
    {
      $token->setParams($params);
    }

    $this->setExpire($token);

    //override request_token
    $this->setToken($token);

    $token->setIdentifier($this->getIdentifier());
    $this->setToken($token);

    return $token;
  }
}