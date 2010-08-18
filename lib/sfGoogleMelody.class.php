<?php
class sfGoogleMelody extends sfOAuth1
{
  protected $scopes = array();
  protected static $apis = array('contact' => 'http://www.google.com/m8/feeds/');

  protected function initialize($config)
  {
    $this->request_token_url = 'https://www.google.com/accounts/OAuthGetRequestToken';
    $this->request_auth_url = 'https://www.google.com/accounts/OAuthAuthorizeToken';
    $this->access_token_url = 'https://www.google.com/accounts/OAuthGetAccessToken';

    if(isset($config['api']))
    {
      $this->useApi($config['api']);
    }
  }




  public function getContacts($uid)
  {
    $url = 'http://www.google.com/m8/feeds/contacts/default/full';//.urlencode($uid).'/full';
    $this->params = array('alt' => 'json', 'max-results' => 99999999);
    $request = OAuthRequest::from_consumer_and_token($this->getConsumer(), $this->getToken(), 'GET', $url, $this->params);
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->getConsumer(), $this->getToken());

    $url = $request->to_url();

    return $this->call($url, null, 'GET');
  }

  public function setScopes($scopes)
  {
    $this->scopes = array_unique($scopes);

    $this->mergeScopesWithParameters();
  }

  public function addScope($scope)
  {
    if(!$this->hasScope($scope))
    {
      $this->scopes[] = $scope;

      $this->mergeScopesWithParameters();
    }
  }

  public function getScopes()
  {
    return $this->scopes;
  }

  public function hasScope($scope)
  {
    return array_search($scope, $this->scopes) !== false;
  }

  public function addScopes($scopes)
  {
    $this->scopes = array_unique(array_merge($this->scopes, $scopes));

    $this->mergeScopesWithParameters();
  }

  protected function mergeScopesWithParameters()
  {
    $scope = implode(' ', $this->getScopes());

    $this->setParameter('scope', $scope);
  }

  public function useApi($api)
  {
    if(is_array($api))
    {
      foreach($api as $tmp_api)
      {
        $this->useApi($tmp_api);
      }
    }
    else
    {
      $this->addScope($this->getScopeByApiName($api));
    }
  }

  public function getScopeByApiName($api)
  {
    $api = strtolower($api);

    return self::$apis[$api];
  }
}