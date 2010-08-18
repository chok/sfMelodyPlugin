<?php
class sfVimeoMelody extends sfOAuth1
{
  protected function initialize($config)
  {
    $this->request_token_url = 'http://vimeo.com/oauth/request_token';
    $this->request_auth_url = 'http://vimeo.com/oauth/authorize';
    $this->access_token_url = 'http://vimeo.com/oauth/access_token';
  }

  public function getContacts($uid)
  {
    $url = 'http://vimeo.com/api/rest/v2/?method=vimeo.activity.userDid';
    $this->params = array('format' => 'json');
    $request = OAuthRequest::from_consumer_and_token($this->getConsumer(), $this->getToken(), 'GET', $url, $this->params);
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->getConsumer(), $this->getToken());

    $url = $request->to_url();

    return $this->call($url, null, 'GET');
  }
}