<?php
class sfYahooMelody extends sfOAuth1
{
  protected function initialize($config)
  {
    $this->request_token_url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
    $this->request_auth_url = 'https://api.login.yahoo.com/oauth/v2/request_auth';
    $this->access_token_url = 'https://api.login.yahoo.com/oauth/v2/get_token';

    $this->namespaces = array('default' => 'http://social.yahooapis.com/v1');
  }

  public function getDefaultParamaters()
  {
    return array('format' => 'json');
  }

  public function getDefaultUrlParamaters()
  {
    return array('me' => 'user/'.$this->getToken()->getParam('xoauth_yahoo_guid'),
                 'uid' => $this->getToken()->getParam('xoauth_yahoo_guid')
                );
  }

  public function getIdentifier()
  {
    return $this->getToken()->getParam($this->getToken()->getParam('xoauth_yahoo_guid'));
  }

  protected function setExpire(&$token)
  {
    $token->setExpire(date('Y-m-d H:i:s', $token->getParam('oauth_authorization_expires_in')));
  }

  public function refreshToken()
  {
    $this->setParameter('oauth_session_handle', $this->getToken()->getParam('oauth_session_handle'));

    $request = OAuthRequest::from_consumer_and_token($this->getConsumer(), $this->getToken('oauth'), 'POST', $this->getAccessTokenUrl(), $this->getParameters());
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->getConsumer(), $this->getToken('oauth'));

    $params = OAuthUtil::parse_parameters($this->call($this->getAccessTokenUrl(), $request));

    $token = new Token();
    $token->setTokenKey($params['oauth_token']);
    $token->setTokenSecret($params['oauth_token_secret']);
    $token->setStatus(Token::STATUS_ACCESS);
    $token->setName($this->getName());
    $token->setOauthVersion($this->getVersion());

    unset($params['oauth_token'], $params['oauth_token_secret']);
    if(count($params) > 0)
    {
      $token->setParams($params);
    }

    $this->setExpire($token);

    //override request_token
    $this->setToken($token);

    return $token;
  }
}