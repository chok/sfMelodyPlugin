<?php
class sfTwitterMelody extends sfOAuth1
{
  protected function initialize($config)
  {
    $this->request_token_url = 'https://api.twitter.com/oauth/request_token';
    $this->request_auth_url = 'https://api.twitter.com/oauth/authorize';
    $this->access_token_url = 'https://api.twitter.com/oauth/access_token';
  }

  public function getContacts($uid)
  {
    $url = 'http://api.twitter.com/statuses/home_timeline.json';
    $this->params = array();
    $request = OAuthRequest::from_consumer_and_token($this->getConsumer(), $this->getToken(), 'GET', $url, $this->params);
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->getConsumer(), $this->getToken());

    $url = $request->to_url();

    return $this->call($url, null, 'GET');
  }
}